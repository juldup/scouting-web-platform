<?php

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
  
  protected $isHomePage = true;
  
  public function showPage() {
    $routeParameters = Route::current()->parameters();
    if (array_key_exists("section_slug", $routeParameters)) {
      if ($this->section->id != 1) {
        return Illuminate\Http\RedirectResponse::create(URL::route('section', array('section_slug' => $routeParameters['section_slug'])));
      }
    }
    return parent::showPage();
  }
  
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
