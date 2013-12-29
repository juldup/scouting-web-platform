<?php

class Section extends Eloquent {
  
  public static function getSectionsForSelect() {
    $sectionArray = array();
    $sections = self::orderBy('position')->get();
    foreach ($sections as $section) {
      $sectionArray[$section->id] = $section->name;
    }
    return $sectionArray;
  }
  
}
