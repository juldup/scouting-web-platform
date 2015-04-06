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
    if ($currentRoute) {
      $currentRouteAction = $currentRoute ? $currentRoute->getAction() : "";
      $currentRouteParameters = $currentRoute->parameters();
      $currentRouteName = $currentRouteAction ? $currentRouteAction['as'] : "";
    } else {
      // On 404 error, there might be no route
      $currentRouteName = "";
      $currentRouteParameters = array();
    }
    
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
            ->withGlobalNewsSelected($currentRouteName === "global_news")
            ->withDailyPhotosSelected($currentRouteName === "daily_photos");
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
    
    // Get current user's section
    $userMembers = $user->getAssociatedLeaderMembers();
    if (count($userMembers)) {
      $leader = $userMembers[0];
      $section = $leader->getSection();
    } else {
      // User not member (e.g. webmaster), use default section
      $section = Section::find(1);
    }
    
    $leaderCategory = array();
    $leaderCategory["Coin des animateurs"] = 'leader_corner';
//      $leaderCategory["Aide sur la gestion du site"] = 'leader_help';
    
    $leaderCategory["Opérations courantes"] = 'title';
    if (Parameter::get(Parameter::$SHOW_CALENDAR)) {
      $leaderCategory['Gérer le calendrier'] = array(
          'routeName' => $user->can(Privilege::$EDIT_CALENDAR, $section) ? 'manage_calendar' : null,
          'routeParameters' => $user->can(Privilege::$EDIT_CALENDAR, 1) ? null : array('section_slug' => $section->slug),
      );
      $leaderCategory[$user->can(Privilege::$MANAGE_ATTENDANCE) ? 'Gérer les présences' : 'Voir les présences'] = 'edit_attendance';
    }
    $leaderCategory[$user->can(Privilege::$MANAGE_EVENT_PAYMENTS) ? 'Gérer les paiements' : 'Voir les paiements'] = 'edit_payment';
    if (Parameter::get(Parameter::$SHOW_PHOTOS))
      $leaderCategory['Gérer les photos'] = array(
          'routeName' => $user->can(Privilege::$POST_PHOTOS, $section) ? 'edit_photos' : null,
          'routeParameters' => $user->can(Privilege::$POST_PHOTOS, 1) ? null : array('section_slug' => $section->slug),
      );
    if (Parameter::get(Parameter::$SHOW_DOCUMENTS))
      $leaderCategory['Gérer les documents'] = array(
          'routeName' => $user->can(Privilege::$EDIT_DOCUMENTS, $section) ? 'manage_documents' : null,
          'routeParameters' => $user->can(Privilege::$EDIT_DOCUMENTS, 1) ? null : array('section_slug' => $section->slug),
      );
    if (Parameter::get(Parameter::$SHOW_NEWS))
      $leaderCategory['Gérer les actualités'] = array(
          'routeName' => $user->can(Privilege::$EDIT_NEWS, $section) ? 'manage_news' : null,
          'routeParameters' => $user->can(Privilege::$EDIT_NEWS, 1) ? null : array('section_slug' => $section->slug),
      );
    if (Parameter::get(Parameter::$SHOW_EMAILS))
      $leaderCategory['Gérer les e-mails'] = array(
          'routeName' => $user->can(Privilege::$SEND_EMAILS, $section) ? 'manage_emails' : null,
          'routeParameters' => $user->can(Privilege::$SEND_EMAILS, 1) ? null : array('section_slug' => $section->slug),
      );
    $leaderCategory['Envoyer un e-mail aux parents'] = array(
          'routeName' => $user->can(Privilege::$SEND_EMAILS, $section) ? 'send_section_email' : null,
          'routeParameters' => $user->can(Privilege::$SEND_EMAILS, 1) ? null : array('section_slug' => $section->slug),
      );
    $leaderCategory['Envoyer un e-mail aux animateurs'] = 'send_leader_email';
    if (Parameter::get(Parameter::$SHOW_HEALTH_CARDS))
      $leaderCategory['Gérer les fiches santé'] = array(
          'routeName' => $user->can(Privilege::$VIEW_HEALTH_CARDS, $section) ? 'manage_health_cards' : null,
          'routeParameters' => $user->can(Privilege::$VIEW_HEALTH_CARDS, 1) ? null : array('section_slug' => $section->slug),
      );
    $leaderCategory['Trésorerie'] = 'accounting';
    
    $leaderCategory['Opérations annuelles'] = 'title';
    $leaderCategory['Gérer les inscriptions'] =
            $user->can(Privilege::$EDIT_LISTING_ALL) || $user->can(Privilege::$EDIT_LISTING_LIMITED) ||
            $user->can(Privilege::$SECTION_TRANSFER || $user->can(Privilege::$MANAGE_ACCOUNTING))
            ? 'manage_registration' : null;
    $leaderCategory['Gérer le listing'] = array(
          'routeName' => $user->can(Privilege::$EDIT_LISTING_ALL, $section) || $user->can(Privilege::$EDIT_LISTING_LIMITED, $section) ? 'manage_listing' : null,
          'routeParameters' => $user->can(Privilege::$EDIT_LISTING_ALL, 1) || $user->can(Privilege::$EDIT_LISTING_LIMITED, 1) ? null : array('section_slug' => $section->slug),
      );
    $leaderCategory['Gérer les animateurs'] = 'edit_leaders';
    $leaderCategory['Privilèges des animateurs'] = 'edit_privileges';
    $leaderCategory['Gérer les sections'] = 'section_data';
    
    $leaderCategory['Contenu du site'] = 'title';
    $leaderCategory["Paramètres du site"] = $user->can(Privilege::$EDIT_GLOBAL_PARAMETERS) ? 'edit_parameters' : null;
    $leaderCategory["Style du site"] = $user->can(Privilege::$EDIT_STYLE) ? 'edit_css' : null;
    
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
    $sectionMenuItems["Page d'accueil"] = 'section';
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
      if (is_array($route)) {
        if ($route['routeName']) {
          $url = URL::route($route['routeName'], $route['routeParameters']);
        } else {
          $url = null;
        }
      } else if ($route) {
        $url = URL::route($route);
      } else {
        $url = null;
      }
      $active = $route == $currentRouteName;
      if ($active) $submenuActive = true;
      $items[$itemName] = array(
          'url' => $url,
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
