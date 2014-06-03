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
 * The help page is a simple page with content that can be edited by the leaders.
 */
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
