<?php

class SectionController extends GenericPageController {
  
  protected function canEdit() {
    return $this->user->can("Modifier les pages #delasection", View::shared('user')->currentSection);
  }
  protected function getEditRouteName() {
    return "manage_section";
  }
  protected function getShowRouteName() {
    return "section";
  }
  protected function getPageType() {
    return "section_home";
  }
  protected function isSectionPage() {
    return true;
  }
  protected function getPageTitle() {
    return $this->section->name;
  }
  
}
