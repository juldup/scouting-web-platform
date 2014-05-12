<?php

/**
 * The listing is an access restricted page that presents the listing
 * of all the scouts.
 * 
 * This controller also offers tools to edit the listing.
 */
class ListingController extends BaseController {
  
  /**
   * [Route] Shows the listing page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LISTING)) {
      return App::abort(404);
    }
    // Make sure the current user is a member an therefore has access to this page
    if (!$this->user->isMember()) {
      return Helper::forbiddenNotMemberResponse();
    }
    // Create an array containing the section(s) of which to display the listing
    if ($this->section->id == 1) {
      // All section
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      // Only current section
      $sections = array($this->section);
    }
    // Gather members per section
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
        // Allows the parents to edit their children's data
        if ($this->user->isOwnerOfMember($member)) {
          $editableMembers[] = $member;
        }
      }
      $sectionArray[] = array(
          'section_data' => $section,
          'members' => $members,
          'show_totem' => $showTotem,
          'show_subgroup' => $showSubgroup,
      );
    }
    // Make view
    return View::make('pages.listing.listing', array(
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                        || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section),
        'can_change_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'sections' => $sectionArray,
        'editable_members' => $editableMembers,
        'subgroup_choices' => $this->createSubgroupList(),
        'subgroup_name' => $this->section->subgroup_name,
    ));
  }
  
  /**
   * [Route] Outputs a simplified version of listing in PDF or Excel format for download
   * @param string $format  The output format: "pdf", "excel" or "csv"
   */
  public function downloadListing($section_slug, $format = "pdf") {
    // Make sure the user is a member and has access to the listing
    if (!$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    // Set list of sections to include
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    // Output listing
    ListingPDF::downloadListing($sections, $format);
  }
  
  /**
   * [Route] Displays the listing management page
   */
  public function showEdit() {
    // Make sure the user can edit the listing
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section) && !$this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Gather members
    $members = Member::where('validated', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('is_leader', '=', false)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    // Make view
    return View::make('pages.listing.editListing', array(
        'members' => $members,
        'can_change_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_edit_identity' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'subgroup_choices' => $this->createSubgroupList(),
        'subgroup_name' => $this->section->subgroup_name,
    ));
  }
  
  /**
   * Generate the list of existing subgroup to provide a selector to choose the subgroup of a member
   */
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
  
  /**
   * [Render] Updates the database with the modified data of a member
   */
  public function submit() {
    // Get member
    $memberId = Input::get('member_id');
    $sectionId = Input::get('section_id');
    $member = Member::find($memberId);
    // Update database with input data
    if ($member) {
      // Select the level of edition the current user has access to
      $sectionTransferPrivileges = false;
      $leaderPrivileges = false;
      $memberPrivileges = false;
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_ALL, $member->section)) {
        $leaderPrivileges = true;
        $memberPrivileges = true;
      }
      if ($this->user->can(Privilege::$SECTION_TRANSFER, 1)) {
        $sectionTransferPrivileges = true;
      }
      if ($this->user->isOwnerOfMember($memberId)) {
        $memberPrivileges = true;
      }
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $member->section)) {
        $leaderPrivileges = true;
      }
      // Make sure the user has some privileges
      if (!$sectionTransferPrivileges && !$memberPrivileges && !$leaderPrivileges) {
        return Helper::forbiddenResponse();
      }
      // Update mmember
      $result = $member->updateFromInput($memberPrivileges, true, $sectionTransferPrivileges, $leaderPrivileges, $leaderPrivileges);
      // Set status message
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
    // Redirect with status message
    if ($success)
      return Redirect::to(URL::to(URL::previous()))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())
            ->with($success ? 'success_message' : 'error_message', $message)
            ->withInput();
  }
  
  /**
   * [Route] Deletes a member from the listing
   */
  public function deleteMember($member_id) {
    // Get member and their section
    $member = Member::find($member_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      // Make sure the current user can delete this member
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Delete member
      try {
        $member->delete();
        return Redirect::route('manage_listing')
                ->with('success_message', $member->first_name . " " . $member->last_name
                        . " a été supprimé" . ($member->gender == 'F' ? 'e' : '') . " définitivement du listing.");
      } catch (Exception $ex) {
      }
    }
    // An error has occurred
    return Redirect::route('manage_listing')
            ->with('error_message', "Une erreur est survenue. Le membre n'a pas été supprimé.");
  }
  
  /**
   * [Route] Outputs the full listing to download (for leaders only)
   * 
   * @param string $format  The output format: "excel" or "csv"
   */
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
  
  /**
   * [Route] Downloads the members' addresses in envelop format
   * 
   * @param string $format  The envelop format: "c6" (C6) or "c5_6" (C5/6)
   */
  public function downloadEnvelops($format, $section_slug) {
    // Make sure the user is a leader
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Select section(s) to include
    if ($this->section->id == 1) {
      $sections = Section::where('id', '!=', 1)
              ->orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    // Generate and output envelops
    EnvelopsPDF::downloadEnvelops($sections, $format);
  }
  
}
