<?php

class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    $currentRoute = Route::currentRouteName();
    $routeParameters = Route::current()->parameters();
    
    $sections = Section::all();//orderBy('position');
    foreach ($sections as $section) {
      $routeParameters['section_slug'] = $section->slug;
      $tabs[] = array(
          "link" => URL::route($currentRoute, $routeParameters),
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    $view->withTabs($tabs);
    
    
  }
  
}
