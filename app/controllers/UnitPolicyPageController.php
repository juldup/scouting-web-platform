<?php

class UnitPolicyPageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_unit_policy_page";
  }
  protected function getShowRouteName() {
    return "unit_policy";
  }
  protected function getPageType() {
    return "unit_policy";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Charte d'unité";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_UNIT_POLICY);
  }
  
}
