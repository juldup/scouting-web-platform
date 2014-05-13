<?php

/**
 * Several pages of the website can be edited by the leaders. This class
 * provides a generic page controller that is extended for each editable
 * page of the website.
 */
abstract class GenericPageController extends BaseController {
  
  // The following methods must be implemented in the child class
  
  /**
   * Returns the name of the route that shows the page
   */
  protected abstract function getShowRouteName();
  
  /**
   * Returns the name of the route that shows the page in edition mode
   */
  protected abstract function getEditRouteName();
  
  /**
   * Returns whether the page belongs to a section or is common to all sections
   */
  protected abstract function isSectionPage();
  
  /**
   * Returns an identifier of the type of page
   */
  protected abstract function getPageType();
  
  /**
   * Returns the title that is displayed at the top of the page
   * (if empty, there will be no title displayed on the page, and
   * the page's meta title will be the name of the unit)
   */
  protected abstract function getPageTitle();
  
  /**
   * Returns true if the page can be displayed (cf. site parameters)
   */
  protected abstract function canDisplayPage();
  
  /**
   * True only if this page is the home page (overriden in the home page controller)
   */
  protected $isHomePage = false;
  
  /**
   * [Route] Displays the page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!$this->canDisplayPage()) {
      return App::abort(404);
    }
    // Get the page
    $page = $this->getPage();
    // Generate edit route
    if ($this->isSectionPage()) {
      // For section pages, add the section slug in the route parameters
      $sectionSlugParameter = array("section_slug" => $this->user->currentSection->slug);
    } else {
      $sectionSlugParameter = array();
    }
    $editURL = URL::route($this->getEditRouteName(), $sectionSlugParameter);
    // Make view
    return View::make('pages.page', array(
        'page_body' => $page->body_html,
        'page_title' => $this->getPageTitle(),
        'edit_url' => $editURL,
        'can_edit' => $this->canEdit(),
        'is_home_page' => $this->isHomePage,
    ));
  }
  
  /**
   * [Route] Shows the page in edit mode
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!$this->canDisplayPage()) {
      return App::abort(404);
    }
    // Make sure the user can edit this page
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    // Get the page and its images
    $page = $this->getPage();
    $images = PageImage::where('page_id', '=', $page->id)->get()->all();
    // Make view
    return View::make('pages.editPage')
            ->with('page_body', $page->body_html)
            ->with('page_title', $this->getPageTitle())
            ->with('page_id', $page->id)
            ->with('images', $images)
            ->with('original_page_url', URL::route($this->getShowRouteName()));
  }
  
  /**
   * [Route] Saves the page that has been modified
   */
  public function savePage() {
    // Make sure the user can edit this page
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    // Update page
    $newBody = Input::get('page_body');
    $page = $this->getPage();
    $page->body_html = $newBody;
    $page->save();
    // Redirect back to page
    if ($this->isSectionPage()) {
      $sectionSlugParameter = array("section_slug" => View::shared('user')->currentSection->slug);;
    } else {
      $sectionSlugParameter = array();
    }
    return Redirect::route($this->getShowRouteName(), $sectionSlugParameter);
  }
  
  /**
   * Fetches the page from the database and returns it. If the page
   * does not exist, a new one is created.
   */
  protected function getPage() {
    // Get section id
    if ($this->isSectionPage()) {
      $sectionId = View::shared('user')->currentSection->id;
    } else {
      $sectionId = 1;
    }
    // Get page
    $page = Page::where('section_id', '=', $sectionId)->where('type', '=' , $this->getPageType())->first();
    // Generate a new page if it does not exist
    if (!$page) {
      $page = Page::create(array(
          "type" => $this->getPageType(),
          "section_id" => $sectionId,
          "body_html" => "<p>Cette page n'existe pas encore.</p>",
      ));
    }
    // Return the page
    return $page;
  }
  
  /**
   * Returns whether the user is allowed to edit this page
   */
  protected function canEdit() {
    if ($this->isSectionPage()) {
      return $this->user->can(Privilege::$EDIT_PAGES, $this->user->currentSection);
    } else {
      return $this->user->can(Privilege::$EDIT_PAGES, 1);
    }
  }
  
}
