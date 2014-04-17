<?php

class User extends Eloquent {
  
  protected $fillable = array('username', 'password', 'email', 'default_section');
  
  // Whether the user is connected (by default, they are)
  var $isConnected = true;
  
  // Currently selected tab
  var $currentSection;
  
  // Members owned by this user (i.e. sharing same e-mail address)
  private $associatedMembers = null;
  
  // Members owned by this user that are leaders
  private $associatedLeaderMembers = null;
  
  // Whether this user is a member (null = unknown)
  private $isMember = null;
  
  // Whether this user is a leader (null = unknown)
  private $isLeader = null;
  
  // Returns a dummy user that is not logged in
  public static function disconnectedUser() {
    $user = new User();
    $user->isConnected = false;
    return $user;
  }
  
  // Returns the user with the given login and password
  public static function getWithUsernameAndPassword($username, $password) {
    // Get users corresponding to username (as username or e-mail address)
    $users = User::where(function($query) use ($username) {
      $query->where('username', '=', $username);
      $query->orWhere('email', '=', strtolower($username));
    })->get();
    // Check password for each of them
    foreach ($users as $user) {
      if (self::testPassword($password, $user->password)) {
        return $user;
      }
    }
    // None match
    return null;
  }
  
  public static function createWith($username, $email, $password) {
    $hashedPassword = User::encodePassword($password);
    $user = self::create(array(
        "username" => $username,
        "password" => $hashedPassword,
        "email" => $email
    ));
    $user->last_visit = time();
    $user->current_visit = time();
    $user->verification_code = self::generateVerificationCode();
    $user->save();
    return $user;
  }
  
  public static function getCookieUsernameName() {
    return strtolower(Parameter::get(Parameter::$UNIT_SHORT_NAME)) . '_username';
  }
  
  public static function getCookiePasswordName() {
    return strtolower(Parameter::get(Parameter::$UNIT_SHORT_NAME)) . '_password';
  }
  
  public function changeEmail($email) {
    $this->email = $email;
    $this->verified = false;
    $this->verification_code = self::generateVerificationCode();
    $this->save();
  }
  
  public function changePassword($password) {
    $this->password = self::encodePassword($password);
    $this->save();
  }
  
  public function changeDefaultSection($defaultSection) {
    $this->default_section = $defaultSection;
    $this->save();
  }
  
  private static function generateVerificationCode() {
    return hash('sha256', rand()) . time(); // TODO Change to base64 for shorter validation link
  }

  public static function encodePassword($password) {
    // Generate random salt
    $salt = substr(sha1(uniqid(rand(), true)), 0, 11);
    // Hashed password is password hashed twice
    $hashedPassword = hash('sha256', $salt . hash('sha256', $salt . $password));
    // Prepend salt to hashed password
    return $salt . $hashedPassword;
  }
  
  public static function getCookiePassword($password, $hashedPassword) {
    // Retrieve salt from hash
    $salt = substr($hashedPassword, 0, 11);
    // Cookie password is password hashed once
    $cookiePassword = hash('sha256', $salt . $password);
    // Make sure cookie converts to hashed password, for backward compatibility
    if ($salt . hash('sha256', $salt . $cookiePassword) == $hashedPassword) {
      return $cookiePassword;
    }
    // This password is an old password, encode cookie with md5 instead
    $cookiePassword = md5($salt . $password);
    return $cookiePassword;
  }
  
  public static function testPassword($rawPassword, $hashedPassword) {
    // 11 first characters in hash is salt
    $salt = substr($hashedPassword, 0, 11);
    // Remove first 11 characters from hash
    $hashedPassword = substr($hashedPassword, 11);
    // Check if passwords match
    return // Raw password is passed twice in hash function
           hash('sha256', $salt . hash('sha256', $salt . $rawPassword)) == $hashedPassword || 
           // Cookie password is passed once in hash function
           hash('sha256', $salt . $rawPassword) == $hashedPassword ||
           // For backward compatibility
           md5($salt . md5($salt . $rawPassword)) == $hashedPassword ||
           md5($salt . $rawPassword) == $hashedPassword ||
           md5($salt . hash('sha256', $salt . $rawPassword)) == $hashedPassword;
  }
  
  // Returns whether the user is logged in
  public function isConnected() {
    return $this->isConnected;
  }
  
  // Returns whether the user owns the given member
  public function isOwnerOfMember($memberId) {
    if ($memberId instanceof Member) $memberId = $memberId->id;
    foreach ($this->getAssociatedMembers() as $member) {
      if ($member->id == $memberId) {
        return true;
      }
    }
    return false;
  }
  
  // Returns whether the user is logged in and is a member
  public function isMember() {
    if ($this->isMember === null) {
      // If the user is not connected, they cannot be a member
      if (!$this->isConnected) {
        return false;
      }
      // If the user has not verified their account, they cannot be a member
      if (!$this->verified) {
        return false;
      }
      // Check if there is an associated member
      $this->isMember = count($this->getAssociatedMembers()) != 0;
      // If the user is webmaster, they are a member
      if ($this->is_webmaster) $this->isMember = true;
    }
    return $this->isMember;
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
      // If the user is webmaster, they are considered a leader
      if ($this->is_webmaster) $this->isLeader = true;
    }
    return $this->isLeader;
  }
  
  public function getDefaultSection() {
    if ($this->default_section) {
      return Section::find($this->default_section);
    } else {
      return Section::find(1);
    }
  }
  
  // Fetches if need be and returns the list of associated members (i.e. sharing this user's e-mail address)
  public function getAssociatedMembers() {
    if ($this->associatedMembers === null) {
      // Find all members sharing an e-mail address with this use
      $email = $this->email;
      if ($email) {
        $this->associatedMembers = Member::where(function($query) use ($email) {
          $query->where('email1', '=', $email);
          $query->orWhere('email2', '=', $email);
          $query->orWhere('email3', '=', $email);
          $query->orWhere(function($query) use ($email) {
            $query->where('email_member', '=', $email);
            $query->where('is_leader', '=', true);
          });
        })->where('validated', '=', true)
                ->get();
      } else {
        $this->associatedMembers = array();
      }
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
    if (!$section) {
      $sectionId = $this->currentSection->id;
    } else if (is_numeric($section)) {
      $sectionId = $section;
    } else {
      $sectionId = $section->id;
    }
    
    $operation = $action;
    if (!is_string($operation)) $operation = $action['id'];
    
    foreach ($this->getAssociatedLeaderMembers() as $leaderMember) {
      $privileges = Privilege::where('member_id', '=', $leaderMember->id)->where("operation", "=", $operation)->get();
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
