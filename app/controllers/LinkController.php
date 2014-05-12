<?php

/**
 * Provides a page with useful links, and the means for the leaders to modify this list
 */
class LinkController extends BaseController {
  
  /**
   * [Route] Displays the link page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LINKS)) {
      return App::abort(404);
    }
    // Get links
    $links = Link::all();
    // Make view
    return View::make('pages.links.links', array(
        'links' => $links,
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
    ));
  }
  
  /**
   * [Route] Displays the link management page
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LINKS)) {
      return App::abort(404);
    }
    // Make sure the current user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get links
    $links = Link::all();
    // Make view
    return View::make('pages.links.editLinks', array(
        'links' => $links,
    ));
  }
  
  /**
   * [Route] Updates or creates a link
   */
  public function submitLink() {
    // Make sure the user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get input data
    $linkId = Input::get('link_id');
    $title = Input::get('link_title');
    $url = Input::get('link_url');
    $description = Input::get('link_description');
    // Check that the title and url are not empty
    if (!$title) {
      $success = false;
      $message = "Le titre ne peut pas être vide.";
    } elseif (!$url) {
      $success = false;
      $message = "L'url ne peut pas être vide.";
    } else {
      // Prepend "http://" to the url if missing
      if (strpos($url, "//") === false) {
        $url = "http://" . $url;
      }
      // Update database
      if ($linkId) {
        // Modifying existing link
        $link = Link::find($linkId);
        if ($link) {
          $link->title = $title;
          $link->url = $url;
          $link->description = $description;
          try {
            $link->save();
            $success = true;
            $message = "Le lien a été mis a jour.";
          } catch (Exception $e) {
            $success = false;
            $message = "Une erreur est survenue. Les changements n'ont pas été enregistrés.";
          }
        } else {
          $success = false;
          $message = "Une erreur est survenue. Les changements n'ont pas été enregistrés.";
        }
      } else {
        // Creating new link
        try {
          Link::create(array(
              'title' => $title,
              'url' => $url,
              'description' => $description,
          ));
          $success = true;
          $message = "Le lien a été créé.";
        } catch (Exception $ex) {
          $success = false;
          $message = "Une erreur est survenue. Le lien n'a pas été créé.";
        }
      }
    }
    // Redirect with status message
    $redirect = Redirect::route('edit_links')
            ->with($success ? "success_message" : "error_message", $message);
    if ($success) return $redirect;
    else return $redirect->withInput();
  }
  
  /**
   * [Route] Deletes a link
   */
  public function deleteLink($link_id) {
    // Make sure the user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Delete link
    $link = Link::find($link_id);
    try {
      if (!$link) throw new Exception();
      $link->delete();
      return Redirect::route('edit_links')
              ->with('success_message', "Le lien vers " . $link->url . " a été supprimé");
    } catch (Exception $ex) {
      return Redirect::route('edit_links')
              ->with('error_message', "Une erreur est survenue. Le lien n'a pas été supprimé.");
    }
  }
  
}
