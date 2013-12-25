<?php

class UnitPolicyController extends GenericPageController {
  
  protected function canEdit() {
    return $this->user->can("Modifier les pages #delasection", 1);
  }
  protected function getEditRouteName() {
    return "manage_unit_policy";
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
  
}
