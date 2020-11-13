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
 * Methods for managing the list of former leaders.
 */
class ArchivedLeaderController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows a page containing the leaders of a section.
   * Can also show archived leaders of a previous year.
   * 
   * @param string $archive  The archive ('YYYY-YYYY') to show, or null for the current leaders
   */
  public function showPage($section_slug = null, $archive = null) {
    // Make sure the user has access to this page
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    // List leaders
    if ($archive == null) $archive = DateHelper::getLastYearForArchiving();
    $leaders = ArchivedLeader::where('section_id', '=', $this->section->id)
              ->where('year', '=', $archive)
              ->orderBy('leader_in_charge', 'DESC')
              ->orderBy('leader_name', 'ASC')
              ->get();
    $lastYear = DateHelper::getLastYearForArchiving();
    $firstYearLeader = ArchivedLeader::orderBy('year')->first();
    $firstYear = ($firstYearLeader ? $firstYearLeader->year : $lastYear);
    $archives = [];
    for ($year = substr($lastYear, 0, 4); $year >= substr($firstYear, 0, 4) - 1 && $year > 1900; $year--) {
      $archives[] = $year . "-" . ($year + 1);
    }
    // Make view
    return View::make('pages.leader.editArchivedLeaders', array(
        'leaders' => $leaders,
        'archive' => $archive,
        'archives' => $archives,
    ));
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
   * [Route] Used to submit the modified data of an archived leader or add a new archived leader
   */
  public function submitLeader($archive) {
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    // Check data integrity
    $inputData = ArchivedLeader::checkInputData();
    if (is_string($inputData)) {
      $success = false;
      $message = $inputData;
    } else {
      $inputData['year'] = $archive;
      if (!array_key_exists('section_id', $inputData) || !$inputData['section_id']) {
        $inputData['section_id'] = $this->section->id;
      }
      // Get the leader from input data
      $memberId = Input::get('member_id');
      // Update database
      if ($memberId) {
        // Existing leader
        $leader = ArchivedLeader::find($memberId);
        if ($leader) {
          // Update existing leader
          $leader->update($inputData);
          // Save
          try {
            $leader->save();
            if ($leader->uploadPictureFromInput()) {
              $success = true;
              $message = "Les données de l'animateur ont été modifiées.";
            } else {
              $success = false;
              $message = "La photo n'a pas pu être enregistrée.";
            }
          } catch (Exception $ex) {
            Log::error($ex);
            $success = false;
            $message = "Une erreur est survenue. Les données n'ont pas été enregistrées.";
          }
        } else {
          // Member not found
          $success = false;
          $message = "Une erreur est survenue. Les données n'ont pas été enregistrées.";
        }
      } else {
        // New leader
        $leader = ArchivedLeader::create($inputData);
        $success = true;
        $message = "L'animateur a été ajouté au listing.";
      }
    }
    // Redirect with status message
    if ($success) {
      $section = Section::find($leader->section_id);
      LogEntry::log("Animateurs", $memberId ? "Modification d'un ancien animateur" : "Ajout d'un ancien animateur",
              array("Nom" => Input::get('first_name') . " " . Input::get('last_name'), "Section" => $section->name)); // TODO improve log message
      return Redirect::to(URL::route('edit_archived_leaders', array('section_slug' => $section->slug, 'archive' => $archive)))
              ->with('success_message', $message);
    } else {
      return Redirect::to(URL::previous())
            ->with('error_message', $message)
            ->withInput();
    }
  }
  
  /**
   * [Route] Deletes an archived leader
   */
  public function deleteLeader($leader_id, $section_slug, $archive) {
    // Make sure the current user can delete this leader
    if (!$this->user->can(Privilege::$EDIT_LISTING_ALL, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get leader to delete
    $member = ArchivedLeader::find($leader_id);
    // Delete leader
    try {
      $member->delete();
      LogEntry::log("Animateurs", "Suppression d'un ancien animateur", array("Nom" => $member->first_name . " " . $member->last_name, "Année" => $member->year));
      return Redirect::route('edit_archived_leaders', array('section_slug' => $this->section->slug, 'archive' => $archive))
              ->with('success_message', $member->first_name . " " . $member->last_name
                      . " a été supprimé" . ($member->gender == 'F' ? 'e' : '') . " définitivement des anciens animateurs (" . $member->year . ").");
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Animateurs", "Erreur lors de la suppression d'un ancien animateur", array("Erreur" => $ex->getMessage()));
    }
    return Redirect::route('edit_archived_leaders', array('section_slug' => $this->section->slug, 'archive' => $archive))
            ->with('error_message', "Une erreur est survenue. L'ancien animateur n'a pas été supprimé.");
  }
  
}
