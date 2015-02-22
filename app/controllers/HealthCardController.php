<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * Parents can fill in their children's health cards online. This controller
 * provides the tools to view, edit and manage the health cards.
 */
class HealthCardController extends BaseController {
  
  protected function currentPageAdaptToSections() {
    return $this->user->isLeader();
  }
  
  /**
   * [Route] Shows the health card page
   * @return type
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    // Get list of members owned by the current user
    $ownedMembers = $this->user->getAssociatedMembers();
    // Gather owned members and existing health cards
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
    // Make view
    return View::make('pages.healthCard.healthCard', array(
        'members' => $members,
        'download_all' => $healthCardCount >= 2,
        'can_manage' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS),
    ));
  }
  
  /**
   * [Route] Shows the page to edit a single health card
   */
  public function showEdit($member_id) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    // Make sure the member belongs to the current user
    if (!$this->user->isOwnerOfMember($member_id)) {
      return Helper::forbiddenResponse();
    }
    // Get the health card of the member
    $healthCard = HealthCard::where('member_id', '=', $member_id)->first();
    // Create a new health card if it does not exist
    if (!$healthCard) {
      $healthCard = new HealthCard();
      $healthCard->member_id = $member_id;
    }
    // Make view
    return View::make('pages.healthCard.healthCardForm', array(
        'health_card' => $healthCard,
    ));
  }
  
  /**
   * [Route] Submits the changes made to a health card
   */
  public function submit() {
    // Get the member id
    $memberId = Input::get('member_id');
    // Make sure the current user owns this member
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
    // Error and warning messages
    $errorMessage = "";
    $warningMessage = "";
    // Check that there is at least one contact's information
    if ((!$inputAll['contact1_name'] || !$inputAll['contact1_phone']) &&
            (!$inputAll['contact2_name'] || !$inputAll['contact2_phone'])) {
      $errorMessage .= "Il faut spécifier au moins une personne de contact et son numéro de téléphone. ";
    }
    // Make sure that question 1 is correctly answered
    if (!$inputAll['has_no_constrained_activities'] && !$inputAll['constrained_activities_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les détails à la question 1. ";
    }
    if ($inputAll['has_no_constrained_activities'] && $inputAll['constrained_activities_details']) {
      $warningMessage .=
              "Vous avez répondu 'oui' à la question 1, mais avez tout de même complété des raisons. ";
    }
    // Make sure that question 4 is correctly answered
    if ($inputAll['has_tetanus_vaccine'] && !$inputAll['tetanus_vaccine_details']) {
      $errorMessage .= "Vous n'avez pas spécifié la date de vaccination à la question 4. ";
    }
    if (!$inputAll['has_tetanus_vaccine'] && $inputAll['tetanus_vaccine_details']) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 4, mais avez tout de même indiqué une date de vaccination. ";
    }
    // Make sure that question 5 is correctly answered
    if ($inputAll['has_allergy'] && !$inputAll['allergy_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les allergies à la question 5. ";
    }
    if (!$inputAll['has_allergy'] && ($inputAll['allergy_details'] || $inputAll['allergy_consequences'])) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 5, mais avez tout de même donné des informations. ";
    }
    // Make sure that question 6 is correctly answered
    if ($inputAll['has_special_diet'] && !$inputAll['special_diet_details']) {
      $errorMessage .= "Vous n'avez pas spécifié le régime à la question 6. ";
    }
    if (!$inputAll['has_special_diet'] && $inputAll['special_diet_details']) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 6, mais avez tout de même indiqué un régime. ";
    }
    // Make sure that question 8 is correctly answered
    if ($inputAll['has_drugs'] && !$inputAll['drugs_details']) {
      $errorMessage .= "Vous n'avez pas spécifié les médicaments à la question 8. ";
    }
    if (!$inputAll['has_drugs'] && ($inputAll['drugs_details'])) {
      $warningMessage .=
              "Vous avez répondu 'non' à la question 8, mais avez tout de même donné des informations. ";
    }
    // Save the card in there is no error
    if (!$errorMessage) {
      // Get health card
      $healthCard = HealthCard::where('member_id', '=', $memberId)->first();
      $newHealthCard = false;
      if ($healthCard) {
        // Update the exsting health card
        $healthCard->update($inputAll);
      } else {
        // Create health card
        $healthCard = HealthCard::create($inputAll);
        $newHealthCard = true;
      }
      // Save signatory data
      $healthCard->reminder_sent = false;
      $healthCard->signatory_id = $this->user->id;
      $healthCard->signatory_email = $this->user->email;
      $healthCard->signature_date = date('Y-m-d');
      // Save the health card
      $healthCard->save();
    }
    // Log
    if (!$errorMessage) {
      $member = Member::find($memberId);
      LogEntry::log("Fiche santé", $newHealthCard ? "Création d'une fiche santé" : "Mise à jour d'une fiche santé",
              array("Membre" => $member->getFullName()));
    }
    // Redirect with status message
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
  
  /**
   * [Route] Outputs a health card in PDF format for download
   */
  public function download($member_id) {
    // Get the member
    $member = Member::find($member_id);
    if (!$member) App::abort(404, "Ce membre n'existe pas.");
    // Make sure the current leader can view this health card
    if (!$this->user->isOwnerOfMember($member_id) &&
            !$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $member->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Get health card
    $healthCard = HealthCard::where('member_id', '=', $member_id)->first();
    // Log
    LogEntry::log("Fiche santé", "Téléchargement d'une fiche santé", array("Membre" => $member->getFullName()));
    // Output the health card in PDF format
    HealthCardPDF::healthCardToPDF($healthCard);
  }
  
  /**
   * [Route] Download all the health card of the current user in PDF format
   */
  public function downloadAll() {
    // Get the members owned by the current user
    $members = $this->user->getAssociatedMembers();
    // Get the health cards of these members
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    // Output these health cards in PDF format
    HealthCardPDF::healthCardsToPDF($healthCards);
  }
  
  /**
   * [Route] Shows the health card management page for leaders
   */
  public function showManage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_HEALTH_CARDS)) {
      return App::abort(404);
    }
    // Make sure the user has access to this section's health cards
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Get the members of the section
    $sectionMembers = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    // Gather members and health cards
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
    // Make view
    return View::make('pages.healthCard.manageHealthCards', array(
        'members' => $members,
        'download_all' => $healthCardCount >= 2,
        'can_manage' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS),
    ));
  }
  
  /**
   * [Route] Outputs all the health cards of the current section in PDF format for download
   */
  public function downloadSectionCards() {
    // Make sure the user has access to the health cards of this section
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Get members
    $members = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    // Gather health cards
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    // Output health card in PDF format
    HealthCardPDF::healthCardsToPDF($healthCards);
  }
  
  /**
   * [Route] Outputs a summary of the current section's health cards in PDF format for download
   */
  public function downloadSectionSummary() {
    // Make sure the user has access to this section's health cards
    if (!$this->user->can(Privilege::$VIEW_HEALTH_CARDS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Get members
    $members = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    // Gather health cards
    $healthCards = array();
    foreach ($members as $member) {
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $healthCards[] = $healthCard;
      }
    }
    // Output health card summary in PDF format
    HealthCardPDF::healthCardsToSummaryPDF($healthCards, $this->section);
  }
  
}
