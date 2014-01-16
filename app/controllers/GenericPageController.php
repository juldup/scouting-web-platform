<?php

abstract class GenericPageController extends BaseController {
  
  protected abstract function getShowRouteName();
  protected abstract function getEditRouteName();
  protected abstract function isSectionPage();
  protected abstract function getPageType();
  protected abstract function getPageTitle();
  
  public function showPage() {
    $page = $this->getPage();
    $sectionSlugArray = array();
    if ($this->isSectionPage()) {
      // Disable section slug on unit pages to avoid redirect
      $sectionSlugArray = array("section_slug" => $this->user->currentSection->slug);
    }
    $editURL = URL::route($this->getEditRouteName(), $sectionSlugArray);
    return View::make('pages.page')
            ->with('page_body', $page->body_html)
            ->with('page_title', $this->getPageTitle())
            ->with('edit_url', $editURL)
            ->with('can_edit', $this->canEdit());
  }
  
  public function showEdit() {
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    $page = $this->getPage();
    $images = PageImage::where('page_id', '=', $page->id)->get()->all();
    return View::make('pages.editPage')
            ->with('page_body', $page->body_html)
            ->with('page_title', $this->getPageTitle())
            ->with('page_id', $page->id)
            ->with('images', $images)
            ->with('original_page_url', URL::route($this->getShowRouteName()));
  }
  
  public function savePage() {
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    $newBody = Input::get('page_body');
    $page = $this->getPage();
    $page->body_html = $newBody;
    $page->save();
    
    $sectionSlugArray = array();
    if ($this->isSectionPage()) {
      // Disable section slug on unit pages to avoid redirect
      $sectionSlugArray = array("section_slug" => View::shared('user')->currentSection->slug);;
    }
    return Illuminate\Http\RedirectResponse::create(URL::route($this->getShowRouteName(), $sectionSlugArray));
  }
  
  protected function getPage() {
    $sectionId = 1;
    if ($this->isSectionPage()) {
      $sectionId = View::shared('user')->currentSection->id;
    }
    $page = Page::where('section_id', '=', $sectionId)->where('type', '=' , $this->getPageType())->first();
    if (!$page) {
      $page = Page::create(array(
          "type" => $this->getPageType(),
          "section_id" => $sectionId,
          "body_html" => "<p>Cette page n'existe pas encore.</p>",
      ));
    }
    return $page;
  }
  
  protected function canEdit() {
    if ($this->isSectionPage()) {
      return $this->user->can(Privilege::$EDIT_PAGES, $this->user->currentSection);
    } else {
      return $this->user->can(Privilege::$EDIT_PAGES, 1);
    }
  }
  
}
