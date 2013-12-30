<?php

class MenuComposer {
  
  public function compose($view) {
    
    $menuItems = array();
    
    $homeCategory = array();
    if (Parameter::get(Parameter::$SHOW_SECTIONS))
      $homeCategory["Unité et sections"] = URL::route('section');
    if (Parameter::get(Parameter::$SHOW_ADDRESSES))
      $homeCategory["Adresses utiles"] = URL::route('addresses');
    if (Parameter::get(Parameter::$SHOW_CONTACTS))
      $homeCategory["Contacts"] = URL::route('contacts');
    if (count($homeCategory))
      $menuItems['Accueil'] = $homeCategory;
    
    $generalCategory = array();
    if (Parameter::get(Parameter::$SHOW_ANNUAL_FEAST))
      $generalCategory["Fête d'unité"] = URL::route('annual_feast');
    if (Parameter::get(Parameter::$SHOW_CONTACTS))
      $generalCategory["Inscriptions"] = URL::route('registration');
    if (Parameter::get(Parameter::$SHOW_HEALTH_CARDS))
      $generalCategory["Fiches santé"] = URL::route('health_card');
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY))
      $generalCategory["Charte d'unité"] = URL::route('unit_policy');
    if (Parameter::get(Parameter::$SHOW_UNIFORMS))
      $generalCategory["Les uniformes"] = URL::route('uniform');
    if (Parameter::get(Parameter::$SHOW_LINKS))
      $generalCategory["Liens utiles"] = URL::route('links');
    if (count($generalCategory)) {
      $menuItems['Général'] = $generalCategory;
    }
    
    $animationCategory = array();
    if (Parameter::get(Parameter::$SHOW_NEWS))
      $animationCategory["Nouvelles"] = URL::route('news');
    if (Parameter::get(Parameter::$SHOW_CALENDAR))
      $animationCategory["Calendrier"] = URL::route('calendar');
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS))
      $animationCategory["Télécharger"] = URL::route('documents');
    if (Parameter::get(Parameter::$SHOW_EMAILS))
      $animationCategory["E-mails"] = URL::route('emails');
    if (Parameter::get(Parameter::$SHOW_PHOTOS))
      $animationCategory["Photos"] = URL::route('photos');
    if (Parameter::get(Parameter::$SHOW_LEADERS))
      $animationCategory["Les animateurs"] = URL::route('leaders');
    if (Parameter::get(Parameter::$SHOW_LISTING))
      $animationCategory["Listing des scouts"] = URL::route('listing');
    if (count($animationCategory)) {
      $menuItems['Animation'] = $animationCategory;
    }
    
    $opinionCategory = array();
    if (Parameter::get(Parameter::$SHOW_SUGGESTIONS))
      $opinionCategory["Suggestions"] = URL::route('suggestions');
    if (Parameter::get(Parameter::$SHOW_GUEST_BOOK))
      $opinionCategory["Livre d'or"] = URL::route('guest_book');
    if (count($opinionCategory)) {
      $menuItems['Votre avis'] = $opinionCategory;
    }
    
    $helpCategory = array();
    if (Parameter::get(Parameter::$SHOW_HELP))
      $helpCategory["Aide"] = URL::route('help');
    if (count($helpCategory)) {
      $menuItems['Aide'] = $helpCategory;
    }
    
    $view->withMenuItems($menuItems);
    
  }
  
}
