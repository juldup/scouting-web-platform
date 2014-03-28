<?php

class HealthCardController extends BaseController {
  
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    
    $ownedMembers = $this->user->getAssociatedMembers();
    
    $members = array();
    $healthCardCount = 0;
    
    foreach ($ownedMembers as $member) {
      $members[$member->id] = array('member' => $member);
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $members[$member->id]['health_card'] = $healthCard;
        $healthCardCount++;
      }
    }
    
    return View::make('pages.healthCard.healthCard', array(
        'members' => $members,
        'download_all' => $healthCardCount >= 2,
        'can_manage' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS),
    ));
    
  }
  
  public function showEdit($member_id) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    
    if (!$this->user->isOwnerOfMember($member_id)) {
      return Helper::forbiddenResponse();
    }
    
    $healthCard = HealthCard::where('member_id', '=', $member_id)->first();
    
    if (!$healthCard) {
      $healthCard = new HealthCard();
      $healthCard->member_id = $member_id;
    }
    
    return View::make('pages.healthCard.healthCardForm', array(
        'health_card' => $healthCard,
    ));
  }
  
  public function submit() {
    
    $memberId = Input::get('member_id');
    
    if (!$this->user->isOwnerOfMember($memberId)) {
      return Helper::forbiddenResponse();
    }
    
    // Get all input
    $inputAll = Input::except('_token');
    // Complete missing booleans from checkboxes
    foreach (array('has_no_constrained_activities',
        'has_tetanus_vaccine',
        'has_allergy',
        'has_special_diet',
        'has_drugs',
        'drugs_autonomy') as $booleanKey) {
      if (!array_key_exists($booleanKey, $inputAll))
              $inputAll[$booleanKey] = false;
    }
    
    $errorMessage = "";
    $warningMessage = "";
    
    if ((!$inputAll['contact1_name'] || !$inputAll['contact1_phone']) &&
            (!$inputAll['contact2_name'] || !$inputAll['contact2_phone'])) {
      $errorMessage .= "Il faut spécifier au moins une personne de contact et son numéro de téléphone. ";
    }
    
    if (!$inputAll['has_no_constrained_activities'] && !$inputAll['constrained_activities_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les détails à la question 1. ";
    }
    if ($inputAll['has_no_constrained_activities'] && $inputAll['constrained_activities_details']) {
      $warningMessage .=
              "Vous avez répondu 'oui' à la question 1, mais avez tout de même complété des raisons. ";
    }
    
    if ($inputAll['has_tetanus_vaccine'] && !$inputAll['tetanus_vaccine_details']) {
      $errorMessage .= "Vous n'avez pas spécifié la date de vaccination à la question 4. ";
    }
    if (!$inputAll['has_tetanus_vaccine'] && $inputAll['tetanus_vaccine_details']) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 4, mais avez tout de même indiqué une date de vaccination. ";
    }
    
    if ($inputAll['has_allergy'] && !$inputAll['allergy_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les allergies à la question 5. ";
    }
    if (!$inputAll['has_allergy'] && ($inputAll['allergy_details'] || $inputAll['allergy_consequences'])) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 5, mais avez tout de même donné des informations. ";
    }
    
    if ($inputAll['has_special_diet'] && !$inputAll['special_diet_details']) {
      $errorMessage .= "Vous n'avez pas spécifié le régime à la question 6. ";
    }
    if (!$inputAll['has_special_diet'] && $inputAll['special_diet_details']) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 6, mais avez tout de même indiqué un régime. ";
    }
    
    if ($inputAll['has_drugs'] && !$inputAll['drugs_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les médicaments à la question 8. ";
    }
    if (!$inputAll['has_drugs'] && ($inputAll['drugs_details'])) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 8, mais avez tout de même donné des informations. ";
    }
    
    if (!$errorMessage) {
    
      $healthCard = HealthCard::where('member_id', '=', $memberId)->first();

      if ($healthCard) {
        // Updating the health card
        $healthCard->update($inputAll);
      } else {
        // Create health card
        $healthCard = HealthCard::create($inputAll);
      }

      // Save signatory data
      $healthCard->reminder_sent = false;
      $healthCard->signatory_id = $this->user->id;
      $healthCard->signatory_email = $this->user->email;
      $healthCard->signature_date = date('Y-m-d');

      $healthCard->save();
      
    }
    
    if ($errorMessage || $warningMessage) {
      $redirect = Redirect::to(URL::previous());
      if ($warningMessage) $redirect = $redirect->with('warning_message', "ATTENTION ! " . $warningMessage);
      if ($errorMessage) {
        return $redirect->with('error_message', $errorMessage)->withInput();
      } else {
        return $redirect->with('success_message', 'La fiche santé a été enregistrée.');
      }
    } else {
      // Success
      return Redirect::to(URL::route('health_card'))->with('success_message', 'La fiche santé a été enregistrée.');
    }
  }
  
  public function download($member_id) {
    
    $member = Member::find($member_id);
    
    if (!$member) App::abort(404, "Ce membre n'existe pas.");
    
    if (!$this->user->isOwnerOfMember($member_id) &&
            !$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $member->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    $healthCard = HealthCard::where('member_id', '=', $member_id)->first();
    
    HealthCardPDF::healthCardToPDF($healthCard);
    
  }
  
  public function downloadAll() {
    
    $members = $this->user->getAssociatedMembers();
    
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    
    HealthCardPDF::healthCardsToPDF($healthCards);
    
  }
  
  public function showManage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $sectionMembers = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    
    $members = array();
    $healthCardCount = 0;
    
    foreach ($sectionMembers as $member) {
      $members[$member->id] = array('member' => $member);
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $members[$member->id]['health_card'] = $healthCard;
        $healthCardCount++;
      }
    }
    
    return View::make('pages.healthCard.manageHealthCards', array(
        'members' => $members,
        'download_all' => $healthCardCount >= 2,
        'can_manage' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS),
    ));
    
  }
  
  public function downloadSectionCards() {
    
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $members = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    
    HealthCardPDF::healthCardsToPDF($healthCards);
    
  }
  
  public function downloadSectionSummary() {
    
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $members = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    
    HealthCardPDF::healthCardsToSummaryPDF($healthCards, $this->section);
    
  }
  
}
