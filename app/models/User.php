<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This Eloquent class represents a user (visitor) of the website
 * 
 * Columns:
 *   - password:          Encoded password of the user
 *   - username:          Username
 *   - email:             E-mail address of the user
 *   - default_section:   The default section is the section loaded by default when starting a new session
 *   - is_webmaster:      Whether this user is the webmaster of the website (the webmaster has all privileges on the website)
 *   - last_visit:        Date of the latest visit (excepted the current)
 *   - current_visit:     Date of the current visit
 *   - verification_code: A code sent by e-mail to the user to verify their e-mail address
 *   - verified:          Whether the e-mail address ownership has been verified with the verification code
 */
class User extends Eloquent {
  
  protected $fillable = array('username', 'password', 'email', 'default_section');
  
  // Whether the user is logged in (by default, they are)
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
  
  /**
   * Returns a dummy user that is not logged in
   */
  public static function disconnectedUser() {
    $user = new User();
    $user->isConnected = false;
    return $user;
  }
  
  /**
   * Returns the user with the given login and password (null if no match)
   */
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
  
  /**
   * Creates a new user with the given credentials
   */
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
  
  /**
   * Returns the name of the username cookie
   */
  public static function getCookieUsernameName() {
    return strtolower(Helper::slugify(Parameter::get(Parameter::$UNIT_SHORT_NAME))) . '_username';
  }
  
  /**
   * Returns the name of the password cookie
   */
  public static function getCookiePasswordName() {
    return strtolower(Helper::slugify(Parameter::get(Parameter::$UNIT_SHORT_NAME))) . '_password';
  }
  
  /**
   * Updates the e-mail of the user and unsets the verified field
   */
  public function changeEmail($email) {
    $this->email = $email;
    $this->verified = false;
    $this->verification_code = self::generateVerificationCode();
    $this->save();
  }
  
  /**
   * Updates the password of the user
   */
  public function changePassword($password) {
    $this->password = self::encodePassword($password);
    $this->save();
  }
  
  /**
   * Updates the default section of the user
   */
  public function changeDefaultSection($defaultSection) {
    $this->default_section = $defaultSection;
    $this->save();
  }
  
  /**
   * Generates a new random verification code
   */
  private static function generateVerificationCode() {
    return hash('sha256', rand()) . time();
  }
  
  /**
   * Returns the password encoded
   */
  public static function encodePassword($password) {
    // Generate random salt
    $salt = substr(sha1(uniqid(rand(), true)), 0, 11);
    // Hashed password is password hashed twice
    $hashedPassword = hash('sha256', $salt . hash('sha256', $salt . $password));
    // Prepend salt to hashed password
    return $salt . $hashedPassword;
  }
  
  /**
   * Returns the password encoded to save in a cookie (different from
   * the encoded password in the database)
   * 
   * @param type $password  The raw user password
   * @param type $hashedPassword  The encoded password as in the database (used for retro-compatibility)
   */
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
  
  /**
   * Tests whether a raw password (or cookie password) corresponds to an encoded password
   * 
   * @param type $rawPassword  The raw password entered by the user or the cookie password
   * @param type $hashedPassword  The encoded password from the database
   */
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
  
  /**
   *  Returns whether the user is logged in
   */
  public function isConnected() {
    return $this->isConnected;
  }
  
  /**
   * Returns whether the user owns the given member
   */
  public function isOwnerOfMember($memberId) {
    if (!$this->isConnected || !$this->verified) return false;
    if ($memberId instanceof Member) $memberId = $memberId->id;
    foreach ($this->getAssociatedMembers() as $member) {
      if ($member->id == $memberId) {
        return true;
      }
    }
    return false;
  }
  
  /**
   * Returns whether the user is logged in and is a member
   */
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
  
  /**
   * Returns whether the user is logged in and is a leader
   */
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
  
  /**
   * Returns whether the user is logged in and is a former member
   */
  public function isFormerLeader() {
    if (!$this->isConnected || !$this->verified) return false;
    return ArchivedLeader::where('email_member', '=', $this->email)->first() != null;
  }
  
  /**
   * Returns the default section of the user
   */
  public function getDefaultSection() {
    if ($this->default_section) {
      return Section::find($this->default_section);
    } else {
      return Section::find(1);
    }
  }
  
  /**
   * Fetches (if needed) and returns the list of associated members (i.e. sharing this user's e-mail address).
   * If the user is not logged in or unverified, there are no associated members
   */
  public function getAssociatedMembers() {
    if (!$this->isConnected || !$this->verified) return array();
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
  
  /**
   * Returns the list of associated members that happen to be leaders
   */
  public function getAssociatedLeaderMembers() {
    if (!$this->isConnected || !$this->verified) return array();
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
  
  /**
   * Return whether this user can do the given action (privilege) for the given section
   */
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
    // Convert action to operation
    $operation = $action;
    if (!is_string($operation)) $operation = $action['id'];
    // Search privileges for all leaders associated with this user
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
