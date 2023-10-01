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
    if (Session::token() != Input::get('_token')) {
      return Redirect::route('guest_book')
              ->withInput()
              ->with('error_message', "Une erreur est survenue. Veuillez rafraichir la page et réessayer.");
    }
    try {
      // Create guest book entry
      GuestBookEntry::create(array(
          'body' => $body,
          'author' => $author,
      ));
      // Log
      LogEntry::log("Livre d'or", "Ajout d'une entrée dans le livre d'or", array("Auteur" => $author, "Message" => $body));
      // Redirect with success message
      return Redirect::route('guest_book')
              ->with('success_message', "Votre message a été ajouté dans le livre d'or.");
    } catch (Exception $e) {
      Log::error($e);
      LogEntry::error("Livre d'or", "Erreur lors de l'ajout d'une entrée dans le livre d'or", array("Auteur" => $author, "Message" => $body));
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
      LogEntry::log("Livre d'or", "Suppression d'une entrée du livre d'or", array("Auteur" => $entry->author, "Message" => $entry->body));
      return Redirect::route('edit_guest_book')
            ->with('success_message', "Le message a été supprimé.");
    }
    LogEntry::error("Livre d'or", "Erreur lors de la suppression d'une entrée du livre d'or");
    return Redirect::route('edit_guest_book')
          ->with('error_message', "Une erreur est survenue. Le message n'a pas été supprimé.");
  }
  
}
