<?php

/**
 * The home page is a unit-wide simple page with content that can be edited by the leaders.
 * 
 * This class also provides a route to get the website logo.
 */
class HomePageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_home_page";
  }
  protected function getShowRouteName() {
    return "home";
  }
  protected function getPageType() {
    return "home";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "";
  }
  protected function canDisplayPage() {
    return true;
  }
  
  /**
   * Override variable in parent: this is the home page
   */
  protected $isHomePage = true;
  
  /**
   * [Route] Overrides the parent's showPage method to redirect to a specific section's
   * home page if a section is selected via the section menu
   */
  public function showPage() {
    $routeParameters = Route::current()->parameters();
    if (array_key_exists("section_slug", $routeParameters)) {
      if ($this->section->id != 1) {
        // Redirect to a section's home page
        return Redirect::route('section', array('section_slug' => $routeParameters['section_slug']));
      }
    }
    // No section specified, show page normally
    return parent::showPage();
  }
  
  /**
   * [Route] Returns the website's logo image
   */
  public function websiteLogo() {
    $logoName = Parameter::get(Parameter::$LOGO_IMAGE);
    if ($logoName) {
      $path = storage_path(Parameter::$LOGO_IMAGE_FOLDER . $logoName);
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
}
