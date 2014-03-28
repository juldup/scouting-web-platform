<?php

class RegistrationController extends GenericPageController {
  
  public function showMain() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_REGISTRATION)) {
      return App::abort(404);
    }
    
    if (Parameter::get(Parameter::$REGISTRATION_ACTIVE)) {
      $page = $this->getPage();
      $pageBody = $page->body_html;
      $pageBody = str_replace("(PRIX UN ENFANT)", Parameter::get(Parameter::$PRICE_1_CHILD), $pageBody);
      $pageBody = str_replace("(PRIX DEUX ENFANTS)", Parameter::get(Parameter::$PRICE_2_CHILDREN), $pageBody);
      $pageBody = str_replace("(PRIX TROIS ENFANTS)", Parameter::get(Parameter::$PRICE_3_CHILDREN), $pageBody);
      $pageBody = str_replace("(PRIX UN ANIMATEUR)", Parameter::get(Parameter::$PRICE_1_LEADER), $pageBody);
      $pageBody = str_replace("(PRIX DEUX ANIMATEURS)", Parameter::get(Parameter::$PRICE_2_LEADERS), $pageBody);
      $pageBody = str_replace("(PRIX TROIS ANIMATEURS)", Parameter::get(Parameter::$PRICE_3_LEADERS), $pageBody);
      $pageBody = str_replace("BEXX-XXXX-XXXX-XXXX", Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), $pageBody);
      $pageBody = str_replace("(ACCES CHARTE)", '<a href="' . URL::route('unit_policy') . '">charte d&apos;unité</a>', $pageBody);
      $pageBody = str_replace("(ACCES CONTACT)", '<a href="' . URL::route('contacts') . '">contact</a>', $pageBody);
      $pageBody = str_replace("(ACCES FORMULAIRE)", '<a href="' . URL::route('registration_form') . '">formulaire d&apos;inscription</a>', $pageBody);
      
      $familyMembers = array();
      if ($this->user->isMember()) {
        $familyMembers = $this->user->getAssociatedMembers();
      }
      
      return View::make('pages.registration.registrationMain', array(
          'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, $this->section),
          'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
          'page_title' => $this->getPageTitle(),
          'page_body' => $pageBody,
          'family_members' => $familyMembers,
          'reregistration_year' => date('Y') . "-" . (date('Y') + 1),
      ));
    } else {
      return View::make('pages.registration.registrationInactive', array(
          'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                              || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)
                              || $this->user->can(Privilege::$SECTION_TRANSFER, 1),
          'page_title' => $this->getPageTitle(),
      ));
    }
  }
  
  public function reregister($member_id) {
    if (!$this->user->isOwnerOfMember($member_id)) {
      return Helper::forbiddenResponse();
    }
    $member = Member::find($member_id);
    if (!$member) return App::abort(404, "Ce member n'existe pas.");
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      return Redirect::route('registration')->with('success_message', "La réinscription de " . $member->first_name . " " . $member->last_name . " a été enregistrée.");
    } catch (Exception $ex) {
      return Redirect::route('registration')->with('error_message', "Une erreur est survenue. La réinscription de "
              . $member->first_name . " " . $member->last_name . " n'a pas été enregistrée. Contactez l'animateur d'unité. $ex");
    }
  }
  
  public function showForm() {
    return View::make('pages.registration.registrationForm', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
    ));
  }
  
  public function submit() {
    $policyAgreement = Input::get('policy_agreement') ? true : false;
    
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY) && !$policyAgreement) {
      $success = false;
      $message = "Vous devez adhérer à la charte d'unité pour inscrire un enfant.";
    } else {
      $result = Member::createFromInput(false);
      if (is_string($result)) {
        // An error has occured
        $success = false;
        $message = $result;
        if (!$message) $message = "Une erreur est survenue. Votre inscription n'a pas été enregistrée. " .
                "Veuillez réessayer ou <a href='" . URL::route('contacts') . 
                "'>contacter l'animateur d'unité</a>.";
      } else {
        $success = true;
        $message = "Votre inscription a été enregistrée. L'animateur d'unité la validera prochainement.";
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
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_REGISTRATION);
  }
  
  public function manageRegistration() {
    // Check that the user is allowed to reach this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Redirect::route('manage_reregistration');
    }
    // Gather pending registrations
    $pendingRegistrations = Member::where('validated', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->get();
    // List other sections that contain pending registrations
    $otherSection = Section::where('id', '!=', $this->section->id)
            ->whereExists(function($query) {
              $query->select(DB::raw(1))
                      ->from('members')
                      ->where('validated', '=', false)
                      ->whereRaw('members.section_id = sections.id');
            })->get();
    // Render view
    return View::make('pages.registration.manageRegistration', array(
        'registrations' => $pendingRegistrations,
        'other_sections' => $otherSection,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
    ));
  }
  
  public function deleteRegistration($member_id) {
    $member = Member::find($member_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      try {
        $member->delete();
        return Redirect::route('manage_registration')
                ->with("success_message", "La demande d'inscription de " . $member->first_name . " " . $member->last_name . " a été supprimée.");
      } catch (Exception $ex) {
      }
    }
    return Redirect::route('manage_registration')
                ->with("error_message", "Une erreur est survenue. La demande d'inscription n'a pas été supprimée. $memberId");
  }
  
  public function manageSubmit() {
    // Get input data
    $sectionId = Input::get('section_id');
    $memberId = Input::get('member_id');
    // Find member
    $member = Member::find($memberId);
    if ($member) {
      // Make sure the user is allowed to change member data
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id) ||
              !$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Update member
      $result = $member->updateFromInput(true, true, true, true, true);
      // Create result message
      if ($result === true) {
        $success = true;
        $name = $member->first_name . " " . $member->last_name;
        if ($member->is_leader) {
          $message = "$name est à présent inscrit en tant qu'animateur.";
        } else {
          $message = "$name est à présent inscrit.";
        }
      } else {
        $success = false;
        $message = $result ? $result : "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
      }
    } else {
      // Member not found
      $success = false;
      $message = "Une erreur est survenue. Le nouveau membre n'a pas été inscrit.";
    }
    // Redirect to page with result message
    if ($success)
      return Redirect::to(URL::route('manage_registration', array('section_slug' => $this->section->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())->with($success ? 'success_message' : 'error_message', $message)->withInput();
  }
  
  public function manageReregistration() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Redirect::route('manage_year_in_section');
    }
    // List scouts
    $query = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('last_name')
            ->orderBy('first_name');
    if ($this->section->id != 1) {
      $query->where('section_id', '=', $this->section->id);
    }
    $activeMembers = $query->get();
    // Render view
    return View::make('pages.registration.manageReregistration', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
    ));
  }
  
  public function ajaxReregister() {
    // Find member
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privileges
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour réinscrire un scout."));
    }
    // Update last reregistration year
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function ajaxCancelReregistration() {
    // Find member
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privileges
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour annuler la réinscription d'un scout."));
    }
    // Cancel last reregistration year
    try {
      $member->last_reregistration = null;
      $member->save();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function ajaxDeleteMember() {
    // Find member
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    // Check privilege
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section_id)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour désinscrire un scout."));
    }
    // Delete member
    try {
      $member->delete();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function manageYearInSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)) {
      return Redirect::route('manage_member_section');
    }
    // List scouts
    $activeMembers = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('year_in_section', 'DESC')
            ->orderBy('birth_date')
            ->where('section_id', '=', $this->section->id)
            ->get();
    // Render view
    return View::make('pages.registration.manageYearInSection', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
    ));
  }
  
  public function ajaxUpdateYearInSection() {
    if (Input::has('section_id')) {
      $sectionId = Input::get('section_id');
      // Make sure the user is allowed to access this page
      if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId)) {
        return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour changer l'année d'un scout."));
      }
      try {
        // Update year for each member of the section
        Member::where('validated', '=', true)
                ->where('is_leader', '=', false)
                ->where('section_id', '=', $sectionId)
                ->increment('year_in_section');
        // Retrieve members
        $members = Member::where('validated', '=', true)
                ->where('is_leader', '=', false)
                ->where('section_id', '=', $sectionId)
                ->get();
        $memberYears = array();
        foreach ($members as $member) {
          $memberYears[$member->id] = $member->year_in_section;
        }
        return json_encode(array("result" => "Success", 'years' => $memberYears));
      } catch (Exception $ex) {
        return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
      }
    } else {
      // Find member
      $memberId = Input::get('member_id');
      $member = Member::find($memberId);
      if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
      // Get year in section
      $yearInSection = Input::get('year') + 0;
      if ($yearInSection <= 0) {
        return json_encode(array("result" => "Failure", "message" => "L'année doit être un nombre positif."));
      }
      // Make sure the user is allowed to access this page
      if (!$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $member->section_id)) {
        return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour changer l'année d'un scout."));
      }
      // Update year in section
      try {
        $member->year_in_section = $yearInSection;
        $member->save();
        return json_encode(array('result' => 'Success'));
      } catch (Exception $ex) {
        return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
      }
    }
  }
  
  public function manageMemberSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section) || $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
        return Redirect::route('manage_registration');
      }
      return Helper::forbiddenResponse();
    }
    // List scouts
    $activeMembers = Member::where('validated', '=', true)
            ->where('is_leader', '=', false)
            ->orderBy('year_in_section', 'DESC')
            ->orderBy('birth_date')
            ->where('section_id', '=', $this->section->id)
            ->get();
    // Render view
    return View::make('pages.registration.manageMemberSection', array(
        'active_members' => $activeMembers,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
    ));
  }
  
  public function submitUpdateSection($section_slug) {
    $sectionFrom = Section::where('slug', '=', $section_slug)->first();
    $sectionTo = Section::find(Input::get('destination'));
    $memberIdsToTransfer = Input::get('members');
    if (!$sectionFrom || !$sectionTo || !is_array($memberIdsToTransfer) || !count($memberIdsToTransfer)) {
      return Redirect::route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Une erreur est survenue. Les changements n'ont pas été enregistrés");
    }
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      return Helper::forbiddenResponse();
    }
    $errorList = "";
    $success = false;
    foreach ($memberIdsToTransfer as $memberId=>$val) {
      $member = Member::find($memberId);
      if ($member && $member->section_id == $sectionFrom->id) {
        try {
          $member->section_id = $sectionTo->id;
          $member->year_in_section = 1;
          $member->subgroup = null;
          $member->save();
          $success = true;
        } catch (Exception $ex) {
          $errorList .= ($errorList ? ", " : "") . $member->first_name . " " . $member->last_name;
        }
      } else {
        $errorList .= ($errorList ? ", " : "") . $member->first_name . " " . $member->last_name;
      }
    }
    if (!$success) {
      return Redirect::route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Une erreur s'est produite. Les changements n'ont pas été enregistrés.");
    } elseif ($errorList) {
      return Redirect::route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Le transfert a été opéré, sauf pour : $errorList");
    } else {
      return Redirect::route('manage_member_section', array('section_slug' => $section_slug))
              ->with('success_message', "Le transfert a été opéré avec succès.");
    }
    
  }
  
}
