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
 * News can be added to the news page. Each section has its own news.
 * This controller displays the news page and allows leaders to add and edit news.
 */
class NewsController extends BaseController {
  
  public function currentPageAdaptToSections() {
    $currentRoute = Route::current();
    $currentRouteAction = $currentRoute ? $currentRoute->getAction() : "";
    $currentRouteName = $currentRouteAction ? $currentRouteAction['as'] : "";
    return ($currentRouteName != 'global_news');
  }

  /**
   * [Route] Shows the news page
   * 
   * @param boolean $showArchives  True if the archived news are being shown
   * @param integer $page  The archive page to display
   */
  public function showPage($section_slug = null, $showArchives = false, $page = 0) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    // Build query
    $oneYearAgo = Helper::oneYearAgo();
    $query = News::orderBy('id', 'DESC');
    $query->where('section_id', '=', $this->section->id);
    if ($showArchives) {
      // Show archived news
      $pageSize = 30;
      $query->where('news_date', '<', $oneYearAgo);
      $archiveQuery = clone $query->getQuery();
      $query->skip($page * $pageSize)
              ->take($pageSize);
      // Determine whether there are more archive pages
      $hasArchives = count($archiveQuery
              ->skip(($page + 1) * $pageSize)
              ->take(1)
              ->get());
    } else {
      // Show recent news
      $archiveQuery = clone $query->getQuery();
      $query->where('news_date', '>=', $oneYearAgo);
      // Determine whether there are archives
      $hasArchives = $archiveQuery->where('news_date', '<', $oneYearAgo)
              ->take(1)
              ->count();
    }
    $news = $query->get();
    // Make view
    return View::make('pages.news.news', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS, $this->section),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
        'has_archives' => $hasArchives,
        'showing_archives' => $showArchives,
        'next_page' => $page + 1,
        'is_global_news_page' => false,
    ));
  }
  
  /**
   * [Route] Shows a page containing a single news item
   */
  public function showSingleNews($news_id) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    // Build query
    $newsItem = News::find($news_id);
    if (!$newsItem) {
      return App::abort(404);
    }
    // Make view
    return View::make('pages.news.single-news', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS, $newsItem->section_id),
        'edit_url' => URL::route('manage_news', array('section_slug' => $newsItem->getSection()->slug)),
        'newsItem' => $newsItem
    ));
  }
  
  /**
   * [Route] Shows the global news page, listing the news of all sections
   */
  public function showGlobalNewsPage() {
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    // Get all recent news
    $oneYearAgo = Helper::oneYearAgo();
    $query = News::orderBy('id', 'DESC');
    $query->where('news_date', '>=', $oneYearAgo);
    $news = $query->get();
    // No archives show on global news page
    $hasArchives = false;
    // Make view
    return View::make('pages.news.news', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS, $this->section),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
        'has_archives' => false, // Not showing archives on global news page
        'showing_archives' => false,
        'is_global_news_page' => true,
    ));
  }
  
  /**
   * [Route] Shows the news page with archived news
   */
  public function showArchives($section_slug = null) {
    $page = Input::get('page');
    if (!$page) $page = 0;
    return $this->showPage($section_slug, true, $page);
  }
  
  /**
   * [Route] Displays the news management page
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_NEWS)) {
      return App::abort(404);
    }
    // Make sure the user has access to this page
    if (!$this->user->can(Privilege::$EDIT_NEWS, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    // Get news
    $oneYearAgo = Helper::oneYearAgo();
    $news = News::where('news_date', '>=', $oneYearAgo)
            ->orderBy('id', 'DESC')
            ->where('section_id', '=', $this->section->id)
            ->get();
    // Make list of sections for section selector
    $sections = Section::getSectionsForSelect();
    // Make view
    return View::make('pages.news.editNews', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_NEWS),
        'edit_url' => URL::route('manage_news', array('section_slug' => $this->section->slug)),
        'page_url' => URL::route('news', array('section_slug' => $this->section->slug)),
        'news' => $news,
        'sections' => $sections,
    ));
  }
  
  /**
   * [Route] Creates or updates a piece of news in the database
   */
  public function submitNews($section_slug) {
    // Gather input
    $newsId = Input::get('news_id');
    $title = Input::get('news_title');
    $body = Input::get('news_body');
    $sectionId = Input::get('section');
    // Make sure the current user can edit news for this section
    if (!$this->user->can(Privilege::$EDIT_NEWS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    // Make basic tests and update/create the piece of news
    $success = false;
    if (!$title) {
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else if (!$body) {
      $success = false;
      $message = "Tu dois entrer un contenu.";
    } else {
      if ($newsId) {
        // Updating a piece of news
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
            Log::error($e);
            $success = false;
            $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
            LogEntry::error("Actualités", "Erreur lors de l'enregistrement d'une nouvelle", array("Erreur" => $e->getMessage()));
          }
        } else {
          $success = false;
          $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
        }
      } else {
        // Creating a piece of news
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
          Log::error($e);
          $success = false;
          $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
          LogEntry::error("Actualités", "Erreur lors de l'enregistrement d'une nouvelle", array("Erreur" => $e->getMessage()));
        }
      }
    }
    // Redirect with status
    $response = Redirect::route('manage_news', array(
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) {
      LogEntry::log("Actualités", $newsId ? "Modification d'une nouvelle" : "Ajout d'une nouvelle",
              array("Titre" => $title, "Contenu" => $body, "Date" => Helper::dateToHuman($news->news_date)));
      return $response;
    } else {
      return $response->withInput();
    }
  }
  
  /**
   * [Route] Deletes a piece of news from the database
   */
  public function deleteNews($news_id) {
    // Get the news
    $news = News::find($news_id);
    if (!$news) {
      App::abort(404, "Cette nouvelle n'existe pas.");
    }
    // Make sure the current user can delete this piece of news
    if (!$this->user->can(Privilege::$EDIT_NEWS, $news->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Delete the news
    try {
      $news->delete();
      $success = true;
      $message = "La nouvelle a été supprimée.";
      LogEntry::log("Actualités", "Suppression d'une nouvelle",
              array("Titre" => $news->title, "Contenu" => $news->body, "Date" => Helper::dateToHuman($news->news_date)));
    } catch (Illuminate\Database\QueryException $e) {
      Log::error($e);
      $success = false;
      $message = "Une erreur s'est produite. La nouvelle n'a pas été supprimée.";
      LogEntry::error("Actualités", "Erreur lors de la suppression d'une nouvelle", array("Erreur" => $e->getMessage()));
    }
    // Redirect with status message
    return Redirect::route('manage_news', array(
        "section_slug" => $news->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
