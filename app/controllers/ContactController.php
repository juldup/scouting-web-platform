<?php

class ContactController extends BaseController {
  
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_CONTACTS)) {
      return App::abort(404);
    }
    
    // Find unit staff
    $unitLeaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', '1')
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'desc')
            ->orderBy('leader_name')
            ->get();
    
    // Find sections' leaders in charge
    $sections = Section::where('id', '!=', 1)
            ->orderBy('position')
            ->get();
    $sectionLeaders = array();
    foreach ($sections as $section) {
      $leader = Member::where('is_leader', '=', true)
              ->where('leader_in_charge', '=', true)
              ->where('validated', '=', true)
              ->where('section_id', '=', $section->id)
              ->first();
      if ($leader) $sectionLeaders[] = $leader;
    }
    
    return View::make('pages.contacts', array(
        "unitLeaders" => $unitLeaders,
        "sectionLeaders" => $sectionLeaders,
        "webmaster" => array(
            "name" => "Julien Dupuis",
            "phone" => "+32 496 628 600",
        )
    ));
  }
  
}
