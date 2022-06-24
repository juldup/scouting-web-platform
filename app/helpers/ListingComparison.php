<?php

class ListingComparison {
  
  protected $comparedListing;
  protected $caseSensitiveCompare;
  protected $ignoreAccentErrors;
  protected $fuzzyAddressComparison;
  
  /**
   * Returns a comparison between the desk listing of the current listing
   */
  function compareDeskListing($deskMembers, $caseSensitiveCompare = true, $ignoreAccentErrors = false, $fuzzyAddressComparison = true) {
    $this->comparedListing = array();
    $this->caseSensitiveCompare = $caseSensitiveCompare;
    $this->ignoreAccentErrors = $ignoreAccentErrors;
    $this->fuzzyAddressComparison = $fuzzyAddressComparison;
//    dd($ignoreAccentErrors);
    usort($deskMembers, array('ListingComparison', 'compareMembers'));
    $localMembers = Member::where('validated', '=', true)->where('is_guest', '=', false)->orderBy('last_name')->orderBy('first_name')->get();
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
        'section' => array('before' => '', 'after' => Section::find($member->section_id)->getSectionCode()),
        'handicap' => array('before' => '', 'after' => $member->has_handicap ? "Oui" : "Non"),
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
        'handicap' => array('before' => $member['handicap'] == "true" ? "Oui" : "Non", 'after' => ''),
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
    $comparedMember['phone'] = $this->comparePhones($localMember, $deskMember);
    // E-mail
    $comparedMember['email'] = $this->compareEmails($localMember, $deskMember);
    // Address
    $comparedMember['address'] = $this->compareAddresses($localMember, $deskMember);
    // Section
    $localSectionName = Section::find($localMember->section_id)->getSectionCode();
    if (Helper::endsWith($deskMember['section'], $localSectionName) || ($localSectionName == "U0" && $deskMember['section'] == "")) {
      $comparedMember['section'] = array('value' => $localSectionName);
    } else {
      $comparedMember['section'] = array('before' => $deskMember['section'], 'after' => $localSectionName);
    }
    // Handicap
    if ($deskMember['handicap'] == "true" && !$localMember->has_handicap) {
      $comparedMember['handicap'] = array('before' => 'Oui', 'after' => 'Non');
    } elseif ($deskMember['handicap'] == "false" && $localMember->has_handicap) {
      $comparedMember['handicap'] = array('before' => 'Non', 'after' => 'Oui');
    } else {
      $comparedMember['handicap'] = array('value' => ($localMember->has_handicap ? "Oui" : "Non"));
    }
    // Totem
    if ($this->stringsEqual(trim($localMember->totem), $deskMember['totem'])) {
      $comparedMember['totem'] = array('value' => $localMember->totem);
    } else {
      $comparedMember['totem'] = array('before' => $deskMember['totem'], 'after' => $localMember->totem);
    }
    // Quali
    if ($this->stringsEqual(trim($localMember->quali), $deskMember['quali'])) {
      $comparedMember['quali'] = array('value' => $localMember->quali);
    } else {
      $comparedMember['quali'] = array('before' => $deskMember['quali'], 'after' => $localMember->quali);
    }
    // Add to array
    $this->comparedListing[] = $comparedMember;
  }
  
  function stringsEqual($str1, $str2) {
    if ($this->ignoreAccentErrors) {
      $str1 = Helper::removeSpecialCharacters($str1);
      $str2 = Helper::removeSpecialCharacters($str2);
    }
    if ($this->caseSensitiveCompare) {
      return $str1 == $str2;
    } else {
      return strcasecmp($str1, $str2) == 0;
    }
  }
  
  function compareEmails($localMember, $deskMember) {
    if ($localMember->is_leader) {
      $localEmail = $localMember->email_member;
      if ($this->stringsEqual(trim($localEmail), $deskMember['email'])) {
        return array('value' => $localEmail);
      } else {
        return array('before' => $deskMember['email'], 'after' => $localEmail);
      }
    } else {
      $localEmails = $localMember->getParentsEmailAddresses();
      foreach ($localEmails as $localEmail) {
        if ($this->stringsEqual(trim($localEmail), $deskMember['email'])) {
          return array('value' => $localEmail);
        }
      }
      return array('before' => $deskMember['email'], 'after' => count($localEmails) ? $localEmails[0] : "");
    }
  }
  
  function comparePhones($localMember, $deskMember) {
    // List of Desk phones
    $deskPhones = [];
    if ($deskMember['phone1']) $deskPhones[] = Helper::formatPhoneNumber($deskMember['phone1']);
    if ($deskMember['phone2']) $deskPhones[] = Helper::formatPhoneNumber($deskMember['phone2']);
    // List of local phones
    if ($localMember->is_leader) {
      $localPhones = [];
      if ($localMember->phone_member) $localPhones[] = Helper::formatPhoneNumber($localMember->phone_member);
    } else {
      $localPhones = $localMember->getParentsPublicPhones();
      foreach ($localPhones as $index => $localPhone) {
        $localPhones[$index] = Helper::formatPhoneNumber($localPhone);
      }
      if ($localMember->phone_member) $localPhones[] = Helper::formatPhoneNumber($localMember->phone_member);
    }
    // List phone numbers in Desk but not in local listing
    $inDeskButNotInListing = "";
    $inBoth = "";
    $phoneCount = 0;
    foreach ($deskPhones as $deskPhone) {
      $inListing = false;
      foreach ($localPhones as $localPhone) {
        if ($this->stringsEqual($deskPhone, $localPhone)) {
          $inListing = true;
        }
      }
      if (!$inListing) {
        $inDeskButNotInListing .= ($inDeskButNotInListing ? ", " : "") . $deskPhone;
      } else {
        $inBoth .= ($inBoth ? ", " : "") . $deskPhone;
        $phoneCount++;
      }
    }
    // List phone numbers in local listing but not in Desk
    $inListingButNotInDesk = "";
    foreach ($localPhones as $localPhone) {
      $inDesk = false;
      foreach ($deskPhones as $deskPhone) {
        if ($this->stringsEqual($deskPhone, $localPhone)) {
          $inDesk = true;
        }
      }
      if (!$inDesk && $phoneCount < 2) {
        $inListingButNotInDesk .= ($inListingButNotInDesk ? ", " : "") . $localPhone;
        $phoneCount++;
      }
    }
    // Return
    if ($inDeskButNotInListing || $inListingButNotInDesk) {
      return array('before' => $inDeskButNotInListing, 'after' => $inListingButNotInDesk, 'keep' => $inBoth);
    } else {
      return array('value' => substr($inBoth, 0, 12) . '…', 'title' => $inBoth);
    }
  }
  
  public function compareAddresses($localMember, $deskMember) {
    if ($this->fuzzyAddressComparison && $deskMember['street'] && $localMember->address) {
      return $this->compareAddressesFuzzy($localMember, $deskMember);
    }
    if ($deskMember['street']) {
      $deskAddresses = [
        $deskMember['street'] . ", " . $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "") . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['street'] . " " . $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "")  . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['street'] . "," . $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "")  . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['street'] . ", " . $deskMember['number'] . ($deskMember['mailbox'] ? " bte" . $deskMember['mailbox'] : "")  . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['street'] . " " . $deskMember['number'] . ($deskMember['mailbox'] ? " bte" . $deskMember['mailbox'] : "") . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['street'] . "," . $deskMember['number'] . ($deskMember['mailbox'] ? " bte" . $deskMember['mailbox'] : "") . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "") . ", " . $deskMember['street'] . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "") . "," . $deskMember['street'] . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "") . " " . $deskMember['street'] . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
      ];
    } else {
      $deskAddresses = [""];
    }
    $localAddress = trim($localMember->address) . " ; " . trim($localMember->postcode) . " " . trim($localMember->city);
    foreach ($deskAddresses as $deskAddress) {
      if ($this->stringsEqual($deskAddress, $localAddress)) {
        return array('value' => mb_substr($localAddress, 0, 12) . '…', 'title' => $localAddress);
      }
    }
    return array('before' => $deskAddresses[0], 'after' => $localAddress);
  }
  
  public function compareAddressesFuzzy($localMember, $deskMember) {
    // Format Desk street
    $deskStreet = $deskMember['street'];
    $deskStreet = preg_replace('/^([^,]*).*$/', '$1', $deskStreet); // Remove everything after first comma
    $deskStreet = preg_replace("/\([^)]+\)/","", $deskStreet); // Remove parentheses and their content
    $deskStreet = Helper::slugifyNoException($deskStreet); // Slugify to remove special characters
    $deskStreet = preg_replace('/saint/', 'st', $deskStreet); // Replace "saint" with "st"
    $deskStreet = preg_replace('/avenue/', 'av', $deskStreet); // Replace "avenue" with "av"
    // Get Desk number
    $deskNumber = Helper::slugifyNoException(preg_replace('/\\ /', '', $deskMember['number'] . "/" . $deskMember['mailbox'])); // Remove spaces and to lower case (e.g. "57 A" => "57a")
    // Format local address
    $localAddress = Helper::slugifyNoException(trim($localMember->address));
    $localAddress = preg_replace('/saint/', 'st', $localAddress); // Replace "saint" with "st"
    $localAddress = preg_replace('/avenue/', 'av', $localAddress); // Replace "avenue" with "av"
    // Compare street, number and postcode
    $leven1 = levenshtein($deskStreet . "-" . $deskNumber, $localAddress);
    $leven2 = levenshtein($deskNumber . "-" . $deskStreet, $localAddress);
    $leven3 = levenshtein($deskStreet . $deskNumber, $localAddress);
    $leven4 = levenshtein($deskNumber . $deskStreet, $localAddress);
    $leven = min([$leven1, $leven2, $leven3, $leven4]);
    $streetMatches = $leven <= 3 || strpos($localAddress, $deskStreet) !== false;
    if ($deskNumber) {
      $numberMatches = strpos($localAddress, $deskNumber) !== false;
    } else {
      $numberMatches = true;
    }
    $postcodeMatches = $this->stringsEqual($deskMember['postcode'], trim($localMember->postcode));
    if ($streetMatches && $numberMatches && $postcodeMatches) {
      $localAddress = $localMember->address . " ; " . $localMember->postcode . " " . $localMember->city;
      return array('value' => mb_substr($localAddress, 0, 12) . '…', 'title' => $localAddress);
    }
    return array(
        'before' => $deskMember['street'] . ", " . $deskMember['number'] . ($deskMember['mailbox'] ? "/" . $deskMember['mailbox'] : "") . " ; " . $deskMember['postcode'] . " " . $deskMember['city'],
        'after' => $localMember->address . " ; " . $localMember->postcode . " " . $localMember->city
    );
  }
  
}
