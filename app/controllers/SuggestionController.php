<?php

/**
 * Visitors can leave suggestions about the website or the unit's activities.
 * 
 * This controller allows visitors to view and post suggestions, and leaders
 * to manage them.
 */
class SuggestionController extends BaseController {
  
  /**
   * [Route] Displays the suggestion page
   * 
   * @param boolean $managing  Whether the page is being shown in management mode
   */
  public function showPage($section_slug = null, $managing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      return App::abort(404);
    }
    // Get list of suggestions
    $suggestions = Suggestion::orderBy('id', 'DESC')->get();
    // Make view
    return View::make('pages.suggestions.suggestions', array(
        'suggestions' => $suggestions,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$MANAGE_SUGGESIONS, 1),
    ));
  }
  
  /**
   * [Route] Used to submit a new suggestion
   */
  public function submit() {
    // Get suggestion text
    $body = Input::get('body');
    if (!$body) return Redirect::route('suggestions');
    // Create suggestion
    Suggestion::create(array(
        'body' => $body,
        'user_id' => $this->user->isConnected() ? $this->user->id : null,
    ));
    // Redirect back with success message
    return Redirect::route('suggestions')
            ->with('success_message', "Votre suggestion a été enregistrée.");
  }
  
  /**
   * [Route] Shows the suggestion page in management mode
   */
  public function showEdit($section_slug = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      return App::abort(404);
    }
    // Make sure the user can manage the suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Show page in management mode
    return $this->showPage($section_slug, true);
  }
  
  /**
   * [Route] Deletes a suggestion
   */
  public function deleteSuggestion($suggestion_id) {
    // Make sure the user can delete suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get suggestion
    $suggestion = Suggestion::find($suggestion_id);
    // Delete suggestion
    if ($suggestion) {
      try {
        $suggestion->delete();
        return Redirect::route('edit_suggestions')
                ->with('success_message', "La suggestion a été supprimée.");
      } catch (Exception $e) {
      }
    }
    return Redirect::route('edit_suggestions')
          ->with('error_message', "La suggestion n'a pas été supprimée.");
  }
  
  /**
   * [Route] Used to submit a response to a suggestion
   */
  public function submitResponse($suggestion_id) {
    // Make sure the user can post responses to suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get suggestion
    $suggestion = Suggestion::find($suggestion_id);
    if ($suggestion) {
      // Save response
      try {
        $response = Input::get("response_$suggestion_id");
        $suggestion->response = $response;
        $suggestion->save();
        return Redirect::route('edit_suggestions')
              ->with('success_message', "La réponse a été enregistrée.");
      } catch (Exception $ex) {
      }
    }
    return Redirect::route('edit_suggestions')
          ->with('error_message', "Une erreur est survenue. La réponse n'a pas été enregistrée.");
  }
  
}
