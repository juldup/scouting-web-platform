<?php

class User extends Eloquent {
  
  // Whether the user is connected (by default, they are)
  var $isConnected = true;
  
  // Currently selected tab
  var $currentSection;
  
  // Members owned by this user (i.e. sharing same e-mail address)
  private $associatedMembers = null;
  
  // Members owned by this user that are leaders
  private $associatedLeaderMembers = null;
  
  // Whether this user is a leader (null = unknown)
  private $isLeader = null;
  
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
    if ($this->isLeader === null) {
      // If the user is not connected, they cannot be a leader
      if (!$this->isConnected) {
        return false;
      }
      // If the user has not verified their account, they cannot be a leader
      if (!$this->verified) {
        return false;
      }
      // Check if there is an associated leader
      $this->isLeader = count($this->getAssociatedLeaderMembers()) != 0;
    }
    return $this->isLeader;
  }
  
  // Fetches if need be and returns the list of associated members (i.e. sharing this user's e-mail address)
  public function getAssociatedMembers() {
    if ($this->associatedMembers === null) {
      // Find all members sharing an e-mail address with this user
      $this->associatedMembers = Member::where("email1", "=", $this->email)
              ->orWhere('email2', '=', $this->email)
              ->orWhere('email3', '=', $this->email)
              ->orWhere('email_member', '=', $this->email)->get();
    }
    return $this->associatedMembers;
  }
  
  // Filters the associated members to keep only leaders
  public function getAssociatedLeaderMembers() {
    if ($this->associatedLeaderMembers === null) {
      $this->associatedLeaderMembers = array();
      foreach ($this->getAssociatedMembers() as $member) {
        if ($member->is_leader) {
          $this->associatedLeaderMembers[] = $member;
        }
      }
    }
    return $this->associatedLeaderMembers;
  }
  
  public function can($action, $section = "") {
    // An unlogged user cannot do anything
    if (!$this->isConnected) {
      return false;
    }
    // An unverified user cannot do anything
    if (!$this->verified) {
      return false;
    }
    // The webmaster is almighty
    if ($this->is_webmaster) {
      return true;
    }
    
    // Find section id
    if ($section === "") {
      $sectionId = $this->currentSection->id;
    } else if (is_numeric($section)) {
      $sectionId = $section;
    } else {
      $sectionId = $section->id;
    }
    
    foreach ($this->getAssociatedLeaderMembers() as $leaderMember) {
      $privileges = Privilege::where('member_id', '=', $leaderMember->id)->where("operation", "=", $action['id'])->get();
      foreach ($privileges as $privilege) {
        if ($privilege->scope == 'U') {
          // Unit-wide privilege found, access granted
          return true;
        } else if ($privilege->scope == 'S') {
          // Section-wide privilege found
          if ($sectionId == $leaderMember->section_id) {
            // Sections match, access granted
            return true;
          }
        }
      }
    }
    
    // No associated leader or matching privilege
    return false;
  }
  
}
