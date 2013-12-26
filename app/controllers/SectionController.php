<?php

class SectionController extends GenericPageController {
  
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
