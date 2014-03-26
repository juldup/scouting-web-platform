<?php

class AnnualFeastController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_annual_feast_page";
  }
  protected function getShowRouteName() {
    return "annual_feast";
  }
  protected function getPageType() {
    return "annual_feast";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Fête d'unité";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_ANNUAL_FEAST);
  }
  
}
