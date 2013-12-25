<?php

class HomeController extends BaseController {
  
  public function showPage() {
    $this->preExecute();
    $homePage = Page::where('section_id', '=', 1)->where('type', '=' ,'accueil')->get();
    return View::make('home')->with('page_content', $homePage[0]->content);
  }
  
  public function showGestion() {
    return "Gestion de la page d'accueil";
  }
  
}
