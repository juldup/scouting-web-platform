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
 * The annual feast page is a simple page with content that can be edited by the leaders.
 */
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
