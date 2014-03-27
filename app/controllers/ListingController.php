<?php

class ListingController extends BaseController {
  
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LISTING)) {
      return App::abort(404);
    }
    
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    
    $sectionArray = array();
    $editableMembers = array();
    foreach ($sections as $section) {
      $members = Member::where('validated', '=', true)
              ->where('section_id', '=', $section->id)
              ->where('is_leader', '=', false)
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
      $showTotem = false;
      $showSubgroup = false;
      foreach ($members as $member) {
        if ($member->totem) $showTotem = true;
        if ($member->subgroup) $showSubgroup = true;
        if ($this->user->isOwnerOfMember($member))
          $editableMembers[] = $member;
      }
      $sectionArray[] = array(
          'section_data' => $section,
          'members' => $members,
          'show_totem' => $showTotem,
          'show_subgroup' => $showSubgroup,
      );
    }
    
    return View::make('pages.listing.listing', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                        || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_change_section' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'sections' => $sectionArray,
        'editable_members' => $editableMembers,
        'subgroup_choices' => $this->createSubgroupList(),
        'subgroup_name' => $this->section->subgroup_name,
    ));
  }
  
  public function downloadListing($section_slug, $format = "pdf") {
    if (!$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    ListingPDF::downloadListing($sections, $format);
  }
  
  public function showEdit() {
    
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $members = Member::where('validated', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('is_leader', '=', false)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    
    return View::make('pages.listing.editListing', array(
        'members' => $members,
        'can_change_section' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1),
        'subgroup_choices' => $this->createSubgroupList(),
        'subgroup_name' => $this->section->subgroup_name,
    ));
    
  }
  
  private function createSubgroupList() {
    $subgroups = DB::table('members')
            ->select('subgroup')
            ->distinct()
            ->where('section_id', '=', $this->section->id)
            ->get();
    $subgroupList = array("" => "(Sélectionner)");
    foreach ($subgroups as $subgroup) {
      if ($subgroup->subgroup)
        $subgroupList[$subgroup->subgroup] = $subgroup->subgroup;
    }
    if (count($subgroupList) == 1) $subgroupList = null;
    return $subgroupList;
  }
  
  public function submit() {
    $memberId = Input::get('member_id');
    $sectionId = Input::get('section_id');
    
    $member = Member::find($memberId);
    if ($member) {
      
      $fullPrivileges = false;
      $leaderPrivileges = false;
      $memberPrivileges = false;
      
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section)) {
        $fullPrivileges = true;
        $leaderPrivileges = true;
        $memberPrivileges = true;
      }
      
      if ($this->user->isOwnerOfMember($memberId)) {
        $memberPrivileges = true;
      }
      
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $member->section)) {
        $leaderPrivileges = true;
      }
      
      if (!$fullPrivileges && !$memberPrivileges && !$leaderPrivileges) {
        return Helper::forbiddenResponse();
      }

      
      $result = $member->updateFromInput($memberPrivileges, true, $fullPrivileges, $leaderPrivileges, $leaderPrivileges);
      if ($result === true) {
        $success = true;
        $message = "Les données ont été modifiées.";
      } else {
        $success = false;
        $message = $result ? $result : "Une erreur est survenue. Les données n'ont pas été modifiées.";
      }
    } else {
      // Member not found
      $success = false;
      $message = "Une erreur est survenue. Les données n'ont pas été modifiées.";
    }
    
    if ($success)
      return Redirect::to(URL::to(URL::previous()))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())
            ->with($success ? 'success_message' : 'error_message', $message)
            ->withInput();
  }
  
  public function deleteMember($member_id) {
    $member = Member::find($member_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      try {
        $member->delete();
        return Redirect::route('manage_listing')
                ->with('success_message', $member->first_name . " " . $member->last_name
                        . " a été supprimé" . ($member->gender == 'F' ? 'e' : '') . " définitivement du listing.");
      } catch (Exception $ex) {
      }
    }
    return Redirect::route('manage_listing')
            ->with('error_message', "Une erreur est survenue. Le membre n'a pas été supprimé.");
  }
  
  public function downloadFullListing($format, $section_slug) {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    ListingPDF::downloadListing($sections, $format, true);
  }
  
  public function downloadEnvelops($format, $section_slug) {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    EnvelopsPDF::downloadEnvelops($sections, $format);
  }
  
}
