<?php

class HealthCardController extends BaseController {
  
  public function showPage() {
    
    $ownedMembers = $this->user->getAssociatedMembers();
    
    $members = array();
    
    foreach ($ownedMembers as $member) {
      $members[$member->id] = array('member' => $member);
      $healthCard = HealthCard::where('member_id', '=', $member->id)->first();
      if ($healthCard) {
        $members[$member->id]['health_card'] = $healthCard;
      }
    }
    
    return View::make('pages.healthCard.healthCard', array(
        'members' => $members,
    ));
    
    
    
  }
  
  public function showEdit($member_id) {
    
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
    
    $healthCard = HealthCard::where('member_id', '=', $memberId)->first();
    
    if ($healthCard) {
      // Updating the health card
      $healthCard->update(Input::except("_token"));
    } else {
      // Create health card
      $healthCard = HealthCard::create(Input::except("_token"));
    }
    
    // Save signatory data
    $healthCard->reminder_sent = false;
    $healthCard->signatory_id = $this->user->id;
    $healthCard->signatory_email = $this->user->email;
    $healthCard->signature_date = date('Y-m-d');
    
    $healthCard->save();
    
    return Redirect::to(URL::previous());
  }
  
}
