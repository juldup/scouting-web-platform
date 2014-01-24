<?php

class Section extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public static function getSectionsForSelect($discard = array()) {
    $discardIds = array();
    foreach ($discard as $section) {
      if (is_numeric($section)) {
        $discardIds[] = $section;
      } else {
        $discardIds[] = $section->id;
      }
    }
    
    $sectionArray = array();
    if (count($discardIds)) {
      $sections = self::whereNotIn('id', $discardIds)
              ->orderBy('position')->get();
    } else {
      $sections = self::orderBy('position')->get();
    }
    foreach ($sections as $section) {
      $sectionArray[$section->id] = $section->name;
    }
    return $sectionArray;
  }
  
  // Returns the generic name of the scouts in this section ('baladins', 'louveteaux', 'éclaireurs', etc.)
  public function getScoutName() {
    if ($this->section_type == 'B') return "baladins";
    if ($this->section_type == 'L') return "louveteaux";
    if ($this->section_type == 'E') return "éclaireurs";
    if ($this->section_type == 'P') return "pionniers";
    return "scouts";
  }
  
}
