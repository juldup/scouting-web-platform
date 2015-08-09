<?php

class ListingComparison {
  
  protected $comparedListing;
  protected $caseSensitiveCompare;
  
  /**
   * Returns a comparison between the desk listing of the current listing
   */
  function compareDeskListing($deskMembers, $caseSensitiveCompare = false) {
    $this->comparedListing = array();
    $this->caseSensitiveCompare = $caseSensitiveCompare;
    usort($deskMembers, array('ListingComparison', 'compareMembers'));
    $localMembers = Member::where('validated', '=', true)->orderBy('last_name')->orderBy('first_name')->get();
    $deskIndex = 0;
    $localIndex = 0;
    while ($deskIndex < count($deskMembers) || $localIndex < count($localMembers)) {
      if ($deskIndex >= count($deskMembers)) {
        // New member
        $this->addNewMember($localMembers[$localIndex]);
        $localIndex++;
      } elseif ($localIndex >= count($localMembers)) {
        // Deleted member
        $this->addDeletedMember($deskMembers[$deskIndex]);
        $deskIndex++;
      } else {
        $nextDeskMember = $deskMembers[$deskIndex];
        $nextLocalMember = $localMembers[$localIndex];
        $comp = self::compareMembers($nextDeskMember, $nextLocalMember);
//        echo $nextDeskMember['last_name'] . " " . $nextDeskMember['first_name'] . " + " . $nextLocalMember->last_name . " " . $nextLocalMember->first_name . " = " . $comp . "<br>";
        if ($comp < 0) {
          // Delete desk member
          $this->addDeletedMember($nextDeskMember);
          $deskIndex++;
        } elseif ($comp > 0) {
          // Local is new member
          $this->addNewMember($nextLocalMember);
          $localIndex++;
        } else {
          // Same member, compare
          $this->addComparedMember($nextLocalMember, $nextDeskMember);
          $deskIndex++;
          $localIndex++;
        }
      }
    }
    return $this->comparedListing;
  }
  
  static function compareMembers($member1, $member2) {
    $lastName1 = Helper::removeSpecialCharacters(trim(is_array($member1) ? $member1['last_name'] : $member1->last_name));
    $lastName2 = Helper::removeSpecialCharacters(trim(is_array($member2) ? $member2['last_name'] : $member2->last_name));
    $compLastName = strcasecmp($lastName1, $lastName2);
    if ($compLastName != 0) return $compLastName;
    $firstName1 = Helper::removeSpecialCharacters(trim(is_array($member1) ? $member1['first_name'] : $member1->first_name));
    $firstName2 = Helper::removeSpecialCharacters(trim(is_array($member2) ? $member2['first_name'] : $member2->first_name));
    return strcasecmp($firstName1, $firstName2);
  }
  
  function addNewMember($member) {
    $this->comparedListing[] = array(
        'last_name' => array('before' => '', 'after' => $member->last_name),
        'first_name' => array('before' => '', 'after' => $member->first_name),
        'gender' => array('before' => '', 'after' => $member->gender),
        'birth_date' => array('before' => '', 'after' => Helper::dateToHumanLeadingZeros($member->birth_date)),
        'phone' => array('before' => '', 'after' => $member->phone1),
        'email' => array('before' => '', 'after' => $member->email1),
        'address' => array('before' => '', 'after' => $member->address . " ; " . $member->postcode . " " . $member->city),
        'section' => array('before' => '', 'after' => $member->section),
        'handicap' => array('before' => '', 'after' => ''),
        'totem' => array('before' => '', 'after' => $member->totem),
        'quali' => array('before' => '', 'after' => $member->quali),
    );
  }
  
  function addDeletedMember($member) {
    $this->comparedListing[] = array(
        'last_name' => array('before' => $member['last_name'], 'after' => ''),
        'first_name' => array('before' => $member['first_name'], 'after' => ''),
        'gender' => array('before' => $member['gender'], 'after' => ''),
        'birth_date' => array('before' => Helper::dateToHumanLeadingZeros($member['birth_date']), 'after' => ''),
        'phone' => array('before' => $member['phone1'], 'after' => ''),
        'email' => array('before' => $member['email'], 'after' => ''),
        'address' => array('before' => '', 'after' => ''),
        'section' => array('before' => $member['section'], 'after' => ''),
        'handicap' => array('before' => '', 'after' => ''),
        'totem' => array('before' => $member['totem'], 'after' => ''),
        'quali' => array('before' => $member['quali'], 'after' => ''),
    );
  }
  
  function addComparedMember($localMember, $deskMember) {
    $comparedMember = array();
    // Last name
    if ($this->stringsEqual(trim($localMember->last_name), $deskMember['last_name'])) {
      $comparedMember['last_name'] = array('value' => $localMember->last_name);
    } else {
      $comparedMember['last_name'] = array('before' => $deskMember['last_name'], 'after' => $localMember->last_name);
    }
    // First name
    if ($this->stringsEqual(trim($localMember->first_name), $deskMember['first_name'])) {
      $comparedMember['first_name'] = array('value' => $localMember->first_name);
    } else {
      $comparedMember['first_name'] = array('before' => $deskMember['first_name'], 'after' => $localMember->first_name);
    }
    // Gender
    if ($this->stringsEqual(trim($localMember->gender), $deskMember['gender'])) {
      $comparedMember['gender'] = array('value' => $localMember->gender);
    } else {
      $comparedMember['gender'] = array('before' => $deskMember['gender'], 'after' => $localMember->gender);
    }
    // Birth date
    if ($this->stringsEqual($localMember->birth_date, $deskMember['birth_date'])) {
      $comparedMember['birth_date'] = array('value' => Helper::dateToHumanLeadingZeros($localMember->birth_date));
    } else {
      $comparedMember['birth_date'] = array('before' => Helper::dateToHumanLeadingZeros($deskMember['birth_date']), 'after' => Helper::dateToHumanLeadingZeros($localMember->birth_date));
    }
    // Phone
    $comparedMember['phone'] = array('value' => '');
    // E-mail
    $comparedMember['email'] = array('value' => '');
    // Address
    $comparedMember['address'] = array('value' => '');
    // Section
    $comparedMember['section'] = array('value' => '');
    // Handicap
    $comparedMember['handicap'] = array('value' => '');
    // Totem
    $comparedMember['totem'] = array('value' => '');
    // Quali
    $comparedMember['quali'] = array('value' => '');
    // Add to array
    $this->comparedListing[] = $comparedMember;
  }
  
  function stringsEqual($str1, $str2) {
    if ($this->caseSensitiveCompare) {
      return $str1 == $str2;
    } else {
      return strcasecmp($str1, $str2) == 0;
    }
  }
  
}
