<?php

class HomeController extends GenericPageController {
  
  protected function canEdit() {
    return $this->user->can("Modifier les pages #delasection", 1);
  }
  protected function getEditRouteName() {
    return "manage_home";
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
  
  public function showPage() {
    $routeParameters = Route::current()->parameters();
    if (array_key_exists("section_slug", $routeParameters)) {
      if ($this->section->id != 1) {
        return Illuminate\Http\RedirectResponse::create(URL::route('section', array('section_slug' => $routeParameters['section_slug'])));
      }
    }
    return parent::showPage();
  }
  
}
