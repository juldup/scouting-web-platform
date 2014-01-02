<?php

class SectionPageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_section_page";
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
