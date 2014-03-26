<?php

class GuestBookController extends BaseController {
  
  public function showPage($section_slug = null, $managing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_GUEST_BOOK)) {
      return App::abort(404);
    }
    $entries = GuestBookEntry::orderBy('id', 'DESC')->get();
    return View::make('pages.guest_book.guest_book', array(
        'guest_book_entries' => $entries,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1),
    ));
  }
  
  public function submit() {
    $body = Input::get('body');
    $author = Input::get('author');
    if (!$author) {
      return Redirect::route('guest_book')
              ->withInput()
              ->with('error_message', "Indiquez qui vous êtes, c'est plus sympa.");
    }
    if (!$body) {
      return Redirect::route('guest_book')
              ->withInput()
              ->with('error_message', "Entrez un message.");
    }
    GuestBookEntry::create(array(
        'body' => $body,
        'author' => $author,
    ));
    return Redirect::route('guest_book')
            ->with('success_message', "Votre message a été ajouté dans le livre d'or.");
  }
  
  public function showEdit($section_slug = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_GUEST_BOOK)) {
      return App::abort(404);
    }
    if (!$this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1)) {
      return Helper::forbiddenResponse();
    }
    return $this->showPage($section_slug, true);
  }
  
  public function delete($entry_id) {
    if (!$this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1)) {
      return Helper::forbiddenResponse();
    }
    $entry = GuestBookEntry::find($entry_id);
    if ($entry) {
      $entry->delete();
      return Redirect::route('edit_guest_book')
            ->with('success_message', "Le message a été supprimé.");
    }
    return Redirect::route('edit_guest_book')
          ->with('error_message', "Une erreur est survenue. Le message n'a pas été supprimé.");
  }
  
}
