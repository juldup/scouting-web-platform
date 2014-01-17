<?php

class SuggestionController extends BaseController {
  
  public function showPage($section_slug = null, $managing = false) {
    $suggestions = Suggestion::orderBy('id', 'DESC')->get();
    return View::make('pages.suggestions.suggestions', array(
        'suggestions' => $suggestions,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$MANAGE_SUGGESIONS, 1),
    ));
  }
  
  public function showEdit($section_slug = null) {
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    return $this->showPage($section_slug, true);
  }
  
}
