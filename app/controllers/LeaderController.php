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
 * Presents a list of the sections' leaders.
 * Also provides tools to manage the listing of leaders. Leaders can
 * be added, modified and deleted.
 */
class LeaderController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows a page containing the leaders of a section.
   * Can also show archived leaders of a previous year.
   * 
   * @param string $archive  The archive ('YYYY-YYYY') to show, or null for the current leaders
   */
  public function showPage($section_slug = null, $archive = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LEADERS)) {
      return App::abort(404);
    }
    
    if ($archive == null) {
      // Showing current year
      $leaders = Member::where('is_leader', '=', true)
              ->where('section_id', '=', $this->section->id)
              ->where('validated', '=', true)
              ->orderBy('leader_in_charge', 'DESC')
              ->orderBy('leader_name', 'ASC')
              ->get();
    } else {
      // Showing archive
      $leaders = ArchivedLeader::where('section_id', '=', $this->section->id)
              ->where('year', '=', $archive)
              ->orderBy('leader_in_charge', 'DESC')
              ->orderBy('leader_name', 'ASC')
              ->get();
    }
    // Count leaders in charge, other leaders, and gender for determining the titles
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
    // List existing archives
    $archives = ArchivedLeader::select('year')
            ->distinct()
            ->where('section_id', '=', $this->section->id)
            ->get();
    $archiveYears = array();
    foreach ($archives as $year) {
      if ($year->year != $archive) {
        $archiveYears[] = $year->year;
      }
    }
    sort($archiveYears);
    // Make view
    return View::make('pages.leader.leaders', array(
        'is_leader' => $this->user->isLeader(),
        'leaders' => $leaders,
        'count_in_charge' => $countInCharge,
        'count_others' => $countOthers,
        'men_in_charge' => $menInCharge,
        'men_in_others' => $menInOthers,
        'archives' => $archiveYears,
        'archive_year' => $archive,
    ));
  }
  
  /**
   * [Route] Shows the leader page of a previous year
   * 
   * @param string $year  The archive in 'YYYY-YYYY' format
   */
  public function showArchivedLeaders($year) {
    return $this->showPage($this->section->slug, $year);
  }
  
  /**
   * [Route] Shows the leader management page
   * 
   * @param integer $memberId  Optional: the id of a scout to turn into a leader
   */
  public function showEdit($section_slug = null, $memberId = false) {
    // Make sure the user has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // List leaders
    $leaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', $this->section->id)
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'DESC')
            ->orderBy('leader_name', 'ASC')
            ->get();
    // List scouts that could be turned into a leader
    $fifteenYearsAgo = date('Y-m-d', strtotime("-15 years"));
    $scouts = Member::where('is_leader', '=', false)
            ->where('validated', '=', true)
            ->where('birth_date', '<', $fifteenYearsAgo)
            ->orderBy('last_name', 'ASC')
            ->orderBy('first_name', 'ASC')
            ->get();
    $scoutsForSelect = array('' => 'Sélectionne un scout');
    foreach ($scouts as $scout) {
      $scoutsForSelect[$scout->id] = $scout->last_name . " " . $scout->first_name;
    }
    // Get scout to turn into a leader (if any)
    if ($memberId) {
      $memberToTurnLeader = Member::where('is_leader', '=', false)
              ->where('validated', '=', true)
              ->where('id', '=', $memberId)
              ->first();
      if ($memberToTurnLeader) $leaders[] = $memberToTurnLeader;
    }
    // Make view
    return View::make('pages.leader.editLeaders', array(
        'leaders' => $leaders,
        'scouts' => $scoutsForSelect,
        'scout_to_leader' => $memberId,
        'can_change_section' => $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'can_delete' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_edit_all' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_edit_limited' => $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section) || $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section),
        'can_edit_own_data' => $this->user->can(Privilege::$UPDATE_OWN_LISTING_ENTRY, $this->section),
        'can_add_leader' => $this->user->can(Privilege::$EDIT_LISTING_ALL, 1) && $this->user->can(Privilege::$SECTION_TRANSFER, 1),
    ));
  }
  
  /**
   * [Route] Shows the management page with a scout selected to be turned into a leader
   * 
   * @param integer $member_id  The id of the scout to turn into a leader
   */
  public function showMemberToLeader($member_id) {
    return $this->showEdit($this->section->slug, $member_id);
  }
  
  /**
   * [Route] Used when a scout to be turned into a leader is selected
   */
  public function postMemberToLeader($section_slug) {
    $memberId = Input::get('member_id');
    if ($memberId) {
      return Redirect::route('edit_leaders_member_to_leader',
              array('member_id' => $memberId, 'section_slug' => $section_slug));
    } else {
      return Redirect::route('edit_leaders', array('section_slug' => $section_slug));
    }
  }
  
  /**
   * [Route] Returns the picutre of a leader
   */
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
  
  /**
   * [Route] Returns the picture of an archived leader
   */
  public function getArchivedLeaderPicture($archived_leader_id) {
    $leader = ArchivedLeader::find($archived_leader_id);
    if ($leader && $leader->has_picture) {
      $path = $leader->getPicturePath();
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
  /**
   * [Route] Used to submit the modified data of a leader
   */
  public function submitLeader() {
    // Get the leader from input data
    $memberId = Input::get('member_id');
    $sectionId = Input::get('section_id');
    if (!$sectionId) $sectionId = $this->section->id;
    // Determine which fields can be edited by the current leader
    $editionLevel = $this->editionLevelAllowed($memberId, $sectionId);
    $canChangeSection = $this->user->can(Privilege::$SECTION_TRANSFER, 1);
    if (!$editionLevel) {
      return Redirect::to(URL::previous())
              ->withInput()
              ->with('error_message', "Tu n'as pas le droit de faire cette modification.");
    }
    // Update database
    if ($memberId) {
      // Existing leader
      $leader = Member::find($memberId);
      $wasLeaderBefore = $leader->is_leader;
      if ($leader) {
        // Update existing leader
        $result = $leader->updateFromInput($editionLevel == "full", true, $canChangeSection, true, true);
        if ($result === true) {
          if (!$wasLeaderBefore) Privilege::addBasePrivilegesForLeader($leader);
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
      // Create leader
      $result = Member::createFromInput(true);
      if (is_string($result)) {
        // An error has occured
        $success = false;
        $message = $result ? $result : "Une erreur est survenue. L'animateur n'a pas été ajouté.";
      } else {
        Privilege::addBasePrivilegesForLeader($result);
        $success = true;
        $message = "L'animateur a été ajouté au listing.";
      }
    }
    // Get section
    $section = Section::find(Input::get('section'));
    // Redirect with status message
    if ($success) {
      LogEntry::log("Animateurs", $memberId ? "Modification d'un animateur" : "Ajout d'un animateur",
              array("Nom" => Input::get('first_name') . " " . Input::get('last_name'), "Section" => $section->name));
      return Redirect::to(URL::route('edit_leaders', array('section_slug' => $section->slug)))
              ->with('success_message', $message);
    } else {
      return Redirect::to(URL::previous())
            ->with('error_message', $message)
            ->withInput();
    }
  }
  
  /**
   * [Route] Deletes a leader
   */
  public function deleteLeader($leader_id, $section_slug) {
    // Get leader to delete
    $member = Member::find($leader_id);
    $sectionId = $member ? $member->section_id : null;
    if ($sectionId) {
      // Make sure the current user can delete this leader
      if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Archive leaders if needed
      ArchivedLeader::archiveLeadersIfNeeded();
      // Delete leader
      try {
        $member->delete();
        LogEntry::log("Animateurs", "Suppression d'un animateur", array("Nom" => $member->getFullName()));
        return Redirect::route('edit_leaders')
                ->with('success_message', $member->getFullName()
                        . " a été supprimé" . ($member->gender == 'F' ? 'e' : '') . " définitivement du listing.");
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Animateurs", "Erreur lors de la suppression d'un animateur", array("Erreur" => $ex->getMessage()));
      }
    }
    return Redirect::route('edit_leaders')
            ->with('error_message', "Une erreur est survenue. L'animateur n'a pas été supprimé.");
  }
  
  /**
   * Determines the fields that can be edited by the current user for
   * the given member in the given section
   * @return string|boolean "full", "limited" or false
   */
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
              $this->user->isOwnerOfMember($memberId)) {
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
