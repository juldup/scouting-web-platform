<?php

/**
 * This composer generates the section selection menu
 */
class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    // Get current section
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    // Get current route and its parameters
    $currentRoute = Route::current();
    $currentRouteName = Route::currentRouteName();
    if (!$currentRouteName) $currentRouteName = 'home';
    $routeParameters = $currentRoute ? $currentRoute->parameters() : array();
    
    // Generate section items with transposed route parameters to match the section
    $sections = Section::orderBy('position')->get();
    foreach ($sections as $section) {
      $routeParameters['section_slug'] = $section->slug;
      $tabs[] = array(
          "link" => URL::route($currentRouteName, $routeParameters),
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    // Pass the section list to the view
    $view->withTabs($tabs);
    
    
  }
  
}
