<?php

class BaseController extends Controller {
  
  /**
   * Setup the layout used by the controller.
   *
   * @return void
   */
  protected function setupLayout() {
    if (!is_null($this->layout)) {
      $this->layout = View::make($this->layout);
    }
  }
  
  protected function preExecute($section_slug = "") {
    $user = User::disconnectedUser();
    $this->selectSection($user, $section_slug);
    View::share('user', $user);
  }
  
  protected function selectSection($user, $section_slug) {
    // Determine currently selected section
    $section = null;
    // A specific section is selected
    if ($section_slug) {
      echo "Using slug";
      $sections = Section::where('slug', '=', $section_slug)->get();
      if (count($sections)) {
        $section = $sections[0];
      }
      var_dump($section);
    }
    // Use section from current session
    if ($section == null && Session::has('currentSection')) {
      echo "Using session";
      $section = Section::find(Session::get('currentSection', '1'));
    }
    // Use default section for the user
    if ($section == null && isset($user->default_section)) {
      echo "Using user";
      $section = Section::find($user->default_section);
    }
    // Use main section
    if ($section == null) {
      echo "Using default";
      $section = Section::find(1);
    }
    $user->currentSection = $section;
    // Save selected section to session
    Session::put('currentSection', $section->id);
  }
  
  protected function checkAccessToGestion() {
    return true; // TODO Check if user is an animator
  }
  
}