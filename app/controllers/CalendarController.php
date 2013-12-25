<?php

class CalendarController extends BaseController {
  
  public function showPage($section_slug = '') {
    $this->preExecute($section_slug);
    return View::make('calendar');
  }
  
  public function showGestion() {
    return "Gestion de la page de calendrier";
  }
  
}
