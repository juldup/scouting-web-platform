<?php

class HealthCardController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    
    $healthCard = new HealthCard();
    $healthCard->member_id = 2;
    $healthCard->signature_date = "0000-00-00";
    
    return View::make('pages.healthCard.healthCardForm', array(
        'health_card' => $healthCard,
    ));
  }
  
  public function showEdit() {
  }
  
  public function submit($section_slug) {
  }
  
}
