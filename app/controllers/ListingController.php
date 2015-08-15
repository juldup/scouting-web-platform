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
 * The listing is an access restricted page that presents the listing
 * of all the scouts.
 * 
 * This controller also offers tools to edit the listing.
 */
class ListingController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
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
    // Log
    LogEntry::log("Listing", "Téléchargement du listing", array("Section" => $this->section->name, "Format" => $format));
    // Output listing
    ListingPDF::downloadListing($sections, $format, false, true, $this->user->isLeader() ? true : false);
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
    if ($this->user->currentSection->id == 1) {
      $members = Member::where('validated', '=', true)
              ->where('is_leader', '=', false)
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
    } else {
      $members = Member::where('validated', '=', true)
              ->where('section_id', '=', $this->section->id)
              ->where('is_leader', '=', false)
              ->orderBy('last_name')
              ->orderBy('first_name')
              ->get();
    }
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
   * [Route] Updates the database with the modified data of a member
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
    if ($success) {
      LogEntry::log("Listing", "Modification d'un membre", array("Membre" => $member->getFullName())); // TODO improve log message
      return Redirect::to(URL::to(URL::previous()))
              ->with($success ? 'success_message' : 'error_message', $message);
    } else {
      return Redirect::to(URL::previous())
            ->with($success ? 'success_message' : 'error_message', $message)
            ->withInput();
    }
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
        LogEntry::log("Listing", "Suppression d'un membre", array("Membre" => $member->getFullName()));
        return Redirect::route('manage_listing')
                ->with('success_message', $member->getFullName()
                        . " a été supprimé" . ($member->gender == 'F' ? 'e' : '') . " définitivement du listing.");
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Listing", "Erreur lors de la suppression d'un membre", array("Membre" => $member->getFullName(), "Erreur" => $ex->getMessage()));
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
    LogEntry::log("Listing", "Téléchargement du listing complet", array("Section" => $this->section->name, "Format" => $format));
    ListingPDF::downloadListing($sections, $format, true, true, true);
  }
  
  /**
   * [Route] Outputs the listing of leaders to download (for leaders only)
   * 
   * @param string $format  The output format: "excel" or "csv"
   */
  public function downloadLeaderListing($section_slug, $format) {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    if ($this->section->id == 1) {
      $sections = Section::orderBy('position')
              ->get();
    } else {
      $sections = array($this->section);
    }
    LogEntry::log("Listing", "Téléchargement du listing des animateurs", array("Section" => $this->section->name, "Format" => $format));
    ListingPDF::downloadListing($sections, $format, $format != 'pdf', false, true);
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
    LogEntry::log("Listing", "Téléchargement des enveloppes", array("Section" => $this->section->name, "Format" => $format));
    // Generate and output envelops
    EnvelopsPDF::downloadEnvelops($sections, $format);
  }
  
  /**
   * [Route] Shows the listing download page
   */
  public function showDownloadListingPage() {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    return View::make('pages.listing.downloadListing');
  }
  
  /**
   * [Route] Post request to download the listing with options
   */
  public function downloadListingWithOptions() {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get sections to include
    $sections = Section::all();
    $selectedSections = array();
    foreach ($sections as $section) {
      if (Input::get('section_' . $section->id)) {
        $selectedSections[] = $section;
      }
    }
    // Get members to include
    $includeScouts = Input::get('include_scouts');
    $includeLeaders = Input::get('include_leaders');
    // Get format
    $format = Input::get('format');
    $full = Input::get('full');
    $groupBySection = Input::get('group_by_section');
    // Download the listing
    ListingPDF::downloadListing($selectedSections, $format, $full, $includeScouts, $includeLeaders, $groupBySection);
  }
  
  /**
   * [Route] Show the Desk listing page
   */
  public function showDeskPage() {
    if (!$this->user->isLeader() || !$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    ini_set("auto_detect_line_endings", true);
    // Save uploaded file
    if (Request::isMethod('post')) {
      $file = Input::file('listingFile');
      if ($file) {
        $dirname = storage_path("site_data/tmp/");
        $filename = "deskListing_" . date("Y-m-d_H-i-s") . "_" . substr(sha1(time() . rand(0, 999999999)), 0, 8) . ".csv";
        $file->move($dirname, $filename);
        // Remember file name in session
        Session::put('desk_listing_file', $dirname . $filename);
      }
      // Remember option
      Session::put('desk_listing_case_insensitive', Input::get('caseInsensitive'));
      Session::put('desk_listing_ignore_accent_errors', Input::get('ignoreAccentErrors'));
      Session::put('desk_listing_fuzzy_address_comparison', Input::get('fuzzyAddressComparison'));
      // Redirect to page with listing
      return Redirect::route('desk_listing');
    }
    // Get file and options
    $filename = Session::get('desk_listing_file');
    $caseInsensitive = Session::get('desk_listing_case_insensitive', false);
    $ignoreAccentErrors = Session::get('desk_listing_ignore_accent_errors', false);
    $fuzzyAddressComparison = Session::get('desk_listing_fuzzy_address_comparison', true);
    if ($filename && file_exists($filename)) {
      $separator = "\t";
      // File exists
      $fileDate = date("d/m/Y à H\hi", filemtime($filename));
      // Convert file CSV content to array
      $fileHandle = fopen($filename, 'r');
      $headers = fgetcsv($fileHandle, 0, $separator);
      $csv = array();
      while (($line = fgets($fileHandle)) !== false) {
        $csv[] = str_getcsv($line, $separator);
      }
      $deskListing = array();
      foreach ($csv as $memberRawData) {
        if (count($memberRawData) != count($headers)) {
          continue;
        }
        $memberData = array();
        foreach ($headers as $index => $fieldName) {
          if (strcasecmp($fieldName, 'Nom') == 0) {
            $memberData['last_name'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Prenom') == 0) {
            $memberData['first_name'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Sexe') == 0) {
            $memberData['gender'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Date de naissance') == 0) {
            $memberData['birth_date'] = Helper::dateToSql(trim($memberRawData[$index]));
          } elseif (strcasecmp($fieldName, 'Tél') == 0) {
            $memberData['phone1'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'GSM') == 0) {
            $memberData['phone2'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Email Tiers') == 0) {
            $memberData['email'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Rue') == 0) {
            $memberData['street'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'No') == 0) {
            $memberData['number'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Bte') == 0) {
            $memberData['mailbox'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Code Postal') == 0) {
            $memberData['postcode'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Ville') == 0) {
            $memberData['city'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Section') == 0) {
            $memberData['section'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Handicap') == 0) {
            $memberData['handicap'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Totem') == 0) {
            $memberData['totem'] = trim($memberRawData[$index]);
          } elseif (strcasecmp($fieldName, 'Quali') == 0) {
            $memberData['quali'] = trim($memberRawData[$index]);
          }
        }
        $deskListing[] = $memberData;
      }
      // Compare Desk listing with current listing
      $modifications = (new ListingComparison())->compareDeskListing($deskListing, !$caseInsensitive, $ignoreAccentErrors, $fuzzyAddressComparison);
      // Return comparison view
      return View::make('pages.listing.deskPage', array(
          'fileDate' => $fileDate,
          'modifications' => $modifications,
          'caseInsensitive' => $caseInsensitive,
          'ignoreAccentErrors' => $ignoreAccentErrors,
          'fuzzyAddressComparison' => $fuzzyAddressComparison,
      ));
    } else {
      // File not uploaded yet, show default page
      return View::make('pages.listing.deskPage', array(
          'caseInsensitive' => $caseInsensitive,
          'ignoreAccentErrors' => $ignoreAccentErrors,
          'fuzzyAddressComparison' => $fuzzyAddressComparison,
      ));
    }
  }
  
}
