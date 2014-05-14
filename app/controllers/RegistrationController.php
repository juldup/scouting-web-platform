<?php

/**
 * Future members can register as scouts or leaders through a registration form.
 * The leaders can then validate the registration.
 * 
 * This controller also allows the leaders to manage reregistrations, transfers between
 * sections, members' year in the section and check subscription fee payment.
 */
class RegistrationController extends GenericPageController {
  
  protected function currentPageAdaptToSections() {
    return $this->user->isLeader();
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
  
  /**
   * [Route] Shows the public registration information page
   */
  public function showMain() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_REGISTRATION)) {
      return App::abort(404);
    }
    if (Parameter::get(Parameter::$REGISTRATION_ACTIVE)) {
      // The registrations are active (i.e. people can register)
      // Get page text and update it with the parametric values
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
      // Get the list of members owned by the user for the reregistration form
      $familyMembers = array();
      if ($this->user->isMember()) {
        $familyMembers = $this->user->getAssociatedMembers();
      }
      // Make view
      return View::make('pages.registration.registrationMain', array(
          'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, $this->section),
          'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
          'page_title' => $this->getPageTitle(),
          'page_body' => $pageBody,
          'family_members' => $familyMembers,
          'reregistration_year' => date('Y') . "-" . (date('Y') + 1),
      ));
    } else {
      // The registration are not active, show a default page
      return View::make('pages.registration.registrationInactive', array(
          'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                              || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)
                              || $this->user->can(Privilege::$SECTION_TRANSFER, 1),
          'page_title' => $this->getPageTitle(),
      ));
    }
  }
  
  /**
   * [Route] Marks a member as reregistered for the next year
   */
  public function reregister($member_id) {
    // Make sure the user owns this member
    if (!$this->user->isOwnerOfMember($member_id)) {
      return Helper::forbiddenResponse();
    }
    // Get the member
    $member = Member::find($member_id);
    if (!$member) return App::abort(404, "Ce member n'existe pas.");
    // Update reregistration status
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      return Redirect::route('registration')->with('success_message', "La réinscription de " . $member->first_name . " " . $member->last_name . " a été enregistrée.");
    } catch (Exception $ex) {
      return Redirect::route('registration')->with('error_message', "Une erreur est survenue. La réinscription de "
              . $member->first_name . " " . $member->last_name . " n'a pas été enregistrée. Contactez l'animateur d'unité. $ex");
    }
  }
  
  /**
   * [Route] Displays the registration form page
   */
  public function showForm() {
    return View::make('pages.registration.registrationForm', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
    ));
  }
  
  /**
   * [Route] Called when the registration form is submitted
   */
  public function submit() {
    // Get whether the policy agreement has been accepted
    $policyAgreement = Input::get('policy_agreement') ? true : false;
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY) && !$policyAgreement) {
      // The policy agreement has not been accepted, set error message
      $success = false;
      $message = "Vous devez adhérer à la charte d'unité pour inscrire un enfant.";
    } else {
      // The policy agreement has been accepted, create a new member instance from input
      $result = Member::createFromInput(false);
      if (is_string($result)) {
        // An error has occured
        $success = false;
        $message = $result;
        if (!$message) $message = "Une erreur est survenue. Votre inscription n'a pas été enregistrée. " .
                "Veuillez réessayer ou <a href='" . URL::route('contacts') . 
                "'>contacter l'animateur d'unité</a>.";
      } else {
        // Success
        $success = true;
        $message = "Votre inscription a été enregistrée. L'animateur d'unité la validera prochainement.";
      }
    }
    // Redirect with status message
    if ($success)
      return Redirect::to(URL::route('registration_form'))
            ->with('success_message', $message);
    else
      return Redirect::to(URL::route('registration_form'))
            ->with('error_message', $message)
            ->withInput();
  }
  
  /**
   * [Route] Shows the registration management page, where the leaders
   * can validate or cancel pending registrations
   */
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
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Deletes a pending registration
   */
  public function deleteRegistration($member_id) {
    // Get the pending registration to delete
    $member = Member::find($member_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      // Make sure the leader can cancel registrations
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Remove pending registration
      try {
        $member->delete();
        return Redirect::route('manage_registration')
                ->with("success_message", "La demande d'inscription de " . $member->first_name . " " . $member->last_name . " a été supprimée.");
      } catch (Exception $ex) {
      }
    }
    // An error has occured
    return Redirect::route('manage_registration')
                ->with("error_message", "Une erreur est survenue. La demande d'inscription n'a pas été supprimée. $memberId");
  }
  
  /**
   * [Route] Used to submit a member data and validate their registration
   */
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
      // Update member with input data
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
  
  /**
   * [Route] Shows the reregistration management page
   */
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
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to mark a member as reregistered for the next year
   */
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
  
  /**
   * [Route] Ajax call to cancel a reregistration
   */
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
  
  /**
   * [Route] Ajax call to delete a member from the reregistration page
   */
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
  
  /**
   * [Route] Shows the year in section management page
   */
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
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to change the year of a single member or of all members of a section
   * @return type
   */
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
  
  /**
   * [Route] Shows the section transfer page
   */
  public function manageMemberSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      return Redirect::route('manage_subscription_fee');
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
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Used to submit the transfers from a section to another
   */
  public function submitUpdateSection($section_slug) {
    // Get sections from and to
    $sectionFrom = Section::where('slug', '=', $section_slug)->first();
    $sectionTo = Section::find(Input::get('destination'));
    // Get list of members to transfer
    $memberIdsToTransfer = Input::get('members');
    // Make sure these parameters are all set correctly
    if (!$sectionFrom || !$sectionTo || !is_array($memberIdsToTransfer) || !count($memberIdsToTransfer)) {
      return Redirect::route('manage_member_section', array('section_slug' => $section_slug))
              ->with('error_message', "Une erreur est survenue. Les changements n'ont pas été enregistrés");
    }
    // Make sure the user can operate transfers between sections
    if (!$this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
      return Helper::forbiddenResponse();
    }
    // Transfer each member and reset their year in the section and their subgroup name
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
    // Redirect with status message
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
  
  /**
   * [Route] Shows the subscription fee management page, where the leaders
   * can check members that have paid their fee
   */
  public function manageSubscriptionFee() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$MANAGE_ACCOUNTING, 1)) {
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)
              || $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
              || $this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
        return Redirect::route('manage_registration');
      }
      return Helper::forbiddenResponse();
    }
    // List scouts
    $members = Member::where('validated', '=', true)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    // Render view
    return View::make('pages.registration.manageSubscriptionFee', array(
        'members' => $members,
        'can_manage_registration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'can_manage_reregistration' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_manage_year_in_section' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_manage_member_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_manage_subscription_fee' => $this->user->can(Privilege::$MANAGE_ACCOUNTING, 1),
    ));
  }
  
  /**
   * [Route] Ajax call to toggle a list of fee payment status
   */
  public function updateSubscriptionFee() {
    // Make sure the user is allowed to change the fee payment status
    if (!$this->user->can(Privilege::$MANAGE_ACCOUNTING, 1)) {
      return json_encode(array('result' => 'Failure'));
    }
    // Get list of changes
    $changes = Input::all();
    // Apply changes
    $error = false;
    $message = "";
    foreach ($changes as $memberId => $state) {
      // Get member and payment status
      $memberId = substr($memberId, strlen('member-'));
      $state = $state != "false" && $state;
      $member = Member::find($memberId);
      // Update status
      if ($member) {
        try {
          $member->subscription_paid = $state;
          $member->save();
        } catch (Exception $e) {
          $error = true;
          $message .= "$e ";
        }
      } else {
        $error = true;
        $message .= "Member $memberId does not exist. ";
      }
    }
    // Redirect with status message
    return json_encode(array('result' => $error ? "Failure" : "Success", 'message' => $message));
  }
  
}
