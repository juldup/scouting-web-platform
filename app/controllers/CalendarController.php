<?php

class CalendarController extends BaseController {
  
  public function showPage($section_slug = '') {
    return View::make('pages.calendar');
  }
  
  public function showGestion() {
    return "Gestion de la page de calendrier";
  }
  
}
