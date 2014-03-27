<?php

class ParameterController extends BaseController {
  
  function showEdit() {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS, 1)) {
      return Helper::forbiddenResponse();
    }
    $prices = array(
        '1 child' => Parameter::get(Parameter::$PRICE_1_CHILD),
        '1 leader' => Parameter::get(Parameter::$PRICE_1_LEADER),
        '2 children' => Parameter::get(Parameter::$PRICE_2_CHILDREN),
        '2 leaders' => Parameter::get(Parameter::$PRICE_2_LEADERS),
        '3 children' => Parameter::get(Parameter::$PRICE_3_CHILDREN),
        '3 leaders' => Parameter::get(Parameter::$PRICE_3_LEADERS),
    );
    return View::make('pages.parameters.editParameters', array(
        'pages' => $this->getPageList(),
        'registration_active' => Parameter::get(Parameter::$REGISTRATION_ACTIVE),
        'prices' => $prices,
    ));
  }
  
  function submitParameters() {
    // Check that the user can edit the parameters
    if (!$this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Save new prices
    try {
      Parameter::set(Parameter::$PRICE_1_CHILD, Helper::formatCashAmount(Input::get('price_1_child')));
      Parameter::set(Parameter::$PRICE_1_LEADER, Helper::formatCashAmount(Input::get('price_1_leader')));
      Parameter::set(Parameter::$PRICE_2_CHILDREN, Helper::formatCashAmount(Input::get('price_2_children')));
      Parameter::set(Parameter::$PRICE_2_LEADERS, Helper::formatCashAmount(Input::get('price_2_leaders')));
      Parameter::set(Parameter::$PRICE_3_CHILDREN, Helper::formatCashAmount(Input::get('price_3_children')));
      Parameter::set(Parameter::$PRICE_3_LEADERS, Helper::formatCashAmount(Input::get('price_3_leaders')));
    } catch (Exception $e) {
      $error = true;
    }
    // Save active registration parameter
    $registration_active = Input::get('registration_active');
    try {
      Parameter::set(Parameter::$REGISTRATION_ACTIVE, $registration_active ? "true" : "false");
    } catch (Exception $e) {
      $error = true;
    }
    // Save the page parameters
    $pages = $this->getPageList();
    $error = false;
    foreach ($pages as $page=>$pageData) {
      $pageInput = Input::get($page);
      try {
        Parameter::set($pageData['parameter_name'], $pageInput ? "true" : "false");
      } catch (Exception $e) {
        $error = true;
      }
    }
    // Return to parameter page
    if (!$error) {
      return Redirect::route('edit_parameters')
              ->with('success_message', 'Les paramètres ont été enregistrés avec succès.');
    } else {
      return Redirect::route('edit_parameters')
              ->with('error_message', 'Une erreur est survenue. Tous les paramètres n\'ont peut-être pas été enregistrés.');
    }
  }
  
  function getPageList() {
    $pages = array(
        // Welcome
        'page_sections' => array(
            'description' => 'Afficher les pages des sections',
            'parameter_name' => Parameter::$SHOW_SECTIONS
        ),
        'page_addresses' => array(
            'description' => 'Afficher la page "adresses utiles',
            'parameter_name' => Parameter::$SHOW_ADDRESSES
        ),
        'page_contacts' => array(
            'description' => 'Afficher la page de contacts',
            'parameter_name' => Parameter::$SHOW_CONTACTS
        ),
        // General
        'page_annual_feast' => array(
            'description' => "Afficher la page d'inscription à la fête d'unité",
            'parameter_name' => Parameter::$SHOW_ANNUAL_FEAST
        ),
        'page_registration' => array(
            'description' => "Afficher la page \"inscription\"",
            'parameter_name' => Parameter::$SHOW_REGISTRATION
        ),
        'page_health_cards' => array(
            'description' => "Afficher la page \"fiches santé\"",
            'parameter_name' => Parameter::$SHOW_HEALTH_CARDS
        ),
        'page_unit_policy' => array(
            'description' => "Afficher la page \"charte d'unité\"",
            'parameter_name' => Parameter::$SHOW_UNIT_POLICY
        ),
        'page_uniforms' => array(
            'description' => "Afficher la page \"uniformes\"",
            'parameter_name' => Parameter::$SHOW_UNIFORMS
        ),
        'page_links' => array(
            'description' => "Afficher la page \"liens utiles\"",
            'parameter_name' => Parameter::$SHOW_LINKS
        ),
        // Animation
        'page_news' => array(
            'description' => "Afficher la page \"nouvelles\"",
            'parameter_name' => Parameter::$SHOW_NEWS
        ),
        'page_calendar' => array(
            'description' => 'Afficher la page "calendrier"',
            'parameter_name' => Parameter::$SHOW_CALENDAR
        ),
        'page_documents' => array(
            'description' => 'Afficher la page "télécharger"',
            'parameter_name' => Parameter::$SHOW_DOCUMENTS
        ),
        'page_emails' => array(
            'description' => 'Afficher la page "e-mails"',
            'parameter_name' => Parameter::$SHOW_EMAILS
        ),
        'page_photos' => array(
            'description' => "Afficher la page \"photos\"",
            'parameter_name' => Parameter::$SHOW_PHOTOS
        ),
        'page_leaders' => array(
            'description' => "Afficher la page \"animateurs\"",
            'parameter_name' => Parameter::$SHOW_LEADERS
        ),
        'page_listing' => array(
            'description' => "Afficher la page \"listing\"",
            'parameter_name' => Parameter::$SHOW_LISTING
        ),
        // Your opinion
        'page_suggestions' => array(
            'description' => "Afficher la page \"suggestions\"",
            'parameter_name' => Parameter::$SHOW_SUGGESTIONS
        ),
        'page_guest_book' => array(
            'description' => "Afficher le livre d'or",
            'parameter_name' => Parameter::$SHOW_GUEST_BOOK
        ),
        // Help
        'page_help' => array(
            'description' => "Afficher la page \"aide\"",
            'parameter_name' => Parameter::$SHOW_HELP
        ),
    );
    foreach ($pages as $page=>$pageData) {
      $pageData['active'] = Parameter::get($pageData['parameter_name']);
      $pages[$page] = $pageData;
    }
    return $pages;
  }
  
}