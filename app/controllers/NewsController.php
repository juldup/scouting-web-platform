<?php

class NewsController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  public function showPage($section_slug = null, $showArchives = false, $page = 0) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    
    $oneYearAgo = Helper::oneYearAgo();
    
    // Build query
    $query = News::orderBy('id', 'DESC');
    if ($this->section->id != 1) {
      $query->where('section_id', '=', $this->section->id);
    }
    if ($showArchives) {
      // Show archived news
      $pageSize = 30;
      $query->where('news_date', '<', $oneYearAgo);
      $archiveQuery = clone $query->getQuery();
      $query->skip($page * $pageSize)
              ->take($pageSize);
      $hasArchives = count($archiveQuery
              ->skip(($page + 1) * $pageSize)
              ->take(1)
              ->get());
      echo $archiveQuery
              ->skip(($page + 1) * $pageSize)
              ->take(1)->toSQL();
    } else {
      // Show recent news
      $archiveQuery = clone $query->getQuery();
      $query->where('news_date', '>=', $oneYearAgo);
      $hasArchives = $archiveQuery->where('news_date', '<', $oneYearAgo)
              ->take(1)
              ->count();
    }
    $news = $query->get();
    
    return View::make('pages.news.news', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS, $this->section),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
        'has_archives' => $hasArchives,
        'showing_archives' => $showArchives,
        'next_page' => $page + 1,
    ));
  }
  
  public function showArchives($section_slug = null) {
    $page = Input::get('page');
    if (!$page) $page = 0;
    return $this->showPage($section_slug, true, $page);
  }
  
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    
    $oneYearAgo = Helper::oneYearAgo();
    $news = News::where('news_date', '>=', $oneYearAgo)
            ->orderBy('id', 'DESC')
            ->where('section_id', '=', $this->section->id)
            ->get();
    
    $sections = Section::getSectionsForSelect();
    
    return View::make('pages.news.editNews', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
        'sections' => $sections,
    ));
  }
  
  public function submitNews($section_slug) {
    
    $newsId = Input::get('news_id');
    $title = Input::get('news_title');
    $body = Input::get('news_body');
    $sectionId = Input::get('section');
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    
    $success = false;
    if (!$title) {
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else if (!$body) {
      $success = false;
      $message = "Tu dois entrer un contenu.";
    } else {
      if ($newsId) {
        $news = News::find($newsId);
        if ($news) {
          if (!$this->user->can(Privilege::$EDIT_NEWS, $news->section_id)) {
            return Helper::forbiddenResponse();
          }
          $news->title = $title;
          $news->body = $body;
          $news->section_id = $sectionId;
          try {
            $news->save();
            $success = true;
            $message = "La nouvelle a été mise à jour.";
            $section_slug = $news->getSection()->slug;
          } catch (Illuminate\Database\QueryException $e) {
            $success = false;
            $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
          }
        } else {
          $success = false;
          $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
        }
      } else {
        try {
          $news = News::create(array(
              'news_date' => date('Y-m-d'),
              'title' => $title,
              'body' => $body,
              'section_id' => $sectionId,
          ));
          $section_slug = $news->getSection()->slug;
          $success = true;
          $message = "La nouvelle a été créée.";
        } catch (Illuminate\Database\QueryException $e) {
          $success = false;
          $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
        }
      }
    }
    
    $response = Redirect::route('manage_news', array(
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) return $response;
    else return $response->withInput();
  }
  
  public function deleteNews($news_id) {
    
    $news = News::find($news_id);
    
    if (!$news) {
     App::abort(404, "Cette nouvelle n'existe pas.");
    }
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $news->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    try {
      $news->delete();
      $success = true;
      $message = "La nouvelle a été supprimée.";
    } catch (Illuminate\Database\QueryException $e) {
      $success = false;
      $message = "Une erreur s'est produite. La nouvelle n'a pas été supprimée.";
    }
    
    return Redirect::route('manage_news', array(
        "section_slug" => $news->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
