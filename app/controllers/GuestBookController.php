<?php

/**
 * The guest book is a place where visitors can leave messages for posterity
 */
class GuestBookController extends BaseController {
  
  /**
   * [Route] Shows the guest book with all its entries
   * 
   * @param boolean $managing  True if in edit mode
   */
  public function showPage($section_slug = null, $managing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_GUEST_BOOK)) {
      return App::abort(404);
    }
    // Fetch all guest book entries
    $entries = GuestBookEntry::orderBy('id', 'DESC')->get();
    // Make view
    return View::make('pages.guest_book.guest_book', array(
        'guest_book_entries' => $entries,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1),
    ));
  }
  
  /**
   * [Route] Adds a new guest book entry
   */
  public function submit() {
    // Get input data
    $body = Input::get('body');
    $author = Input::get('author');
    // Check input data
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
    try {
      // Create guest book entry
      GuestBookEntry::create(array(
          'body' => $body,
          'author' => $author,
      ));
      // Redirect with success message
      return Redirect::route('guest_book')
              ->with('success_message', "Votre message a été ajouté dans le livre d'or.");
    } catch (Exception $e) {
      return Redirect::route('guest_book')
              ->with('error_message', "Une erreur est survenue. Votre message n'a pas été ajouté au livre d'or.")
              ->withInput();
    }
  }
  
  /**
   * [Route] Shows the guest book management page
   */
  public function showEdit($section_slug = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_GUEST_BOOK)) {
      return App::abort(404);
    }
    // Make sure the user has access to the guest book management page
    if (!$this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Show edit page
    return $this->showPage($section_slug, true);
  }
  
  /**
   * [Route] Deletes a guest book entry
   */
  public function delete($entry_id) {
    // Make sure the user can delete guest book entries
    if (!$this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get entry and delete it, redirecting with a status message
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
