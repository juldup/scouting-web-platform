<?php

class RegistrationController extends GenericPageController {
  
  public function showMain() {
    $page = $this->getPage();
    $pageContent = $page->content_html;
    $pageContent = str_replace("(PRIX UN ENFANT)", Parameter::get(Parameter::$PRICE_1_CHILD), $pageContent);
    $pageContent = str_replace("(PRIX DEUX ENFANTS)", Parameter::get(Parameter::$PRICE_2_CHILDS), $pageContent);
    $pageContent = str_replace("(PRIX TROIS ENFANTS)", Parameter::get(Parameter::$PRICE_3_CHILDS), $pageContent);
    $pageContent = str_replace("(PRIX UN ANIMATEUR)", Parameter::get(Parameter::$PRICE_1_LEADER), $pageContent);
    $pageContent = str_replace("(PRIX DEUX ANIMATEURS)", Parameter::get(Parameter::$PRICE_2_LEADERS), $pageContent);
    $pageContent = str_replace("(PRIX TROIS ANIMATEURS)", Parameter::get(Parameter::$PRICE_3_LEADERS), $pageContent);
    $pageContent = str_replace("BEXX-XXXX-XXXX-XXXX", Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), $pageContent);
    $pageContent = str_replace("(ACCES CHARTE)", '<a href="' . URL::route('unit_policy') . '">charte d&apos;unité</a>', $pageContent);
    $pageContent = str_replace("(ACCES CONTACT)", '<a href="' . URL::route('contacts') . '">contact</a>', $pageContent);
    $pageContent = str_replace("(ACCES FORMULAIRE)", '<a href="' . URL::route('registration_form') . '">formulaire d&apos;inscription</a>', $pageContent);
    
//    formulaire d'inscription.
    return View::make('pages.registration.registrationMain', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, $this->section),
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'page_title' => $this->getPageTitle(),
        'page_content' => $pageContent,
    ));
  }
  
  public function showForm() {
    return View::make('pages.registration.registrationForm', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
    ));
  }
  
  public function submit() {
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
    
    if (!$firstName)
      $errorMessage .= "Vous devez entrer le prénom. ";
    elseif (!Helper::hasCorrectCapitals($firstName, true))
      $errorMessage .= "L'usage des majuscules dans le prénom n'est pas correct. ";
    
    if (!$lastName)
      $errorMessage .= "Vous devez entrer le nom de famille. ";
    elseif (!Helper::hasCorrectCapitals($lastName, false))
      $errorMessage .= "L'usage des majuscules dans le nom de famille n'est pas correct. ";
    
    $birthDate = Helper::checkAndReturnDate($birthDateYear, $birthDateMonth, $birthDateDay);
    if (!$birthDate)
      $errorMessage .= "La date de naissance n'est pas valide. ";
    
    if ($gender != 'M' && $gender != 'F')
      $errorMessage .= "Le sexe n'est pas une entrée valide. ";
    
    if (strlen($nationality) < 2 || strlen($nationality) > 3)
      $errorMessage .= "Utilisez la notation en deux lettres pour la nationality (BE, FR, ...). ";
    
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
      $errorMessage .= "Vous devez donner au moins un numéro de téléphone. ";
    
    if ($email1 && !filter_var($email1, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email1\" n'est pas valide. ";
    
    if ($email2 && !filter_var($email2, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email2\" n'est pas valide. ";
    
    if ($email3 && !filter_var($email3, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail \"$email3\" n'est pas valide. ";
    
    if ($emailMember && !filter_var($emailMember, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail du scout \"$emailMember\" n'est pas valide. ";
    
    if ($totem && !Helper::hasCorrectCapitals($totem))
      $errorMessage .= "L'usage des majuscules dans le totem n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($quali && !Helper::hasCorrectCapitals($quali))
      $errorMessage .= "L'usage des majuscules dans le quali n'est pas correct (il doit commencer par une majuscule). ";
    
    if ($isLeader) {
      if (!$leaderName)
        $errorMessage .= "Entrez un nom d'animateur. ";
      elseif (!Helper::hasCorrectCapitals ($leaderName, true))
        $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
    }
    
    if ($hasHandicap && !$handicapDetails)
      $errorMessage .= "Merci de préciser la nature du handicap. ";
    
    if (!$hasHandicap && trim($handicapDetails))
      $errorMessage .= "Vous devez cocher la case handicap, ou supprimer les détails de l'handicap. ";
    
    if ($familyMembers != "0" && $familyMembers != "1" && $familyMembers != "2")
      $familyMembers = 0;
    
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY) && !$policyAgreement)
      $errorMessage .= "Vous devez adhérer à la charte d'unité pour inscrire un enfant.";
    
    if ($errorMessage) {
      $success = false;
      $message = $errorMessage;
    } else {
      try {
        Member::create(array(
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
        ));
        $success = true;
        $message = "Votre inscription a été enregistrée. L'animateur d'unité la validera prochainement.";
      } catch (Exception $e) {
        $success = false;
        $message = "Une erreur est survenue. Votre inscription n'a pas été enregistrée. " .
                "Veuillez réessayer ou <a href='" . URL::route('contacts') . 
                "'>contacter l'animateur d'unité</a>.";
      }
    }
    
    if ($success)
      return Redirect::to(URL::route('registration_form'))
            ->with('success_message', $message);
    else
      return Redirect::to(URL::route('registration_form'))
            ->with('error_message', $message)
            ->withInput();
  }
  
  protected function getEditRouteName() {
    return "edit_registration_page";
  }
  protected function getShowRouteName() {
    return "registration";
  }
  protected function getPageType() {
    return "registration";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Inscription dans l'unité";
  }
  
  public function manage() {
    
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $pendingRegistrations = Member::where('validated', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->get();
    
    $otherSection = Section::where('id', '!=', $this->section->id)
            ->whereExists(function($query) {
      $query->select(DB::raw(1))
              ->from('members')
              ->where('validated', '=', false)
              ->whereRaw('members.section_id = sections.id');
    })->get();
    
    return View::make('pages.registration.manageRegistration', array(
        'registrations' => $pendingRegistrations,
        'other_sections' => $otherSection,
    ));
    
  }
  
  public function manageSubmit() {
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
    $emailMember = strtolower(Input::get('email_member'));
    $totem = Input::get('totem');
    $quali = Input::get('quali');
    $memberId = Input::get('member_id');
    $familyMembers = Input::get('family_in_other_units');
    $familyDetails = Input::get('family_in_other_units_details');
    $pictureFile = Input::file('picture');
    
    $isLeader = Input::get('is_leader') ? true : false;
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
    
    if ($isLeader) {
      if (!$leaderName)
        $errorMessage .= "Tu dois entrer un nom d'animateur. ";
      elseif (!Helper::hasCorrectCapitals ($leaderName, true))
        $errorMessage .= "L'usage des majuscule dans le nom d'animateur n'est pas correct. ";
    }
    
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
        $member->is_leader = $isLeader;
        $member->leader_name = $leaderName;
        $member->leader_in_charge = $leaderInCharge;
        $member->leader_description = $leaderDescription;
        $member->leader_role = $leaderRole;
        $member->validated = true;

        try {
          $member->save();
          $success = true;
          if ($isLeader) $message = "$firstName $lastName est à présent inscrit en tant qu'animateur.";
          else $message = "$firstName $lastName est à présent inscrit.";
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
    
    // Upload picture
    if ($success && $member && $isLeader && $pictureFile) {
      if (!$pictureFile->getSize()) {
        $success = false;
        $message = "L'animateur a été inscrit, mais la photo n'a pas pu être enregistrée.";
      } else {
        try {
          $image = new Resizer($pictureFile->getRealPath());
          $image->resizeImage(256, 256, "crop");
          $image->saveImage($member->getPicturePath());
          $member->has_picture = true;
          $member->save();
        } catch (Exception $e) {
          $success = false;
          $message = "L'animateur a été inscrit, mais la photo n'a pas pu être enregistrée.";
        }
      }
    }
    
    if ($success)
      return Redirect::to(URL::route('manage_registration', array('section_slug' => $this->section->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())->with($success ? 'success_message' : 'error_message', $message)->withInput();
  }
  
}
