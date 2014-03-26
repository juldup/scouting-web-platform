<?php

class HelpPageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_help_page";
  }
  protected function getShowRouteName() {
    return "help";
  }
  protected function getPageType() {
    return "help";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Aide";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_HELP);
  }
  
}
