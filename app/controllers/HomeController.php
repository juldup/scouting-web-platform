<?php

class HomeController extends BaseController {
  
  public function showPage() {
    $homePage = $this->getHomePage();
    return View::make('pages.page')
            ->with('page_content', $homePage->content_html)
            ->with('can_edit', $this->user->can("Modifier les pages #delasection", 1));
  }
  
  public function showGestion() {
    if (!$this->user->can("Modifier les pages #delasection", 1)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    $homePage = $this->getHomePage();
    return View::make('pages.editPage')
            ->with('page_content', $homePage->content_markdown)
            ->with('original_page_url', URL::route('home'));
  }
  
  public function savePage() {
    if (!$this->user->can("Modifier les pages #delasection", 1)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    $newContent = Input::get('page_content');
    $homePage = $this->getHomePage();
    $homePage->content_markdown = $newContent;
    $homePage->content_html = \Michelf\Markdown::defaultTransform($newContent);
    $homePage->save();
    return Illuminate\Http\RedirectResponse::create(URL::route('manage_home'));
  }
  
  protected function getHomePage() {
    $homePage = Page::where('section_id', '=', 1)->where('type', '=' ,'home')->first();
    if (!$homePage) {
      $homePage = Page::create(array(
          "type" => "home",
          "section_id" => 1,
          "content_html" => "<h1>Cette page n'existe pas encore.</h1>",
          "content_markdown" => "# Tape ici le titre le la page\n\nTape ici le contenu de la page.\n\nRegarde l'exemple de droite si tu veux faire une mise en page avancée.",
      ));
//      $homePage->save();
    }
    return $homePage;
  }
  
}
