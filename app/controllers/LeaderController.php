<?php

class LeaderController extends BaseController {
  
  public function showPage($archive = null) {
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $countInCharge = 0;
    $countOthers = 0;
    $menInCharge = false; // Whether there is at least one male in charge
    $menInOthers = false; // Whether there is at least one male in the others
    foreach ($leaders as $leader) {
      if ($leader->leader_in_charge) {
        $countInCharge++;
        if ($leader->gender != 'F') $menInCharge = true;
      } else {
        $countOthers++;
        if ($leader->gender != 'F') $menInOthers = true;
      }
    }
    
    return View::make('pages.leader.leaders', array(
        'is_leader' => $this->user->isLeader(),
        'leaders' => $leaders,
        'count_in_charge' => $countInCharge,
        'count_others' => $countOthers,
        'men_in_charge' => $menInCharge,
        'men_in_others' => $menInOthers,
    ));
  }
  
  public function showEdit($section_slug, $memberId = false) {
    
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    $scouts = Member::where('is_leader', '=', false)
            ->where('validated', '=', true)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    $scoutsForSelect = array('' => 'Sélectionne un scout');
    foreach ($scouts as $scout) {
      $scoutsForSelect[$scout->id] = $scout->last_name . " " . $scout->first_name;
    }
    
    if ($memberId) {
      $memberToTurnLeader = Member::where('is_leader', '=', false)
              ->where('validated', '=', true)
              ->where('id', '=', $memberId)
              ->first();
      if ($memberToTurnLeader) $leaders[] = $memberToTurnLeader;
    }
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
        'scouts' => $scoutsForSelect,
        'scout_to_leader' => $memberId,
    ));
  }
  
  public function showMemberToLeader($member_id) {
    return $this->showEdit($this->section->slug, $member_id);
  }
  
  public function postMemberToLeader($section_slug) {
    $memberId = Input::get('member_id');
    if ($memberId) {
      return Redirect::route('edit_leaders_member_to_leader',
              array('member_id' => $memberId, 'section_slug' => $section_slug));
    } else {
      return Redirect::route('edit_leaders', array('section_slug' => $section_slug));
    }
  }

  public function showEditPrivileges() {
    
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
    ));
  }
  
  public function getLeaderPicture($leader_id) {
    $leader = Member::find($leader_id);
    if ($leader && $leader->is_leader && $leader->has_picture) {
      $path = $leader->getPicturePath();
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
  public function submitLeader() {
    $memberId = Input::get('member_id');
    $sectionId = Input::get('section_id');
    
    $editionLevel = $this->editionLevelAllowed($memberId, $sectionId);
    if (!$editionLevel) {
      return Redirect::to(URL::previous())
              ->withInput()
              ->with('error_message', "Tu n'as pas le droit de faire cette modification.");
    }
    
    if ($memberId) {
      // Existing leader
      $leader = Member::find($memberId);
      if ($leader) {
        $result = $leader->updateFromInput($editionLevel == "full", true, $editionLevel == "full", true, true);

        if ($result === true) {
          $success = true;
          $message = "Les données de l'animateur ont été modifiées.";
        } else {
          $success = false;
          $message = $result ? $result : "Une erreur est survenue. Les données n'ont pas été enregistrées.";
        }
      } else {
        // Member not found
        $success = false;
        $message = "Une erreur est survenue. Les données n'ont pas été enregistrées.";
      }
    } else {
      // New leader
      if ($editionLevel != "full") {
        return Redirect::to(URL::previous())
                ->with('error_message', "Tu n'as pas le droit d'inscrire un nouvel animateur.");
      }

      $result = Member::createFromInput(true);
      if ($result === true) {
        $success = true;
        $message = "L'animateur a été ajouté au listing.";
      } else {
        $success = false;
        $message = $result ? $result : "Une erreur est survenue. L'animateur n'a pas été ajouté.";
      }
    }
        
    if ($success)
      return Redirect::to(URL::route('edit_leaders', array('section_slug' => $leader->getSection()->slug)))
              ->with($success ? 'success_message' : 'error_message', $message);
    else
      return Redirect::to(URL::previous())
            ->with($success ? 'success_message' : 'error_message', $message)
            ->withInput();
  }
  
  private function editionLevelAllowed($memberId, $sectionId) {
    if (!$memberId) {
      // Creating new leader
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        // Full edition is required to create a member
        return "full";
      } else {
        return false;
      }
    } else{
      // Edit a member
      $existingMember = Member::find($memberId);
      if (!$existingMember) return "full"; // Let the process continue, it will fail later anyway
      $memberSectionId = $existingMember->section_id;
      
      // Check if the user has full edit privileges
      if ($this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_ALL, $memberSectionId)) {
        return "full";
      }
      
      // Check if the user is modifying their own member entry
      if ($this->user->can(Privilege::$UPDATE_OWN_LISTING_ENTRY, $sectionId) &&
              $sectionId == $memberSectionId &&
              $user->isOwnerOfMember($memberId)) {
        return "full";
      }
      
      // Check if the user has limited edit privileges
      if ($this->user->can(Privilege::$EDIT_LISTING_LIMITED, $sectionId) &&
              $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $memberSectionId)) {
        return "limited";
      }
      
      // None of the above apply
      return false;
    }
    
    
  }
  
}
