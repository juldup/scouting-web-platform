<?php

class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $PICTURE_FOLDER_PATH = "site_data/leader_pictures/";
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getHumanBirthDate() {
    return date('d/m/Y', strtotime($this->birth_date));
  }
  
  public static function existWithEmail($email) {
    if (!$email) return false;
    $aMember = Member::where(function($query) use ($email) {
      $query->where('email1', '=', $email);
      $query->orWhere('email2', '=', $email);
      $query->orWhere('email3', '=', $email);
      $query->orWhere('email_member', '=', $email);
    })->where('validated', '=', true)
            ->first();
    if ($aMember) return true;
    else return false;
  }
  
  public function isReregistered() {
    return $this->last_reregistration == date('Y') . "-" . (date('Y') + 1);
  }
  
  public function getPictureURL() {
    return URL::route('get_leader_picture', array('leader_id' => $this->id));
  }
  
  public function getPicturePath() {
    return $this->getPicturePathFolder() . $this->getPicturePathFilename();
  }
  
  public function getPicturePathFolder() {
    return storage_path(self::$PICTURE_FOLDER_PATH);
  }
  
  public function getPicturePathFilename() {
    return $this->id . ".picture";
  }
  
  public static function getFamilyOtherUnitsForSelect() {
    return array('0' => "Aucun", '1' => "1", '2' => '2 ou plus');
  }
  
  public function getPersonalPhone() {
    if ($this->phone_member) return $this->phone_member;
    if ($this->phone1) return $this->phone1;
    if ($this->phone2) return $this->phone2;
    if ($this->phone3) return $this->phone3;
    return "";
  }
  
  public function getPublicPhone() {
    if ($this->phone1 && !$this->phone1_private) return $this->phone1;
    if ($this->phone2 && !$this->phone2_private) return $this->phone2;
    if ($this->phone3 && !$this->phone3_private) return $this->phone3;
    if ($this->phone_member && !$this->phone_member_private) return $this->phone_member;
    return "";
  }
  
  public function getAllPublicPhones($separator = " - ", $showAlsoPrivate = false) {
    $phones = "";
    if ($this->phone1 && ($showAlsoPrivate || !$this->phone1_private))
      $phones .= $this->phone1 . ($this->phone1_owner ? " (" . $this->phone1_owner . ")" : "");
    if ($this->phone2 && ($showAlsoPrivate || !$this->phone2_private))
      $phones .= ($phones ? $separator : "") . $this->phone2 . ($this->phone2_owner ? " (" . $this->phone2_owner . ")" : "");
    if ($this->phone3 && ($showAlsoPrivate || !$this->phone3_private))
      $phones .= ($phones ? $separator : "") . $this->phone3 . ($this->phone3_owner ? " (" . $this->phone3_owner . ")" : "");
    if ($this->phone_member && ($showAlsoPrivate || !$this->phone_member_private))
      $phones .= ($phones ? $separator : "") . $this->phone_member . " (personnel)";
    return $phones;
  }
  
  public function hasParentsEmailAddress() {
    return $this->email1 || $this->email2 || $this->email3 ? true : false;
  }
  
  public function getParentsEmailAddresses() {
    $emails = array();
    if ($this->email1) $emails[] = $this->email1;
    if ($this->email2) $emails[] = $this->email2;
    if ($this->email3) $emails[] = $this->email3;
    return $emails;
  }
  
  public function getAllEmailAddresses($separator = " - ", $includePersonal = true) {
    $emails = "";
    if ($this->email1)
      $emails .= Helper::sanitizeForHTML($this->email1);
    if ($this->email2)
      $emails .= ($emails ? $separator : "") . Helper::sanitizeForHTML($this->email2);
    if ($this->email3)
      $emails .= ($emails ? $separator : "") . Helper::sanitizeForHTML($this->email3);
    if ($this->email_member && $includePersonal)
      $emails .= ($emails ? $separator : "") . Helper::sanitizeForHTML($this->email_member) . " (personnel)";
    return $emails;
  }
  
  // If input data is correct, updates this and returns true.
  // If input data is incorrect, returns false or an error message.
  public function updateFromInput($canEditIdentity, $canEditContact, $canEditSection, $canEditTotem, $canEditLeader) {
    $data = self::checkInputData($canEditIdentity, $canEditContact, $canEditSection, $canEditTotem, $canEditLeader);
    if (is_string($data)) {
      // An error has occured
      return $data;
    }
    
    if ($canEditIdentity) {
      $this->first_name = $data['first_name'];
      $this->last_name = $data['last_name'];
      $this->birth_date = $data['birth_date'];
      $this->gender = $data['gender'];
      $this->nationality = $data['nationality'];
      $this->has_handicap = $data['has_handicap'];
      $this->handicap_details = $data['handicap_details'];
    }
    
    if ($canEditContact) {
      $this->address = $data['address'];
      $this->postcode = $data['postcode'];
      $this->city = $data['city'];
      $this->phone1 = $data['phone1'];
      $this->phone1_owner = $data['phone1_owner'];
      $this->phone1_private = $data['phone1_private'];
      $this->phone2 = $data['phone2'];
      $this->phone2_owner = $data['phone2_owner'];
      $this->phone2_private = $data['phone2_private'];
      $this->phone3 = $data['phone3'];
      $this->phone3_owner = $data['phone3_owner'];
      $this->phone3_private = $data['phone3_private'];
      $this->phone_member = $data['phone_member'];
      $this->phone_member_private = $data['phone_member_private'];
      $this->email1 = $data['email1'];
      $this->email2 = $data['email2'];
      $this->email3 = $data['email3'];
      $this->email_member = $data['email_member'];
    }
    
    if ($canEditSection) {
        $this->section_id = $data['section_id'];
    }
    
    if ($canEditTotem) {
      $this->totem = $data['totem'];
      $this->quali = $data['quali'];
      $this->subgroup = $data['subgroup'];
    }
    
    if ($canEditLeader) {
      $this->is_leader = $data['is_leader'];
      $this->leader_name = $data['leader_name'];
      $this->leader_in_charge = $data['leader_in_charge'];
      $this->leader_description = $data['leader_description'];
      $this->leader_role = $data['leader_role'];
    }
    
    $this->comments = $data['comments'];
    $this->family_in_other_units = $data['family_in_other_units'];
    $this->family_in_other_units_details = $data['family_in_other_units_details'];
    $this->validated = true;
    
    try {
      $this->save();
      if ($this->is_leader) return $this->uploadPictureFromInput();
      else return true;
    } catch (Exception $ex) {
      return false;
    }
  }
  
  // If input data is correct, creates and returns a new user.
  // If input data is incorrect, returns false or an error message.
  public static function createFromInput($validate = false) {
    
    $data = self::checkInputData();
    if (is_string($data)) {
      // An error has occured
      return $data;
    }
    
    try {
      if ($validate) $data['validated'] = true;
      $member = Member::create($data);
      if ($member->is_leader) return $member->uploadPictureFromInput();
      else return $member;
    } catch (Exception $e) {
      return false;
    }
  }
  
  public static function checkInputData($canEditIdentity = true, $canEditContact = true, $canEditSection = true, $canEditTotem = true, $canEditLeader = true) {
    $firstName = Input::get('first_name');
    $lastName = Input::get('last_name');
    $birthDateDay = Input::get('birth_date_day');
    $birthDateMonth = Input::get('birth_date_month');
    $birthDateYear = Input::get('birth_date_year');
    $birthDate = Helper::checkAndReturnDate($birthDateYear, $birthDateMonth, $birthDateDay);
    $gender = Input::get('gender');
    $nationality = mb_strtoupper(Input::get('nationality'));
    $address = Input::get('address');
    $postcode = Input::get('postcode');
    $city = Input::get('city');
    $hasHandicap = Input::get('has_handicap') ? true : false;
    $handicapDetails = Input::get('handicap_details');
    $comments = Input::get('comments');
    $leaderName = Input::get('leader_name');
    $leaderInCharge = Input::get('leader_in_charge') ? true : false;
    $leaderDescription = Input::get('leader_description');
    $leaderRole = Input::get('leader_role');
    $sectionId = Input::get('section');
    $subgroup = Input::get('subgroup');
    $phone1Unformatted = Input::get('phone1');
    $phone1Owner = Input::get('phone1_owner');
    $phone1Private = Input::get('phone1_private') ? true : false;
    $phone2Unformatted = Input::get('phone2');
    $phone2Owner = Input::get('phone2_owner');
    $phone2Private = Input::get('phone2_private') ? true : false;
    $phone3Unformatted = Input::get('phone3');
    $phone3Owner = Input::get('phone3_owner');
    $phone3Private = Input::get('phone3_private') ? true : false;
    $phoneMemberUnformatted = Input::get('phone_member');
    $phoneMemberPrivate = Input::get('phone_member_private');
    $email1 = strtolower(Input::get('email1'));
    $email2 = strtolower(Input::get('email2'));
    $email3 = strtolower(Input::get('email3'));
    $emailMember = strtolower(Input::get('email_member'));
    $totem = Input::get('totem');
    $quali = Input::get('quali');
    $familyMembers = Input::get('family_in_other_units');
    $familyDetails = Input::get('family_in_other_units_details');
    $isLeader = Input::get('is_leader') ? true : false;
    $policyAgreement = Input::get('policy_agreement') ? true : false;
    
    $errorMessage = "";
    
    if ($canEditIdentity) {
      if (!$firstName)
        $errorMessage .= "Il manque le prénom. ";
      elseif (!Helper::hasCorrectCapitals($firstName, true))
        $errorMessage .= "L'usage des majuscules dans le prénom n'est pas correct. ";
      
      if (!$lastName)
        $errorMessage .= "Il manque le nom de famille. ";
      elseif (!Helper::hasCorrectCapitals($lastName, false))
        $errorMessage .= "L'usage des majuscules dans le nom de famille n'est pas correct. ";
      
      if (!$birthDate)
        $errorMessage .= "La date de naissance n'est pas valide. ";
      
      if ($gender != 'M' && $gender != 'F')
        $errorMessage .= "Le sexe n'est pas une entrée valide. ";
      
      if (strlen($nationality) < 2 || strlen($nationality) > 3)
        $errorMessage .= "Utiliser la notation en deux lettres pour la nationality (BE, FR, ...). ";
    }
    
    if ($canEditContact) {
      if (!$address || !$postcode || !$city)
        $errorMessage .= "L'adresse n'est pas complète. ";
      elseif (!is_numeric ($postcode))
        $errorMessage .= "Le code postal doit être un nombre. ";
      
      $phone1 = Helper::formatPhoneNumber($phone1Unformatted);
      if ($phone1Unformatted && !$phone1)
        $errorMessage .= "Le numéro de téléphone \"$phone1Unformatted\" n'est pas valide. ";    
      
      $phone2 = Helper::formatPhoneNumber($phone2Unformatted);
      if ($phone2Unformatted && !$phone2)
        $errorMessage .= "Le numéro de téléphone \"$phone2Unformatted\" n'est pas valide. ";
      
      $phone3 = Helper::formatPhoneNumber($phone3Unformatted);
      if ($phone3Unformatted && !$phone3)
        $errorMessage .= "Le numéro de téléphone \"$phone3Unformatted\" n'est pas valide. ";
      
      $phoneMember = Helper::formatPhoneNumber($phoneMemberUnformatted);
      if ($phoneMemberUnformatted && !$phoneMember)
        $errorMessage .= "Le numéro de GSM du scout \"$phoneMemberUnformatted\" n'est pas correct. ";
      
      if (!$phone1Unformatted && !$phone2Unformatted && !$phone3Unformatted && !$phoneMemberUnformatted)
        $errorMessage .= "Il est nécessaire d'indiquer au moins un numéro de téléphone. ";
      
      if ($email1 && !filter_var($email1, FILTER_VALIDATE_EMAIL))
        $errorMessage .= "L'adresse e-mail \"$email1\" n'est pas valide. ";
      
      if ($email2 && !filter_var($email2, FILTER_VALIDATE_EMAIL))
        $errorMessage .= "L'adresse e-mail \"$email2\" n'est pas valide. ";
      
      if ($email3 && !filter_var($email3, FILTER_VALIDATE_EMAIL))
        $errorMessage .= "L'adresse e-mail \"$email3\" n'est pas valide. ";
      
      if ($emailMember && !filter_var($emailMember, FILTER_VALIDATE_EMAIL))
        $errorMessage .= "L'adresse e-mail du scout \"$emailMember\" n'est pas valide. ";
    }
    
    if ($canEditTotem) {
      if ($totem && !Helper::hasCorrectCapitals($totem))
        $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    }
    
    if ($canEditLeader) {
      if ($isLeader) {
        if (!$leaderName)
          $errorMessage .= "Il manque le nom d'animateur. ";
        elseif (!Helper::hasCorrectCapitals ($leaderName, true))
          $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
      }
    }
    
    if ($canEditIdentity) {
      if ($hasHandicap && !$handicapDetails)
        $errorMessage .= "Merci de préciser la nature du handicap. ";
      
      if (!$hasHandicap && trim($handicapDetails))
        $errorMessage .= "Vous devez cocher la case handicap, ou supprimer les détails du handicap. ";
    }
    
    if ($familyMembers != "0" && $familyMembers != "1" && $familyMembers != "2")
      $familyMembers = 0;
    
    if ($errorMessage) {
      return $errorMessage;
    } else {
      return array(
          'first_name' => $firstName,
          'last_name' => $lastName,
          'birth_date' => $birthDate,
          'gender' => $gender,
          'nationality' => $nationality,
          'address' => $address,
          'postcode' => $postcode,
          'city' => $city,
          'has_handicap' => $hasHandicap,
          'handicap_details' => $handicapDetails,
          'comments' => $comments,
          'leader_name' => $leaderName,
          'leader_in_charge' => $leaderInCharge,
          'leader_description' => $leaderDescription,
          'leader_role' => $leaderRole,
          'section_id' => $sectionId,
          'subgroup' => $subgroup,
          'phone1' => $phone1,
          'phone1_owner' => $phone1Owner,
          'phone1_private' => $phone1Private,
          'phone2' => $phone2,
          'phone2_owner' => $phone2Owner,
          'phone2_private' => $phone2Private,
          'phone3' => $phone3,
          'phone3_owner' => $phone3Owner,
          'phone3_private' => $phone3Private,
          'phone_member' => $phoneMember,
          'phone_member_private' => $phoneMemberPrivate,
          'email1' => $email1,
          'email2' => $email2,
          'email3' => $email3,
          'email_member' => $emailMember,
          'totem' => $totem,
          'quali' => $quali,
          'family_in_other_units' => $familyMembers,
          'family_in_other_units_details' => $familyDetails,
          'is_leader' => $isLeader,
          'validated' => false,
        );
    }
  }
  
  public function uploadPictureFromInput() {
    
    $pictureFile = Input::file('picture');
    
    if ($pictureFile) {
      if (!$pictureFile->getSize()) {
        return "La photo n'a pas pu être enregistrée.";
      } else {
        try {
          $image = new Resizer($pictureFile->getRealPath());
          $image->resizeImage(256, 256, "crop");
          $image->saveImage($this->getPicturePath());
          $this->has_picture = true;
          $this->save();
          return true;
        } catch (Exception $e) {
          return "La photo n'a pas pu être enregistrée.";
        }
      }
    }
    
    return true;
    
  }
    
}
