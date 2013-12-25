<?php

class AddressesController extends GenericPageController {
  
  protected function canEdit() {
    return $this->user->can("Modifier les pages #delasection", 1);
  }
  protected function getEditRouteName() {
    return "manage_addresses";
  }
  protected function getShowRouteName() {
    return "addresses";
  }
  protected function getPageType() {
    return "addresses";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Adresses utiles";
  }
  
}
