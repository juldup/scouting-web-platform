<?php

class NewsController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    
    $oneYearAgo = date('Y-m-d', time() - 365*24*3600);
    
    if ($this->section->id == 1) {
      $news = News::where('news_date', '>=', $oneYearAgo)->get();
    } else {
      $news = News::where('news_date', '>=', $oneYearAgo)
              ->where('section_id', '=', $this->section->id)->get();
    }
    
    echo News::where('news_date', '>', $oneYearAgo)
              ->where('section_id', '=', $this->section->id)->getQuery()->toSQL();
    echo $oneYearAgo;
    echo $this->section->id;
    
    return View::make('pages.news.news', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
    ));
  }
  
  public function showEdit() {
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $this->user->currentSection)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $oneYearAgo = date('Y-m-d', time() - 365*24*3600);
    $news = News::where('news_date', '>=', $oneYearAgo)
              ->where('section_id', '=', $this->section->id)->get();
    
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
    $content = Input::get('news_content');
    $sectionId = Input::get('section');
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $sectionId)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $success = false;
    if (!$title) {
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else if (!$content) {
      $success = false;
      $message = "Tu dois entrer un contenu.";
    } else {
      if ($newsId) {
        $news = News::find($newsId);
        if ($news) {
          $news->title = $title;
          $news->content = $content;
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
              'content' => $content,
              'section_id' => $sectionId,
          ));
          $section_slug = $news->getSection()->slug;
          $success = true;
          $message = "La nouvelle a été créée.";
        } catch (Illuminate\Database\QueryException $e) {
          $success = false;
          throw $e;
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
      throw new NotFoundHttpException("Cette nouvelle n'existe pas");
    }
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $news->section_id)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    try {
      $news->delete();
      $success = true;
      $message = "La nouvelle a été supprimée.";
    } catch (Illuminate\Database\QueryException $e) {
      $success = false;
      $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
    }
    
    return Redirect::route('manage_news', array(
        "section_slug" => $news->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
