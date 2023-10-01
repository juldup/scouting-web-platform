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
 * The leaders can add and edit custom pages.
 */
class CustomPageController extends GenericPageController {
  
  protected $pagesAdaptToSections = true;
  
  private $customPage;
  
  public function __construct() {
    parent::__construct();
    // Get current custom page
    $params = Route::current()->parameters();
    if (array_key_exists('page_slug', $params)) {
      $pageSlug = $params['page_slug'];
      $this->customPage = Page::where('slug', '=', $pageSlug)->first();
    } else {
      // Showing page list, there is no selected custom page
    }
  }
  
  protected function getEditRouteName() {
    return "edit_custom_page_submit";
  }
  protected function getEditRouteParameters() {
    if (!$this->customPage) return App::abort(404);
    return array('page_slug' => $this->customPage->slug);
  }
  protected function getShowRouteName() {
    return "custom_page";
  }
  protected function getShowRouteParameters() {
    if (!$this->customPage) return App::abort(404);
    return array('page_slug' => $this->customPage->slug);
  }
  protected function getPageType() {
    return "custom";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    if (!$this->customPage) return App::abort(404);
    return $this->customPage->title;
  }
  protected function canDisplayPage() {
    if ($this->customPage->leaders_only && !$this->user->isLeader()) return false;
    return true;
  }
  
  protected function getPage() {
    if (!$this->customPage) return App::abort(404);
    return $this->customPage;
  }

  /**
   * [Route] Shows the list of editable pages across the website
   */
  public function showPageList() {
    if (!$this->user->isLeader()) return Helper::forbiddenResponse();
    return View::make('pages.customPage.pageList');
  }
  
  /**
   * [Route] Adds a new custom page to the website
   */
  public function addCustomPage() {
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) return Helper::forbiddenResponse();
    $pageTitle = Input::get('page_title');
    $leadersOnly = Input::get('leaders_only');
    // Check that page title is valid
    if (!$pageTitle) {
      return Redirect::route('edit_pages')->with('error_message', 'Le titre de la page ne peut pas être vide.');
    }
    $slug = Helper::slugify($pageTitle);
    $existingPage = Page::where('slug', '=', $slug)->first();
    if ($existingPage) {
      return Redirect::route('edit_pages')->with('error_message', 'Cette page existe déjà.');
    }
    // Create new page
    $page = Page::create(array(
        'type' => 'custom',
        'section_id' => 1,
        'title' => $pageTitle,
        'slug' => $slug,
        'body_html' => '',
        'leaders_only' => $leadersOnly ? 1 : 0,
    ));
    $page->position = $page->id;
    $page->save();
    LogEntry::log("Page", "Création d'une page", array('Titre' => $pageTitle));
    return Redirect::route('edit_custom_page', array('page_slug' => $slug));
  }
  
  /**
   * [Route] Deletes a custom page
   */
  public function deletePage() {
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) return Helper::forbiddenResponse();
    if ($this->customPage) {
      $this->customPage->delete();
      LogEntry::log("Page", "Suppression d'une page", array('Titre' => $this->customPage->title));
      return Redirect::route('edit_pages')->with('success_message', "La page <strong>" . htmlspecialchars($this->customPage->title) . "</strong> a été supprimée");
    } else {
      return Redirect::route('edit_pages')->with('error_message', "Cette page n'existe plus");
    }
  }
  
  /**
   * [Ajax] Changes the order of the custom pages
   */
  public function saveCustomPageOrder() {
    // Error message, ready to be sent
    $errorResponse = json_encode(array("result" => "Failure"));
    // Check that the user has the right to modify the page order
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return $errorResponse;
    }
    // Get list of pages in order
    $pageIdsInOrder = Input::get('page_order');
    $pageIdsInOrderArray = explode(" ", $pageIdsInOrder);
    // Retrieve pages
    $pages = Page::where('type', '=', 'custom')
            ->where(function($query) use ($pageIdsInOrderArray) {
              foreach ($pageIdsInOrderArray as $pageId) {
                $query->orWhere('id', '=', $pageId);
              }
            })->get();
    // Check that the number of pages corresponds
    if (count($pageIdsInOrderArray) != count($pages)) {
      return $errorResponse;
    }
    // Get the list of positions
    $positions = array();
    foreach ($pages as $page) {
      $positions[] = $page->position;
    }
    sort($positions);
    // Assign new positions
    foreach ($pages as $page) {
      // Get new order of this album
      $index = array_search($page->id, $pageIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $page->position = $positions[$index];
    }
    // Save all pages
    foreach ($pages as $page) {
      try {
        $page->save();
      } catch (Exception $ex) {
        Log::error($ex);
        return $errorResponse;
      }
    }
    // Log
    LogEntry::log("Page", "Réordonnancement des pages"); // TODO improve log message
    // Return success response
    return json_encode(array('result' => "Success"));
  }
  
}
