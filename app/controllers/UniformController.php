<?php

class UniformController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "manage_uniform";
  }
  protected function getShowRouteName() {
    return "uniform";
  }
  protected function getPageType() {
    return "section_uniform";
  }
  protected function isSectionPage() {
    return true;
  }
  protected function getPageTitle() {
    return "Uniforme " . $this->section->de_la_section;
  }
  
}
