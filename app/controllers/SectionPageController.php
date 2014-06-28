<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * Each section has its own home page. This controller manages them.
 */
class SectionPageController extends GenericPageController {
  
  protected function currentPageAdaptToSections() {
    return $this->isSectionPage();
  }
  
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
    // When visiting the unit presentation page from the main menu, this page is not
    // considered a section page
    $currentRoute = Route::current();
    $currentRouteAction = $currentRoute ? $currentRoute->getAction() : "";
    $currentRouteName = $currentRouteAction ? $currentRouteAction['as'] : "";
    return ($currentRouteName != 'section_unit');
  }
  protected function getPageTitle() {
    if ($this->section->id == 1) return "Présentation de l'unité";
    return $this->section->name;
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_SECTIONS);
  }
  
  /**
   * [Route] Shows the unit presentation page from the main menu
   */
  protected function showUnitPage() {
    $this->section = Section::find(1);
    return $this->showPage();
  }
  
}
