<?php

class ContactController extends BaseController {
  
  public function showPage() {
    
    // Find unit staff
    $unitLeaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', '1')
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'desc')
            ->orderBy('leader_name')
            ->get();
    
    // Find sections' leaders in charge
    $sectionLeaders = Member::leftJoin('sections', "section_id", '=', "sections.id")
            ->where('is_leader', '=', true)
            ->where('leader_in_charge', '=', true)
            ->where('section_id', '!=', 1)
            ->where('validated', '=', true)
            ->orderBy('sections.position')
            ->get();
    
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
