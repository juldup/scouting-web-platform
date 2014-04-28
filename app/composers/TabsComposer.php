<?php

class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    $currentRoute = Route::current();
    $currentRouteName = Route::currentRouteName();
    if (!$currentRouteName) $currentRouteName = 'home';
    $routeParameters = $currentRoute ? $currentRoute->parameters() : array();
    
    $sections = Section::orderBy('position')->get();
    foreach ($sections as $section) {
      $routeParameters['section_slug'] = $section->slug;
      $tabs[] = array(
          "link" => URL::route($currentRouteName, $routeParameters),
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    $view->withTabs($tabs);
    
    
  }
  
}
