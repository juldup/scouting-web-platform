<?php

class CalendarController extends BaseController {
  
  public function showPage() {
    $this->preExecute();
    return View::make('calendar');
  }
  
  public function showGestion() {
    return "Gestion de la page de calendrier";
  }
  
}
