<?php

class AccueilController extends BaseController {
  
	public function showPage() {
    $homePage = Page::where('section_id', '=', 1)->where('type', '=' ,'accueil')->get();
    //var_dump($homePage);
		return $homePage[0]->content;
	}
  
  public function showGestion() {
    return "Gestion de la page d'accueil";
  }
  
}
