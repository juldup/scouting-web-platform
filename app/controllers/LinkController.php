<?php

class LinkController extends BaseController {
  
  public function showPage() {
    
    $links = Link::all();
    
    return View::make('pages.links.links', array(
        'links' => $links,
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
    ));
  }
  
  public function showEdit() {
    
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $links = Link::all();
    
    return View::make('pages.links.editLinks', array(
        'links' => $links,
    ));
  }
  
  public function submitLink() {
    
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $linkId = Input::get('link_id');
    $title = Input::get('link_title');
    $url = Input::get('link_url');
    $description = Input::get('link_description');
    
    if (!$title) {
      $success = false;
      $message = "Le titre ne peut pas être vide.";
    } elseif (!$url) {
      $success = false;
      $message = "L'url ne peut pas être vide.";
    } else {
      
      if (strpos($url, "//") === false) {
        $url = "http://" . $url;
      }
      
      if ($linkId) {
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
    
    $redirect = Redirect::route('edit_links')
            ->with($success ? "success_message" : "error_message", $message);
    if ($success) return $redirect;
    else return $redirect->withInput();
  }
  
  public function deleteLink($link_id) {
    
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
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
