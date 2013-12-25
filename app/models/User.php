<?php

class User extends Eloquent {
  
  // Whether the user is connected (by default, they are)
  var $isConnected = true;
  
  // Currently selected tab
  var $currentSection;
  
  // Returns a dummy user that is not logged in
  public static function disconnectedUser() {
    $user = new User();
    $user->isConnected = false;
    return $user;
  }
  
  // Returns whether the user is logged in
  public function isConnected() {
    return $this->isConnected;
  }
  
  // Returns whether the user is a leader
  public function isLeader() {
    if (!$this->isConnected) return false;
    return true;
  }
  
  public function can($action, $section = "") {
    if ($section === "") $sectionId = $currentSection->id;
    else if (is_numeric($section)) $sectionId = $section;
    else $sectionId = $section->id;
    
    return $action == "Modifier les pages #delasection" && $sectionId = 1;
  }
  
}