<?php

class User extends Eloquent {
  
  var $isConnected = true;
  var $currentSection;
  
  public static function disconnectedUser() {
    $user = new User();
    $user->isConnected = false;
    return $user;
  }
  
  public function isConnected() {
    return $this->isConnected;
  }
  
  public function isAnimator() {
    if (!$this->isConnected) return false;
    return true;
  }
  
}