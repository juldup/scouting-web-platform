<?php

class SuggestionController extends BaseController {
  
  public function showPage($section_slug = null, $managing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      return App::abort(404);
    }
    $suggestions = Suggestion::orderBy('id', 'DESC')->get();
    return View::make('pages.suggestions.suggestions', array(
        'suggestions' => $suggestions,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$MANAGE_SUGGESIONS, 1),
    ));
  }
  
  public function submit() {
    $body = Input::get('body');
    if (!$body) return Redirect::route('suggestions');
    Suggestion::create(array(
        'body' => $body,
        'user_id' => $this->user->isConnected() ? $this->user->id : null,
    ));
    return Redirect::route('suggestions')
            ->with('success_message', "Votre suggestion a été enregistrée.");
  }
  
  public function showEdit($section_slug = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      return App::abort(404);
    }
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    return $this->showPage($section_slug, true);
  }
  
  public function deleteSuggestion($suggestion_id) {
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    $suggestion = Suggestion::find($suggestion_id);
    if ($suggestion) {
      $suggestion->delete();
      return Redirect::route('edit_suggestions')
            ->with('success_message', "La suggestion a été supprimée.");
    }
    return Redirect::route('edit_suggestions')
          ->with('error_message', "La suggestion n'a pas été supprimée.");
  }
  
  public function submitResponse($suggestion_id) {
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    $suggestion = Suggestion::find($suggestion_id);
    if ($suggestion) {
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
