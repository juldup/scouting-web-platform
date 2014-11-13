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
 * This composer generates the navigation menu of the website
 */
class MenuComposer {
  
  public function compose($view) {
    
    // Get current route to set current menu item as active
    $currentRoute = Route::current();
    $currentRouteAction = $currentRoute ? $currentRoute->getAction() : "";
    $currentRouteParameters = $currentRoute->parameters();
    $currentRouteName = $currentRouteAction ? $currentRouteAction['as'] : "";
    
    // Generate menus
    $mainMenu = $this->generateMainMenu($currentRouteName);
    $leaderMenu = $this->generateLeaderMenu($currentRouteName);
    $sectionList = $this->generateSectionList($currentRouteName, $currentRouteParameters);
    $sectionMenuItems = $this->generateSectionMenu($currentRouteName);
    
    // Pass the menu arrays to the view
    $view->withMainMenu($mainMenu)
            ->withLeaderMenu($leaderMenu)
            ->withSectionList($sectionList)
            ->withSectionMenuItems($sectionMenuItems)
            ->withGlobalNewsSelected($currentRouteName === "global_news");
  }
  
  /**
   * Generate the main menu
   */
  private function generateMainMenu($currentRouteName) {
    // Home category
    $homeCategory = array();
    if (Parameter::get(Parameter::$SHOW_SECTIONS))
      $homeCategory["Présentation"] = 'section_unit';
    if (Parameter::get(Parameter::$SHOW_UNIT_POLICY))
      $homeCategory["Charte d'unité"] = 'unit_policy';
    if (Parameter::get(Parameter::$SHOW_CONTACTS))
      $homeCategory["Contacts" . (Parameter::get(Parameter::$SHOW_LINKS) ? " et liens" : "")] = 'contacts';
    if ((Parameter::get(Parameter::$SHOW_SECTIONS) || Parameter::get(Parameter::$SHOW_UNIT_POLICY) || Parameter::get(Parameter::$SHOW_CONTACTS)) &&
        (Parameter::get(Parameter::$SHOW_REGISTRATION) || Parameter::get(Parameter::$SHOW_HEALTH_CARDS)))
      $homeCategory["divider_1"] = "divider";
    if (Parameter::get(Parameter::$SHOW_REGISTRATION))
      $homeCategory["Inscription"] = 'registration';
    if (Parameter::get(Parameter::$SHOW_HEALTH_CARDS))
      $homeCategory["Fiches santé"] = 'health_card';
    if (Parameter::get(Parameter::$SHOW_ANNUAL_FEAST))
      $homeCategory["Fête d'unité"] = 'annual_feast';
    $homeCategory["divider_2"] = 'divider';
    $homeCategory["Nouveautés"] = 'view_recent_changes';
    if (Parameter::get(Parameter::$SHOW_SUGGESTIONS))
      $homeCategory["Suggestions"] = 'suggestions';
    if (Parameter::get(Parameter::$SHOW_GUEST_BOOK))
      $homeCategory["Livre d'or"] = 'guest_book';
    if (Parameter::get(Parameter::$SHOW_HELP))
      $homeCategory["Aide"] = 'help';
    return $this->convertMenuItems($homeCategory, $currentRouteName);
  }
  
  private function generateLeaderMenu($currentRouteName) {
    // Leader corner category
    $user = View::shared('user');
    if (!$user->isLeader()) return null;
    
    $leaderCategory = array();
    $leaderCategory["Coin des animateurs"] = 'leader_corner';
//      $leaderCategory["Aide sur la gestion du site"] = 'leader_help';
    
    $leaderCategory["Opérations courantes"] = 'title';
    if (Parameter::get(Parameter::$SHOW_CALENDAR)) {
      $leaderCategory['Gérer le calendrier'] = $user->can(Privilege::$EDIT_CALENDAR) ? 'manage_calendar' : null;
      $leaderCategory[$user->can(Privilege::$MANAGE_ATTENDANCE) ? 'Gérer les présences' : 'Voir les présences'] = 'edit_attendance';
    }
    if (Parameter::get(Parameter::$SHOW_PHOTOS))
      $leaderCategory['Gérer les photos'] = $user->can(Privilege::$POST_PHOTOS) ? 'edit_photos' : null;
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS))
      $leaderCategory['Gérer les documents'] = $user->can(Privilege::$EDIT_DOCUMENTS) ? 'manage_documents' : null;
    if (Parameter::get(Parameter::$SHOW_NEWS))
      $leaderCategory['Gérer les actualités'] = $user->can(Privilege::$EDIT_NEWS) ? 'manage_news' : null;
    if (Parameter::get(Parameter::$SHOW_EMAILS))
      $leaderCategory['Gérer les e-mails'] = $user->can(Privilege::$SEND_EMAILS) ? 'manage_emails' : null;
    $leaderCategory['Envoyer un e-mail aux parents'] = $user->can(Privilege::$SEND_EMAILS) ? 'send_section_email' : null;
    $leaderCategory['Envoyer un e-mail aux animateurs'] = 'send_leader_email';
    if (Parameter::get(Parameter::$SHOW_HEALTH_CARDS))
      $leaderCategory['Gérer les fiches santé'] = $user->can(Privilege::$VIEW_HEALTH_CARDS) ? 'manage_health_cards' : null;
    $leaderCategory['Trésorerie'] = 'accounting';
    
    $leaderCategory['Opérations annuelles'] = 'title';
    $leaderCategory['Gérer les inscriptions'] =
            $user->can(Privilege::$EDIT_LISTING_ALL) || $user->can(Privilege::$EDIT_LISTING_LIMITED) ||
            $user->can(Privilege::$SECTION_TRANSFER || $user->can(Privilege::$MANAGE_ACCOUNTING))
            ? 'manage_registration' : null;
    $leaderCategory['Gérer le listing'] =
            $user->can(Privilege::$EDIT_LISTING_ALL) || $user->can(Privilege::$EDIT_LISTING_LIMITED) ? 'manage_listing' : null;
    $leaderCategory['Gérer les animateurs'] = 'edit_leaders';
    $leaderCategory['Privilèges des animateurs'] = 'edit_privileges';
    $leaderCategory['Gérer les sections'] = 'section_data';
    
    $leaderCategory['Contenu du site'] = 'title';
    $leaderCategory["Paramètres du site"] = $user->can(Privilege::$EDIT_GLOBAL_PARAMETERS) ? 'edit_parameters' : null;
    
    $leaderCategory['Supervision'] = 'title';
//      $leaderCategory['Changements récents'] = 'view_private_recent_changes';
    $leaderCategory['Liste des utilisateurs du site'] = 'user_list';
    if (Parameter::get(Parameter::$SHOW_SUGGESTIONS))
      $leaderCategory['Gérer les suggestions'] = $user->can(Privilege::$MANAGE_SUGGESIONS) ? 'edit_suggestions' : null;
    if (Parameter::get(Parameter::$SHOW_GUEST_BOOK))
      $leaderCategory["Gérer le livre d'or"] = $user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES) ? 'edit_guest_book' : null;
    $leaderCategory["Logs"] = 'logs';
    
    return $this->convertMenuItems($leaderCategory, $currentRouteName);
  }
  
  /**
   * Generate the top part of the section menu containing the section list
   */
  private function generateSectionList($currentRouteName, $routeParameters) {
    // Get current section
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    // Generate section items with transposed route parameters to match the section
    $sections = Section::orderBy('position')->get();
    $sectionList = array();
    foreach ($sections as $section) {
      $routeParameters['section_slug'] = $section->slug;
      $sectionList[] = array(
          // Transpose route for section pages, url to section main page otherwise
          "link" => View::shared('section_page') ? URL::route($currentRouteName, $routeParameters) : URL::route('section', array('section_slug' => $section->slug)),
          "text" => $section->id == 1 ? "Toute l'unité" : $section->name,
          "is_selected" => $selectedSectionId == $section->id,
          "color" => $section->color,
      );
    }
    
    return $sectionList;
  }
  
  /**
   * Generate the bottom part of the section menu containing access the the section pages
   */
  private function generateSectionMenu($currentRouteName) {
    // Section category
    $sectionMenuItems = array();
    if (Parameter::get(Parameter::$SHOW_NEWS))
      $sectionMenuItems["Actualités de la section"] = 'news';
    if (Parameter::get(Parameter::$SHOW_CALENDAR))
      $sectionMenuItems["Calendrier"] = 'calendar';
    if (Parameter::get(Parameter::$SHOW_LEADERS))
      $sectionMenuItems["Les animateurs"] = 'leaders';
    if (Parameter::get(Parameter::$SHOW_PHOTOS))
      $sectionMenuItems["Photos"] = 'photos';
    if (Parameter::get(Parameter::$SHOW_EMAILS))
      $sectionMenuItems["E-mails"] = 'emails';
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS))
      $sectionMenuItems["Télécharger"] = 'documents';
    if (Parameter::get(Parameter::$SHOW_LISTING))
      $sectionMenuItems["Listing"] = 'listing';
    if (Parameter::get(Parameter::$SHOW_UNIFORMS))
      $sectionMenuItems["Les uniformes"] = 'uniform';
    $menu = $this->convertMenuItems($sectionMenuItems, $currentRouteName);
    return $menu['items'];
  }
  
  /**
   * Transforms a simple list of menu items to a list of menu items with attributes
   */
  private function convertMenuItems($menuItems, $currentRouteName) {
    // Generate menu structure (with sub-category titles, dividers and items' route)
    $items = array();
    $submenuActive = false;
    foreach ($menuItems as $itemName => $itemData) {
      $route = $itemData != 'divider' && $itemData != 'title' ? $itemData : null;
      $active = $route == $currentRouteName;
      if ($active) $submenuActive = true;
      $items[$itemName] = array(
          'url' => $route ? URL::route($route) : null,
          'active' => $active,
          'is_divider' => $itemData == "divider",
          'is_title' => $itemData == 'title',
      );
    }
    return array(
        'items' => $items,
        'active' => $submenuActive,
    );
  }
  
}
