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
 * The unit is composed of sections. These sections can be created, deleted and
 * parametered by the leaders with this controller.
 */
class SectionDataController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Show the section management page
   */
  public function showPage() {
    // Make sure the user is a leader and therefore has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get list of sections
    $sections = Section::where('id', '!=', 1)
            ->orderBy('position')
            ->get();
    // Make view
    return View::make('pages.sections.editSections', array(
        'sections' => $sections,
    ));
  }
  
  /**
   * [Route] Updates the data of a given section
   */
  public function submitSectionData() {
    // Get input data
    $sectionId = Input::get('section_id');
    $name = Input::get('section_name');
    $email = Input::get('section_email');
    $sectionCategory = Input::get('section_category');
    $sectionType = Input::get('section_type');
    $sectionTypeNumber = Input::get('section_type_number');
    $color = Input::get('section_color');
    $calendarShortname = Input::get('section_calendar_shortname');
    $la_section = Input::get('section_la_section');
    $de_la_section = Input::get('section_de_la_section');
    $subgroup_name = Input::get('section_subgroup_name');
    $start_age = intval(Input::get('section_start_age'));
    // Get whether the user can change all section data or only limited data
    $fullEdit = $this->user->can(Privilege::$MANAGE_SECTIONS, $sectionId);
    // Check validity
    $errorMessage = "";
    if ($fullEdit) {
      // Name
      if (!$name)
        $errorMessage .= "Le nom de la section ne peut pas être vide. ";
      elseif (!Helper::hasCorrectCapitals($name))
        $errorMessage .= "L'usage des majuscules dans le nom de la section n'est pas correct. ";
      // Slug
      $slug = Helper::slugify($name);
      $identicalSlugCount = Section::where('id', '!=', $sectionId)
              ->where('slug', '=', $slug)
              ->count();
      if ($identicalSlugCount != 0)
        $errorMessage .= "Il y a déjà une section portant ce nom. ";
    }
    // E-mail
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail n'est pas valide.";
    if ($fullEdit) {
      // Color
      if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color))
        $errorMessage .= "La couleur n'est pas valide. ";
      // "la section"
      if (!$la_section)
        $errorMessage .= "\"la section\" ne peut être vide. ";
      // "de la section"
      if (!$de_la_section)
        $errorMessage .= "\"de la section\" ne peut être vide. ";
    }
    // Subgroup name
    $subgroup_name = ucfirst($subgroup_name);
    if ($subgroup_name && !Helper::hasCorrectCapitals($subgroup_name))
      $errorMessage .= "L'usage des majuscules dans le nom des sous-groupes n'est pas correct. ";
    // If validity check did not pass, redirect with error message
    if ($errorMessage) {
      return Redirect::route('section_data')
              ->withInput()
              ->with('error_message', $errorMessage);
    }
    // Update or create section
    if ($sectionId) {
      // Updating a section
      // Make sure the user can update this section, at least partially
      if (!$this->user->can(Privilege::$MANAGE_SECTIONS, $sectionId) && !$this->user->can(Privilege::$EDIT_SECTION_EMAIL_AND_SUBGROUP, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      // Get section
      $section = Section::find($sectionId);
      if ($section) {
        // Update section data
        if ($fullEdit) {
          $section->name = $name;
          $section->slug = $slug;
          $section->section_category = $sectionCategory;
          $section->section_type = $sectionType;
          $section->section_type_number = $sectionTypeNumber;
          $section->color = $color;
          $section->calendar_shortname = $calendarShortname;
          $section->la_section = $la_section;
          $section->de_la_section = $de_la_section;
          $section->start_age = $start_age ? $start_age : null;
        }
        $section->email = $email;
        $section->subgroup_name = $subgroup_name;
        // Save and redirect with success message
        try {
          $section->save();
          LogEntry::log("Sections", "Modification des données d'une section", array("Section" => $section->name)); // TODO improve log message
          return Redirect::route('section_data')->with('success_message', "Les changements ont été enregistrés.");
        } catch (Exception $e) {
          // An error has occured while saving
          Log::error($e);
          LogEntry::error("Sections", "Erreur lors de la modification des données d'une section", array("Erreur" => $e->getMessage()));
          return Redirect::route('section_data')
                  ->withInput()
                  ->with('error_message', "Une erreur est survenue.");
        }
      } else {
        // The section was not found, redirect with error message
        return Redirect::route('section_data')
                  ->with('error_message', "Une erreur est survenue : la section n'existe pas.");
      }
    } else {
      // Creating a new section
      // Make sure the user can create a section
      if (!$this->user->can(Privilege::$MANAGE_SECTIONS, 1)) {
        return Helper::forbiddenResponse();
      }
      // Create section
      $section = new Section();
      $section->name = $name;
      $section->slug = $slug;
      $section->email = $email;
      $section->section_category = $sectionCategory;
      $section->section_type = $sectionType;
      $section->section_type_number = $sectionTypeNumber;
      $section->color = $color;
      $section->la_section = $la_section;
      $section->de_la_section = $de_la_section;
      $section->subgroup_name = $subgroup_name;
      try {
        $section->save();
        // Set position equal to id to start with, the section will come last in position
        $section->position = $section->id;
        $section->save();
        // Log
        LogEntry::log("Sections", "Création d'une nouvelle section", array("Section" => $name)); // TODO improve log message
        // Redirect with success message
        return Redirect::route('section_data')->with('success_message', "La section a été créée avec succès. N'oublie pas de mettre à jour l'ordre des sections.");
      } catch (Exception $ex) {
        // An error has occured, redirect with error message
        Log::error($ex);
        LogEntry::error("Sections", "Erreur lors de la création d'une nouvelle section", array("Erreur" => $ex->getMessage()));
        return Redirect::route('section_data')
                  ->withInput()
                  ->with('error_message', "Une erreur est survenue. La section n'a pas pu être créée.");
      }
    }
  }
  
  /**
   * [Route] Ajax call to save section order
   */
  public function changeSectionOrder() {
    // Prepare error response
    $errorResponse = json_encode(array("result" => "Failure"));
    // Check that the user has the right to modify the section order
    if (!$this->user->can(Privilege::$MANAGE_SECTIONS, 1)) {
      return $errorResponse;
    }
    // Get new order from input
    $sectionIdsInOrder = Input::get('section_order');
    $sectionIdsInOrderArray = explode(" ", $sectionIdsInOrder);
    // Retrieve sections
    $sections = Section::where('id', '!=', 1)->get();
    // Check that the number of sections corresponds
    if (count($sectionIdsInOrderArray) != count($sections)) {
      return $errorResponse;
    }
    // Check that all sections are in the list
    foreach ($sections as $section) {
      if (!in_array($section->id, $sectionIdsInOrderArray)) {
        return $errorResponse;
      }
    }
    // Get the list of positions
    $positions = array();
    foreach ($sections as $section) {
      $positions[] = $section->position;
    }
    // Sort positions
    sort($positions);
    // Assign new positions
    foreach ($sections as $section) {
      // Get new order of this section
      $index = array_search($section->id, $sectionIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $section->position = $positions[$index];
    }
    // Save all sections
    foreach ($sections as $section) {
      try {
        $section->save();
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Sections", "Erreur lors du réordonnancement des sections", array("Erreur" => $ex->getMessage()));
        return $errorResponse;
      }
    }
    // Log
    LogEntry::log("Sections", "Réordonnancement des sections"); // TODO improve log message
    // Return success message
    return json_encode(array('result' => "Success"));
  }
  
  /**
   * [Route] Deletes an existing section (if it does not contain any members)
   */
  public function deleteSection($section_id) {
    // Check that the user can delete a section
    if (!$this->user->can(Privilege::$MANAGE_SECTIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get the section
    $section = Section::find($section_id);
    if (!$section) {
      return Redirect::route('section_data')
              ->with('error_message', "Cette section n'existe pas.");
    }
    // Check that this section does not have any members
    $memberCount = Member::where("validated", '=', true)
            ->where('section_id', '=', $section_id)
            ->where('is_leader', '=', false)
            ->count();
    if ($memberCount) {
      return Redirect::route('section_data')
              ->with('error_message', "Cette section contient des membres. Il faut <a href='"
                      . URL::route('manage_listing', array('section_slug' => $section->slug)) . 
                      "'>supprimer ou changer de section tous les membres</a> avant de supprimer la section.");
    }
    // Check that this section does not have any leaders
    $memberCount = Member::where("validated", '=', true)
            ->where('section_id', '=', $section_id)
            ->where('is_leader', '=', true)
            ->count();
    if ($memberCount) {
      return Redirect::route('section_data')
              ->with('error_message', "Cette section contient des animateurs. Il faut <a href='"
                      . URL::route('edit_leaders', array('section_slug' => $section->slug)) . 
                      "'>supprimer ou changer de section tous les animateurs</a> avant de supprimer la section.");
    }
    // Delete section
    try {
      $section->delete();
      LogEntry::log("Sections", "Suppression d'une section", array("Section" => $section->name));
      return Redirect::route('section_data')
              ->with('success_message', "La section " . $section->name . " a été supprimée avec succès.");
    } catch (Exception $e) {
      Log::error($e);
      LogEntry::error("Sections", "Erreur lors de la suppression d'une section", array("Erreur" => $e->getMessage()));
      return Redirect::route('section_data')
              ->withInput()
              ->with('error_message', "La section " . $section->name . " n'a été supprimée.");
    }
  }
  
}