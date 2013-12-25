<?php

class MenuComposer {
  
  public function compose($view) {
    
    $menuItems = array();
    
    $homeCategory = array();
    $homeCategory["Unité et sections"] = URL::route('sections');
    $homeCategory["Adresses utiles"] = URL::route('addresses');
    $homeCategory["Contacts"] = URL::route('contacts');
    if (count($homeCategory)) {
      $menuItems['Accueil'] = $homeCategory;
    }
    
    $generalCategory = array();
    $generalCategory["Fête d'unité"] = URL::route('home');
    $generalCategory["Inscriptions"] = URL::route('home');
    $generalCategory["Fiches santé"] = URL::route('home');
    $generalCategory["Charte d'unité"] = URL::route('home');
    $generalCategory["Les uniformes"] = URL::route('home');
    $generalCategory["Liens utiles"] = URL::route('home');
    if (count($generalCategory)) {
      $menuItems['Général'] = $generalCategory;
    }
    
    $animationCategory = array();
    $animationCategory["Nouvelles"] = URL::route('home');
    $animationCategory["Calendrier"] = URL::route('calendar');
    $animationCategory["Télécharger"] = URL::route('home');
    $animationCategory["E-mails"] = URL::route('home');
    $animationCategory["Photos"] = URL::route('home');
    $animationCategory["Les animateurs"] = URL::route('home');
    $animationCategory["Listing des scouts"] = URL::route('home');
    $animationCategory["Covoiturage"] = URL::route('home');
    if (count($animationCategory)) {
      $menuItems['Animation'] = $animationCategory;
    }
    
    $opinionCategory = array();
    $opinionCategory["Suggestions"] = URL::route('home');
    $opinionCategory["Livre d'or"] = URL::route('home');
    if (count($opinionCategory)) {
      $menuItems['Votre avis'] = $opinionCategory;
    }
    
    $helpCategory = array();
    $helpCategory["Aide"] = URL::route('home');
    if (count($helpCategory)) {
      $menuItems['Aide'] = $helpCategory;
    }
    
    $view->withMenuItems($menuItems);
    
  }
  
}
