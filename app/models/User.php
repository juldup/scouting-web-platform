<?php

class Member extends Eloquent {
  
  var $isConnected = true;
  
  public static function disconnectedMember() {
    $member = new Member();
    $member->isConnected = false;
    return $member;
  }
  
  public function isConnected() {
    return $this->isConnected;
  }
  
  public function isAnimator() {
    if (!$this->isConnected) return false;
    return true;
  }
  
}