<?php

class LeaderController extends BaseController {
  
  public function showPage($archive = null) {
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $countInCharge = 0;
    $countOthers = 0;
    $menInCharge = false; // Whether there is at least one male in charge
    $menInOthers = false; // Whether there is at least one male in the others
    foreach ($leaders as $leader) {
      if ($leader->leader_in_charge) {
        $countInCharge++;
        if ($leader->gender != 'F') $menInCharge = true;
      } else {
        $countOthers++;
        if ($leader->gender != 'F') $menInOthers = true;
      }
    }
    
    return View::make('pages.leader.leaders', array(
        'is_leader' => $this->user->isLeader(),
        'leaders' => $leaders,
        'count_in_charge' => $countInCharge,
        'count_others' => $countOthers,
        'men_in_charge' => $menInCharge,
        'men_in_others' => $menInOthers,
    ));
  }
  
  public function showEdit($section_slug, $memberId = false) {
    
    if (!$this->user->isLeader()) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $scouts = Member::where('is_leader', '=', false)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    $scoutsForSelect = array('' => 'Sélectionne un scout');
    foreach ($scouts as $scout) {
      $scoutsForSelect[$scout->id] = $scout->last_name . " " . $scout->first_name;
    }
    
    if ($memberId) {
      $memberToTurnLeader = Member::where('is_leader', '=', false)
              ->where('id', '=', $memberId)
              ->first();
      if ($memberToTurnLeader) $leaders[] = $memberToTurnLeader;
    }
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
        'scouts' => $scoutsForSelect,
        'scout_to_leader' => $memberId,
    ));
  }
  
  public function showMemberToLeader($member_id) {
    return $this->showEdit($this->section->slug, $member_id);
  }
  
  public function postMemberToLeader($section_slug) {
    $memberId = Input::get('member_id');
    if ($memberId) {
      return Redirect::route('edit_leaders_member_to_leader',
              array('member_id' => $memberId, 'section_slug' => $section_slug));
    } else {
      return Redirect::route('edit_leaders', array('section_slug' => $section_slug));
    }
  }

  public function showEditPrivileges() {
    
    if (!$this->user->isLeader()) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
    ));
  }
  
  public function getLeaderPicture($leader_id) {
    $leader = Member::find($leader_id);
    if ($leader && $leader->is_leader && $leader->has_picture) {
      $path = $leader->getPicturePath();
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
  public function submitLeader() {
    $firstName = Input::get('first_name');
    $lastName = Input::get('last_name');
    $birthDateDay = Input::get('birth_date_day');
    $birthDateMonth = Input::get('birth_date_month');
    $birthDateYear = Input::get('birth_date_year');
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
    $phoneMemberUnformatted = Input::get('phone_member');
    $phoneMemberPrivate = Input::get('phone_member_private');
    $emailMember = Input::get('email_member');
    $totem = Input::get('totem');
    $quali = Input::get('quali');
    $memberId = Input::get('member_id');
    $familyMembers = Input::get('family_in_other_units');
    $familyDetails = Input::get('family_in_other_units_details');
    $pictureFile = Input::file('picture');
    
    $editionLevel = $this->editionLevelAllowed($memberId, $sectionId);
    if (!$editionLevel) {
      return Redirect::to(URL::previous())
              ->withInput()
              ->with('error_message', "Tu n'as pas le droit de faire cette modification.");
    }
    
    $errorMessage = "";
    
    if (!$leaderName)
      $errorMessage .= "Tu dois entrer un nom d'animateur. ";
    elseif (!Helper::hasCorrectCapitals ($leaderName, true))
      $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
    
    $phoneMember = Helper::formatPhoneNumber($phoneMemberUnformatted);
    if ($phoneMemberUnformatted && !$phoneMember)
      $errorMessage .= "Le numéro de GSM n'est pas correct. ";
    
    if ($totem && !Helper::hasCorrectCapitals($totem))
      $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($totem && !Helper::hasCorrectCapitals($quali))
      $errorMessage .= "L'usage des majuscules dans le quali n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($emailMember && !filter_var($emailMember, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail n'est pas valide. ";
    
    if (!$firstName)
      $errorMessage .= "Tu dois entrer le prénom. ";
    elseif (!Helper::hasCorrectCapitals($firstName, true))
      $errorMessage .= "L'usage des majuscules dans le prénom n'est pas correct. ";
    
    if (!$lastName)
      $errorMessage .= "Tu dois entrer le nom de famille. ";
    elseif (!Helper::hasCorrectCapitals($lastName, false))
      $errorMessage .= "L'usage des majuscules dans le nom de famille n'est pas correct. ";
    
    $birthDate = Helper::checkAndReturnDate($birthDateYear, $birthDateMonth, $birthDateDay);
    if (!$birthDate)
      $errorMessage .= "La date de naissance n'est pas valide. ";
    
    if ($gender != 'M' && $gender != 'F')
      $errorMessage .= "Le sexe n'est pas une entrée valide. ";
    
    if (strlen($nationality) < 2 || strlen($nationality) > 3)
      $errorMessage .= "Utilise la notation en deux lettres pour la nationality (BE, FR, ...). ";
    
    if (!$address || !$postcode || !$city)
      $errorMessage .= "L'adresse n'est pas complète. ";
    elseif (!is_numeric ($postcode))
      $errorMessage .= "Le code postal doit être un nombre. ";
    
    if ($hasHandicap && !$handicapDetails) $errorMessage .= "Merci de préciser la nature du handicap. ";
    
    if ($familyMembers != "0" && $familyMembers != "1" && $familyMembers != "2")
      $familyMembers = 0;
    
    if ($errorMessage) {
      $success = false;
      $message = $errorMessage;
    } else {
      if ($memberId) {
        $leader = Member::find($memberId);
        if ($leader) {
          $leader->comments = $comments;
          $leader->leader_name = $leaderName;
          $leader->leader_in_charge = $leaderInCharge;
          $leader->leader_description = $leaderDescription;
          $leader->leader_role = $leaderRole;
          $leader->phone_member = $phoneMember;
          $leader->phone_member_private = $phoneMemberPrivate;
          $leader->email_member = $emailMember;
          $leader->totem = $totem;
          $leader->quali = $quali;
          if ($editionLevel == "full") {
            $leader->address = $address;
            $leader->postcode = $postcode;
            $leader->city = $city;
            $leader->first_name = $firstName;
            $leader->last_name = $lastName;
            $leader->birth_date = $birthDate;
            $leader->gender = $gender;
            $leader->nationality = $nationality;
            $leader->has_handicap = $hasHandicap;
            $leader->handicap_details = $handicapDetails;
            $leader->section_id = $sectionId;
            $leader->family_in_other_units = $familyMembers;
            $leader->family_in_other_units_details = $familyDetails;
            $leader->is_leader = true;
          }
          
          try {
            $leader->save();
            $success = true;
            $message = "Les données de l'animateur ont été modifiées.";
          } catch (Exception $e) {
            $success = false;
            $message = "Une erreur est survenue. Les données n'ont pas été enregistrées.";
            throw $e;
          }
        } else {
          // Member not found
          $success = false;
          $message = "Une erreur est survenue. Les données n'ont pas été enregistrées.";
        }
      } else {
        // New leader
        try {
          $leader = Member::create(array(
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
              'phone_member' => $phoneMember,
              'phone_member_private' => $phoneMemberPrivate,
              'email_member' => $emailMember,
              'totem' => $totem,
              'quali' => $quali,
              'family_in_other_units' => $familyMembers,
              'family_in_other_units_details' => $familyDetails,
              'is_leader' => true,
          ));
          $success = true;
          $message = "L'animateur a été ajouté au listing.";
        } catch (Exception $e) {
          $success = false;
          $message = "Une erreur est survenue. L'animateur n'a pas été ajouté. $e";
        }
      }
    }
    
    // Upload picture
    if ($success && $leader && $pictureFile) {
      if (!$pictureFile->getSize()) {
        $success = false;
        $message = "Les données ont été modifiées, mais la photo n'a pas pu être enregistrée.";
      } else {
        try {
          $image = new Resizer($pictureFile->getRealPath());
          $image->resizeImage(256, 256, "crop");
          $image->saveImage($leader->getPicturePath());
          $leader->has_picture = true;
          $leader->save();
        } catch (Exception $e) {
          $success = false;
          $message = "Les données ont été enregistrées, mais la photo n'a pas pu être mise à jour.";
          throw $e;
        }
      }
    }
    
    if ($success)
      return Redirect::to(URL::route('edit_leaders', array('section_slug' => $leader->getSection()->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())->with($success ? 'success_message' : 'error_message', $message)->withInput();
  }
  
  private function editionLevelAllowed($memberId, $sectionId) {
    if (!$memberId) {
      // Creating new leader
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        // Full edition is required to create a member
        return "full";
      } else {
        return false;
      }
    } else{
      // Edit a member
      $existingMember = Member::find($memberId);
      if (!$existingMember) return "full"; // Let the process continue, it will fail later anyway
      $memberSectionId = $existingMember->section_id;
      
      // Check if the user has full edit privileges
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_ALL, $memberSectionId)) {
        return "full";
      }
      
      // Check if the user is modifying their own member entry
      if ($this->user->can(Privilege::$UPDATE_OWN_LISTING_ENTRY, $sectionId) &&
              $sectionId == $memberSectionId &&
              $user->isOwnerOfMember($memberId)) {
        return "full";
      }
      
      // Check if the user has limited edit privileges
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $memberSectionId)) {
        return "limited";
      }
      
      // None of the above apply
      return false;
    }
    
    
  }
  
}
