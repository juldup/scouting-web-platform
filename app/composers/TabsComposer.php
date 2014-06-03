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
 * This composer generates the section selection menu
 */
class TabsComposer {
  
  public function compose($view) {
    
    $tabs = array();
    
    // Get current section
    $user = View::shared('user');
    $selectedSectionId = $user->currentSection->id;
    
    // Get current route and its parameters
    $currentRoute = Route::current();
    $currentRouteName = Route::currentRouteName();
    if (!$currentRouteName) $currentRouteName = 'home';
    $routeParameters = $currentRoute ? $currentRoute->parameters() : array();
    
    // Generate section items with transposed route parameters to match the section
    $sections = Section::orderBy('position')->get();
    foreach ($sections as $section) {
      $routeParameters['section_slug'] = $section->slug;
      $tabs[] = array(
          "link" => URL::route($currentRouteName, $routeParameters),
          "text" => $section->name,
          "is_selected" => $selectedSectionId == $section->id,
      );
    }
    
    // Pass the section list to the view
    $view->withTabs($tabs);
    
    
  }
  
}
