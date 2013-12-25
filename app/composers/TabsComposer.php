<?php

class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    $currentRoute = Route::currentRouteName();
    
    $sections = Section::all();//orderBy('position');
    foreach ($sections as $section) {
      $tabs[] = array(
          "link" => URL::route($currentRoute, array('section_slug' => $section->slug)),
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    $view->withTabs($tabs);
    
    
  }
  
}
