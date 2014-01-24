<?php

class RegistrationController extends GenericPageController {
  
  public function showMain() {
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
      if ($result === true) {
        $success = true;
        $message = "Votre inscription a été enregistrée. L'animateur d'unité la validera prochainement.";
      } else {
        $success = false;
        $message = $result;
        if (!$message) $message = "Une erreur est survenue. Votre inscription n'a pas été enregistrée. " .
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
  
  public function manageRegistration() {
    // Check that the user is allowed to reach this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Helper::forbiddenResponse();
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
    ));
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
      return Helper::forbiddenResponse();
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
    ));
  }
  
  public function ajaxReregister() {
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour réinscrire un scout."));
    }
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    try {
      $member->last_reregistration = date('Y') . '-' . (date('Y') + 1);
      $member->save();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function ajaxCancelReregistration() {
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour annuler la réinscription d'un scout."));
    }
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    try {
      $member->last_reregistration = null;
      $member->save();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function ajaxDeleteMember() {
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL)) {
      return json_encode(array("result" => "Failure", "message" => "Vous n'avez pas les privilèges requis pour désinscrire un scout."));
    }
    $memberId = Input::get('member_id');
    $member = Member::find($memberId);
    if (!$member) return json_encode(array("result" => "Failure", "message" => "Ce membre n'existe pas."));
    try {
      $member->delete();
      return json_encode(array('result' => 'Success'));
    } catch (Exception $ex) {
      return json_encode(array("result" => "Failure", "message" => "Erreur inconnue."));
    }
  }
  
  public function manageYearInSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Render view
    return View::make('pages.registration.manageYearInSection', array(
    ));
  }
  
  public function manageMemberSection() {
    // Make sure the user is allowed to access this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Render view
    return View::make('pages.registration.manageMemberSection', array(
    ));
  }
  
}
