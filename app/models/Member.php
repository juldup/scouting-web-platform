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
 * This Eloquent class represents a member (scout or leader) registered in the unit
 * 
 * Columns:
 *   Identity
 *   - first_name:       The first name of the member
 *   - last_name:        The last name of the member
 *   - birth_date:       The date of birth
 *   - gender:           The gender ('M'/'F') of the member
 *   - nationality:      The nationality of the member ('BE', 'FR', etc.)
 *   - has_handicap:     Whether the member has a handicap
 *   - handicap_details: A short explanation about the handicap (if any)
 *   Scout-related details
 *   - totem:                         The totem of the member (if any)
 *   - quali:                         The quali of the member (if any)
 *   - section_id:                    The section this member is currently in
 *   - year_in_section:               The year in the section
 *   - year_in_section_last_update    The scout year in format YYYY-YYYY corresponding to the members' current year_in_section
 *   - subgroup:                      The subgroup in the section this member belongs to
 *   - role:                          The role of the member in the section
 *   - family_in_other_units:         The number of direct relatives (same family) registered in other scout units
 *   - family_in_other_units_details: Explanation about the previous field
 *   - comments:                      A short optional comment about this member
 *   Contact
 *   - address:                       The main address (street and number) of this member
 *   - postcode:                      The postcode of the address
 *   - city:                          The city of the address
 *   - phone1:                        A contact phone numbers of the parents
 *   - phone2:                        Another contact phone numbers of the parents
 *   - phone3:                        Another contact phone numbers of the parents
 *   - phone_member:                  The phone number of the member (if any)
 *   - phone1_owner:                  The name of the owner of phone1
 *   - phone2_owner:                  The name of the owner of phone2
 *   - phone3_owner:                  The name of the owner of phone3
 *   - phone1_private:                Whether phone1 is private and should not appear in listings
 *   - phone2_private:                Whether phone2 is private and should not appear in listings
 *   - phone3_private:                Whether phone3 is private and should not appear in listings
 *   - phone_member_private:          Whether phone_member is private and should not appear in listings
 *   - email1:                        The e-mail address of the parents
 *   - email2:                        Another e-mail address of the parents
 *   - email3:                        Another e-mail address of the parents
 *   - email_member:                  The e-mail address of the member
 *   Registration
 *   - subscription_paid:             Whether the subscription fee had been paid
 *   - last_reregistration:           The last time this member was marked as reregistered (scouting year in the format 'YYYY-YYYY')
 *   Leader stuff
 *   - is_leader:                     Whether the member is a leader (the following fields make sense only if this one is true)
 *   - leader_in_charge:              Whether the member is a leader in charge of his/her section
 *   - list_order:                    Order to sort the leader list on the leader page
 *   - leader_name:                   Name of the leader (how they are called in their section)
 *   - leader_description:            A short description of the leader
 *   - leader_role:                   Role of the leader in the section
 *   - has_picture:                   Whether this leader has an uploaded picture
 *   Initial registration
 *   - validated:                     Whether this member's registration has been validated by the leaders (the member is
 *                                    not actually a member as long as this field is false)
 */
class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Folder in the file system (relative to the storage folder) in which leader pictures are stored
  public static $PICTURE_FOLDER_PATH = "site_data/leader_pictures/";
  
  /**
   * Returns the section this member belongs to
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  /**
   * Returns the birth date in a human readable format ('d/m/Y')
   */
  public function getHumanBirthDate() {
    return date('d/m/Y', strtotime($this->birth_date));
  }
  
  /**
   * Returns whether there is a registered member with the given
   * e-mail address as parent's or member's e-mail address
   */
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
  
  /**
   * Returns whether the member is reregistered for the next year
   */
  public function isReregistered() {
    return $this->last_reregistration == date('Y') . "-" . (date('Y') + 1);
  }
  
  /**
   * Returns the URL at which the leader picture can be downloaded
   */
  public function getPictureURL() {
    return URL::route('get_member_picture', array('leader_id' => $this->id));
  }
  
  /**
   * Returns the path in the local file system where the leader picture file is
   */
  public function getPicturePath() {
    return $this->getPicturePathFolder() . $this->getPicturePathFilename();
  }
  
  /**
   * Returns the folder in the local file system containg the leader picture file
   */
  public function getPicturePathFolder() {
    return storage_path(self::$PICTURE_FOLDER_PATH);
  }
  
  /**
   * Returns the filename of the leader picture file in the file system
   */
  public function getPicturePathFilename() {
    return $this->id . ".picture";
  }
  
  /**
   * Returns the array of family members in other units to plug in
   * in a html select element
   */
  public static function getFamilyOtherUnitsForSelect() {
    return array('0' => "Aucun", '1' => "1", '2' => '2 ou plus');
  }
  
  /**
   * Returns the personal phone number (or a parent's phone number if no personal phone number)
   */
  public function getPersonalPhone() {
    if ($this->phone_member) return $this->phone_member;
    if ($this->phone1) return $this->phone1;
    if ($this->phone2) return $this->phone2;
    if ($this->phone3) return $this->phone3;
    return "";
  }
  
  /**
   * Returns the first name and last name of the member, separated by a space
   */
  public function getFullName() {
    return $this->first_name . " " . $this->last_name;
  }
  
  /**
   * Returns a public phone number of this member
   */
  public function getPublicPhone() {
    if ($this->phone1 && !$this->phone1_private) return $this->phone1;
    if ($this->phone2 && !$this->phone2_private) return $this->phone2;
    if ($this->phone3 && !$this->phone3_private) return $this->phone3;
    if ($this->phone_member && !$this->phone_member_private) return $this->phone_member;
    return "";
  }
  
  /**
   * Returns the list of public phone numbers of this members as a string
   * (including parent's and personal phone numbers)
   * 
   * @param string $separator  The separator used between the phone numbers
   * @param boolean $showAlsoPrivate  If true, private phone numbers are also included
   */
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
  
  /**
   * Returns the list of parent's public phone numbers
   */
  public function getParentsPublicPhones() {
    $phones = array();
    if ($this->phone1 && !$this->phone1_private) $phones[] = $this->phone1;
    if ($this->phone2 && !$this->phone2_private) $phones[] = $this->phone2;
    if ($this->phone3 && !$this->phone3_private) $phones[] = $this->phone3;
    return $phones;
  }
  
  /**
   * Returns whether there exists at least the e-mail address of one parent
   */
  public function hasParentsEmailAddress() {
    return $this->email1 || $this->email2 || $this->email3 ? true : false;
  }
  
  /**
   * Returns the list of e-mail addresses of the parents
   */
  public function getParentsEmailAddresses() {
    $emails = array();
    if ($this->email1) $emails[] = $this->email1;
    if ($this->email2) $emails[] = $this->email2;
    if ($this->email3) $emails[] = $this->email3;
    return $emails;
  }
  
  /**
   * Returns the list of e-mail addresses as a string
   * 
   * @param string $separator  The separator used between the e-mail addresses
   * @param boolean $includePersonal  If true, the personal e-mail address is included
   */
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
  
  /**
   * If input data is correct, updates this member and returns true.
   * If input data is incorrect, returns false or an error message.
   * 
   * @param type $canEditIdentity  Whether the current user is allowed to edit identity information
   * @param type $canEditContact  Whether the current user is allowed to edit contact information
   * @param type $canEditSection  Whether the current user is allowed to change the section
   * @param type $canEditTotem  Whether the current user is allowed to edit totem, quali, subgroup and role
   * @param type $canEditLeader  Whether the current user is allowed to edit leader information
   */
  public function updateFromInput($canEditIdentity, $canEditContact, $canEditSection, $canEditTotem, $canEditLeader, $canEditPhoto) {
    // Archive leaders
    ArchivedLeader::archiveLeadersIfNeeded();
    // Get input data and check it for errors
    $data = self::checkInputData($canEditIdentity, $canEditContact, $canEditSection, $canEditTotem, $canEditLeader);
    if (is_string($data)) {
      // An error has occured
      return $data;
    }
    // Update identity
    if ($canEditIdentity) {
      $this->first_name = $data['first_name'];
      $this->last_name = $data['last_name'];
      $this->birth_date = $data['birth_date'];
      $this->gender = $data['gender'];
      $this->nationality = $data['nationality'];
      $this->has_handicap = $data['has_handicap'];
      $this->handicap_details = $data['handicap_details'];
    }
    // Update contact information
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
    // Update section
    if ($canEditSection) {
        $this->section_id = $data['section_id'];
    }
    // Update totem, quali and subgroup
    if ($canEditTotem) {
      $this->totem = $data['totem'];
      $this->quali = $data['quali'];
      $this->subgroup = $data['subgroup'];
      $this->role = $data['role'];
    }
    // Update leader information
    if ($canEditLeader) {
      $this->is_leader = $data['is_leader'];
      $this->leader_name = $data['leader_name'];
      $this->leader_in_charge = $data['leader_in_charge'];
      $this->leader_description = $data['leader_description'];
      $this->leader_role = $data['leader_role'];
      $this->list_order = $data['list_order'];
    }
    // Update comments
    $this->comments = $data['comments'];
    // Update family members informations
    $this->family_in_other_units = $data['family_in_other_units'];
    $this->family_in_other_units_details = $data['family_in_other_units_details'];
    // Mark as registered
    $this->validated = true;
    // Save
    try {
      $this->save();
      return $this->uploadPictureFromInput() ? true : false;
    } catch (Exception $ex) {
      Log::error($ex);
      return false;
    }
  }
  
  /**
   * If input data is correct, creates and returns a new user.
   * If input data is incorrect, returns false or an error message.
   * 
   * @param boolean $validate  If true, the newly created member is immediately marked as registered
   */
  public static function createFromInput($validate = false) {
    // Archive leaders
    ArchivedLeader::archiveLeadersIfNeeded();
    // Get data from input and check it
    $data = self::checkInputData();
    if (is_string($data)) {
      // An error has occured
      return $data;
    }
    // Create member
    try {
      if ($validate) $data['validated'] = true;
      $data['year_in_section_last_update'] = date('Y') . "-" . (date('Y') + 1);
      $member = Member::create($data);
      // Set last reregistration year
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      return $member->uploadPictureFromInput();
    } catch (Exception $e) {
      Log::error($e);
      return false;
    }
  }
  
  /**
   * Checks whether the input data is valid. If it is valid, returns
   * the an array containg the data. If it is invalid, returns a string
   * containing an error message.
   * 
   * @param type $canEditIdentity  Whether the current user is allowed to edit identity information
   * @param type $canEditContact  Whether the current user is allowed to edit contact information
   * @param type $canEditSection  Whether the current user is allowed to change the section
   * @param type $canEditTotem  Whether the current user is allowed to edit totem, quali, subgroup and role
   * @param type $canEditLeader  Whether the current user is allowed to edit leader information
   */
  public static function checkInputData($canEditIdentity = true, $canEditContact = true, $canEditSection = true, $canEditTotem = true, $canEditLeader = true) {
    // Get data from input
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
    $listOrder = intval(Input::get('list_order'));
    $leaderDescription = Input::get('leader_description');
    $leaderRole = Input::get('leader_role');
    $sectionId = Input::get('section');
    $subgroup = Input::get('subgroup');
    $role = Input::get('role');
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
    // Error message is initially empty
    $errorMessage = "";
    // Check all fields for errors
    if ($canEditIdentity) {
      // First name
      if (!$firstName)
        $errorMessage .= "Il manque le prénom. ";
      elseif (!Helper::hasCorrectCapitals($firstName, true))
        $errorMessage .= "L'usage des majuscules dans le prénom n'est pas correct. ";
      // Last name
      if (!$lastName)
        $errorMessage .= "Il manque le nom de famille. ";
      elseif (!Helper::hasCorrectCapitals($lastName, false))
        $errorMessage .= "L'usage des majuscules dans le nom de famille n'est pas correct. ";
      // Birth date
      if (!$birthDate)
        $errorMessage .= "La date de naissance n'est pas valide. ";
      // Gender
      if ($gender != 'M' && $gender != 'F')
        $errorMessage .= "Le sexe n'est pas une entrée valide. ";
      // Nationality
      if (strlen($nationality) < 2 || strlen($nationality) > 3)
        $errorMessage .= "Utiliser la notation en deux lettres pour la nationality (BE, FR, ...). ";
    }
    if ($canEditContact) {
      // Address
      if (!$address || !$postcode || !$city)
        $errorMessage .= "L'adresse n'est pas complète. ";
      elseif (!is_numeric ($postcode))
        $errorMessage .= "Le code postal doit être un nombre. ";
      // Phone numbers
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
      // E-mail addresses
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
      // Totem
      if ($totem && !Helper::hasCorrectCapitals($totem))
        $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    }
    if ($canEditLeader) {
      if ($isLeader) {
        // Leader name
        if (!$leaderName)
          $errorMessage .= "Il manque le nom d'animateur. ";
        elseif (!Helper::hasCorrectCapitals ($leaderName, true))
          $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
      }
    }
    if ($canEditIdentity) {
      // Handicap
      if ($hasHandicap && !$handicapDetails)
        $errorMessage .= "Merci de préciser la nature du handicap. ";
      if (!$hasHandicap && trim($handicapDetails))
        $errorMessage .= "Vous devez cocher la case handicap, ou supprimer les détails du handicap. ";
    }
    // Set default value for $familyMembers
    if ($familyMembers != "0" && $familyMembers != "1" && $familyMembers != "2")
      $familyMembers = 0;
    // Make sure the member is not a non-leader in the "Unit" section
    if (!$isLeader && $sectionId == 1) {
      $errorMessage .= "Il est impossible d'inscrire un membre non animateur dans la section \"Unité\". ";
    }
    // Return error message or array containing the data
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
          'list_order' => $listOrder,
          'leader_description' => $leaderDescription,
          'leader_role' => $leaderRole,
          'section_id' => $sectionId,
          'subgroup' => $subgroup,
          'role' => $role,
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
  
  /**
   * Saves the uploaded picture to the file system and updates the member
   * to mark it as having a leader picture. Returns this member instance, or
   * a string in case of error.
   */
  public function uploadPictureFromInput() {
    // Get picture file
    $pictureFile = Input::file('picture');
    if ($pictureFile) {
      if (!$pictureFile->getSize()) {
        // An upload error has occured
        return "La photo n'a pas pu être enregistrée.";
      } else {
        try {
          // Resize image
          $image = new Resizer($pictureFile->getRealPath());
          $image->resizeImage(256, 256, "crop");
          // Save image
          $image->saveImage($this->getPicturePath());
          // Update member
          $this->has_picture = true;
          $this->save();
          return $this;
        } catch (Exception $e) {
          Log::error($e);
          // An error has occured while saving the picture
          return "La photo n'a pas pu être enregistrée.";
        }
      }
    }
    // There is no picture file
    return $this;
  }
  
  /**
   * Updates the year in section of all members automatically based on the current
   * date and the year_in_section_last_update field
   */
  public static function updateYearInSectionAuto() {
    // Get current year
    $month = date('m');
    $startYear = date('Y');
    if ($month < 8) $startYear--;
    $currentYear = $startYear . "-" . ($startYear + 1);
    // Update members' year in section where needed
    $count = Member::where('validated', '=', true)
            ->where('year_in_section_last_update', '<', $currentYear)
            ->increment('year_in_section');
    Member::where('validated', '=', true)
            ->where('year_in_section_last_update', '<', $currentYear)
            ->update(array('year_in_section_last_update' => $currentYear));
    if ($count) {
      LogEntry::log("Inscription", "Augmentation automatique de l'année de tous les membres");
    }
  }
  
  /**
   * Return whether this user can do the given action (privilege) for the given section
   */
  public function can($action, $section = "") {
    // Find section id
    if (!$section) {
      $sectionId = $this->section_id;
    } else if (is_numeric($section)) {
      $sectionId = $section;
    } else {
      $sectionId = $section->id;
    }
    // Convert action to operation
    $operation = $action;
    if (!is_string($operation)) $operation = $action['id'];
    // Search privileges
    $privileges = Privilege::where('member_id', '=', $this->id)->where("operation", "=", $operation)->get();
    foreach ($privileges as $privilege) {
      if ($privilege->scope == 'U') {
        // Unit-wide privilege found, access granted
        return true;
      } else if ($privilege->scope == 'S') {
        // Section-wide privilege found
        if ($sectionId == $this->section_id) {
          // Sections match, access granted
          return true;
        }
      }
    }
    // No associated leader or matching privilege
    return false;
  }
  
}
