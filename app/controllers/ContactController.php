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
 * The contact page publicly shows:
 * - the address of the unit's premises (an editable page)
 * - the contact information of the leaders in charge.
 * - a list of external links (see LinkController)
 * 
 * The contacts' e-mail addresses are kept private, and so are the phone numbers that are marked as private.
 */
class ContactController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_address_page";
  }
  protected function getShowRouteName() {
    return "contacts";
  }
  protected function getPageType() {
    return "addresses";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Adresse";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_ADDRESSES);
  }
  
  /**
   * [Route] Shows the contact page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_CONTACTS)) {
      return App::abort(404);
    }
    // Get page
    $page = $this->getPage();
    $pageBody = $page->body_html;
    // Find unit staff
    $unitLeaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', '1')
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'desc')
            ->orderBy('leader_name')
            ->get();
    // Find sections' leaders in charge
    $sections = Section::where('id', '!=', 1)
            ->orderBy('position')
            ->get();
    $sectionLeaders = array();
    foreach ($sections as $section) {
      $leader = Member::where('is_leader', '=', true)
              ->where('leader_in_charge', '=', true)
              ->where('validated', '=', true)
              ->where('section_id', '=', $section->id)
              ->first();
      if ($leader) $sectionLeaders[] = $leader;
    }
    // Get links
    $links = Link::all();
    // Make view
    return View::make('pages.contacts.contacts', array(
        "unitLeaders" => $unitLeaders,
        "sectionLeaders" => $sectionLeaders,
        "webmaster" => array(
            "name" => "Julien Dupuis",
            "phone" => "+32 496 628 600",
        ),
        "links" => $links,
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
        'page_body' => $pageBody,

    ));
  }
  
}
