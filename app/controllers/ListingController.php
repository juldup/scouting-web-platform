<?php

class ListingController extends BaseController {
  
  public function showPage() {
    
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    
    $sectionArray = array();
    foreach ($sections as $section) {
      $members = Member::where('validated', '=', true)
              ->where('section_id', '=', $section->id)
              ->where('is_leader', '=', false)
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
      $showTotem = false;
      $showSubgroup = false;
      foreach ($members as $member) {
        if ($member->totem) $showTotem = true;
        if ($member->subgroup) $showSubgroup = true;
      }
      $sectionArray[] = array(
          'section_data' => $section,
          'members' => $members,
          'show_totem' => $showTotem,
          'show_subgroup' => $showSubgroup,
      );
    }
    
    return View::make('pages.listing.listing', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                        || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'sections' => $sectionArray,
    ));
  }
  
  public function manage() {
    
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $members = Member::where('validated', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    
    return View::make('pages.listing.manageListing', array(
        'members' => $members,
    ));
    
  }
  
  public function submit() {
    $memberId = Input::get('member_id');
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
    $sectionId = Input::get('section');
    $phoneMemberUnformatted = Input::get('phone_member');
    $phoneMemberPrivate = Input::get('phone_member_private');
    $emailMember = strtolower(Input::get('email_member'));
    $totem = Input::get('totem');
    $quali = Input::get('quali');
    $familyMembers = Input::get('family_in_other_units');
    $familyDetails = Input::get('family_in_other_units_details');
    $email1 = strtolower(Input::get('email1'));
    $email2 = strtolower(Input::get('email2'));
    $email3 = strtolower(Input::get('email3'));
    $phone1Unformatted = Input::get('phone1');
    $phone1Owner = Input::get('phone1_owner');
    $phone1Private = Input::get('phone1_private') ? true : false;
    $phone2Unformatted = Input::get('phone2');
    $phone2Owner = Input::get('phone2_owner');
    $phone2Private = Input::get('phone2_private') ? true : false;
    $phone3Unformatted = Input::get('phone3');
    $phone3Owner = Input::get('phone3_owner');
    $phone3Private = Input::get('phone3_private') ? true : false;
    
    $errorMessage = "";
    
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
      $errorMessage .= "Le numéro de GSM n'est pas correct. ";
    
    if ($totem && !Helper::hasCorrectCapitals($totem))
      $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($quali && !Helper::hasCorrectCapitals($quali))
      $errorMessage .= "L'usage des majuscules dans le quali n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($email1 && !filter_var($email1, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email1\" n'est pas valide. ";
    
    if ($email2 && !filter_var($email2, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email2\" n'est pas valide. ";
    
    if ($email3 && !filter_var($email3, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email3\" n'est pas valide. ";
    
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
    
    if ($hasHandicap && !$handicapDetails)
      $errorMessage .= "Merci de préciser la nature du handicap. ";
    
    if ($familyMembers != "0" && $familyMembers != "1" && $familyMembers != "2")
      $familyMembers = 0;
    
    if ($errorMessage) {
      $success = false;
      $message = $errorMessage;
    } else {
      $member = Member::find($memberId);
      if ($member) {
        $member->comments = $comments;
        $member->phone1 = $phone1;
        $member->phone1_owner = $phone1Owner;
        $member->phone1_private = $phone1Private;
        $member->phone2 = $phone2;
        $member->phone2_owner = $phone2Owner;
        $member->phone2_private = $phone2Private;
        $member->phone3 = $phone3;
        $member->phone3_owner = $phone3Owner;
        $member->phone3_private = $phone3Private;
        $member->phone_member = $phoneMember;
        $member->phone_member_private = $phoneMemberPrivate;
        $member->email1 = $email1;
        $member->email2 = $email2;
        $member->email3 = $email3;
        $member->email_member = $emailMember;
        $member->totem = $totem;
        $member->quali = $quali;
        $member->address = $address;
        $member->postcode = $postcode;
        $member->city = $city;
        $member->first_name = $firstName;
        $member->last_name = $lastName;
        $member->birth_date = $birthDate;
        $member->gender = $gender;
        $member->nationality = $nationality;
        $member->has_handicap = $hasHandicap;
        $member->handicap_details = $handicapDetails;
        $member->section_id = $sectionId;
        $member->family_in_other_units = $familyMembers;
        $member->family_in_other_units_details = $familyDetails;
        $member->validated = true;

        try {
          $member->save();
          $success = true;
          $message = "$firstName $lastName est à présent inscrit.";
        } catch (Exception $e) {
          $success = false;
          $message = "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
        }
      } else {
        // Member not found
        $success = false;
        $message = "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
      }
    }
    
    if ($success)
      return Redirect::to(URL::route('manage_registration', array('section_slug' => $this->section->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())->with($success ? 'success_message' : 'error_message', $message)->withInput();
  }
  
}
