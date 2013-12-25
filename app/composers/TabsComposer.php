<?php

class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    $sections = Section::all();//orderBy('position');
    foreach ($sections as $section) {
      $tabs[] = array(
          "link" => "",
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    $view->withTabs($tabs);
    
    
  }
  
}
