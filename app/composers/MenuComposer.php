<?php

class MenuComposer {
  
  public function compose($view) {
    
    $menuItems = array();
    
    $currentRouteAction = Route::current()->getAction();
    $currentRouteName = $currentRouteAction['as'];
    
    $homeCategory = array();
    if (Parameter::get(Parameter::$SHOW_SECTIONS))
      $homeCategory["Unité et sections"] = 'section';
    if (Parameter::get(Parameter::$SHOW_ADDRESSES))
      $homeCategory["Adresses utiles"] = 'addresses';
    if (Parameter::get(Parameter::$SHOW_CONTACTS))
      $homeCategory["Contacts"] = 'contacts';
    if (count($homeCategory))
      $menuItems['Accueil'] = $homeCategory;
    
    $generalCategory = array();
    if (Parameter::get(Parameter::$SHOW_ANNUAL_FEAST))
      $generalCategory["Fête d'unité"] = 'annual_feast';
    if (Parameter::get(Parameter::$SHOW_REGISTRATION))
      $generalCategory["Inscriptions"] = 'registration';
    if (Parameter::get(Parameter::$SHOW_HEALTH_CARDS))
      $generalCategory["Fiches santé"] = 'health_card';
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY))
      $generalCategory["Charte d'unité"] = 'unit_policy';
    if (Parameter::get(Parameter::$SHOW_UNIFORMS))
      $generalCategory["Les uniformes"] = 'uniform';
    if (Parameter::get(Parameter::$SHOW_LINKS))
      $generalCategory["Liens utiles"] = 'links';
    if (count($generalCategory)) {
      $menuItems['Général'] = $generalCategory;
    }
    
    $animationCategory = array();
    if (Parameter::get(Parameter::$SHOW_NEWS))
      $animationCategory["Nouvelles"] = 'news';
    if (Parameter::get(Parameter::$SHOW_CALENDAR))
      $animationCategory["Calendrier"] = 'calendar';
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS))
      $animationCategory["Télécharger"] = 'documents';
    if (Parameter::get(Parameter::$SHOW_EMAILS))
      $animationCategory["E-mails"] = 'emails';
    if (Parameter::get(Parameter::$SHOW_PHOTOS))
      $animationCategory["Photos"] = 'photos';
    if (Parameter::get(Parameter::$SHOW_LEADERS))
      $animationCategory["Les animateurs"] = 'leaders';
    if (Parameter::get(Parameter::$SHOW_LISTING))
      $animationCategory["Listing des scouts"] = 'listing';
    $animationCategory["divider_1"] = 'divider';
    $animationCategory["Nouveau sur le site"] = 'view_recent_changes';
    if (count($animationCategory)) {
      $menuItems['Animation'] = $animationCategory;
    }
    
    $opinionCategory = array();
    if (Parameter::get(Parameter::$SHOW_SUGGESTIONS))
      $opinionCategory["Suggestions"] = 'suggestions';
    if (Parameter::get(Parameter::$SHOW_GUEST_BOOK))
      $opinionCategory["Livre d'or"] = 'guest_book';
    if (count($opinionCategory)) {
      $menuItems['Votre avis'] = $opinionCategory;
    }
    
    $helpCategory = array();
    if (Parameter::get(Parameter::$SHOW_HELP))
      $helpCategory["Aide"] = 'help';
    if (count($helpCategory)) {
      $menuItems['Aide'] = $helpCategory;
    }
    
    $user = View::shared('user');
    if ($user->isLeader()) {
      $leaderCategory = array();
      $leaderCategory["Coin des animateurs"] = 'leader_corner';
      $leaderCategory["Aide sur la gestion du site"] = 'leader_help';
      
      $leaderCategory["Opérations courantes"] = 'title';
      $leaderCategory['Gérer le calendrier'] = 'manage_calendar';
      $leaderCategory['Gérer les photos'] = 'edit_photos';
      $leaderCategory['Gérer les documents'] = 'manage_documents';
      $leaderCategory['Gérer les nouvelles'] = 'manage_news';
      $leaderCategory['Gérer les e-mails'] = 'manage_emails';
      $leaderCategory['Envoyer un e-mail'] = 'send_section_email';
      $leaderCategory['Trésorerie'] = 'accounts';
      
      $leaderCategory['Opérations annuelles'] = 'title';
      $leaderCategory['Gérer les inscriptions'] = 'manage_registration';
      $leaderCategory['Gérer le listing'] = 'manage_listing';
      $leaderCategory['Gérer les animateurs'] = 'edit_leaders';
      $leaderCategory['Privilèges des animateurs'] = 'edit_privileges';
      $leaderCategory['Gérer les sections'] = 'section_data';
      
      $leaderCategory['Contenu du site'] = 'title';
      $leaderCategory["Paramètres du site"] = 'edit_parameters';
      
      $leaderCategory['Supervision'] = 'title';
      $leaderCategory['Changements récents'] = 'view_private_recent_changes';
      $leaderCategory['Liste des utilisateurs du site'] = 'user_list';
      
      if (count($leaderCategory)) {
        $menuItems['Coin des animateurs'] = $leaderCategory;
      }
    }
    
    $menuArray = array();
    foreach ($menuItems as $submenuName => $submenu) {
      $items = array();
      $submenuActive = false;
      foreach ($submenu as $itemName => $itemData) {
        $route = $itemData != 'divider' && $itemData != 'title' ? $itemData : null;
        $active = $route == $currentRouteName;
        if ($active) $submenuActive = true;
        $items[$itemName] = array(
            'url' => $route ? URL::route($route) : null,
            'active' => $active,
            'is_divider' => $itemData == "divider",
            'is_title' => $itemData == 'title',
        );
      }
      $menuArray[$submenuName] = array(
          'items' => $items,
          'active' => $submenuActive,
      );
    }
    
    $view->withMenuItems($menuArray);
    
  }
  
}
