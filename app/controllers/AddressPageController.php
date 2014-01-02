<?php

class AddressPageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_address_page";
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
