<?php

class HealthCardController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    
    $healthCard = new HealthCard();
    $healthCard->member_id = 1;
    $healthCard->signature_date = "2013-12-02";
    
    return View::make('pages.healthCard.healthCardForm', array(
        'health_card' => $healthCard,
    ));
  }
  
  public function showEdit() {
  }
  
  public function submit($section_slug) {
  }
  
}
