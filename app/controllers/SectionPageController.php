<?php

/**
 * Each section has its own home page. This controller manages them.
 */
class SectionPageController extends GenericPageController {
  
  protected $pagesAdaptToSections = true;
  
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
    if ($this->section->id == 1) return "Présentation de l'unité";
    return $this->section->name;
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_SECTIONS);
  }
  
}
