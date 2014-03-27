<?php

class SectionDataController extends BaseController {
  
  public function showPage() {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    $sections = Section::where('id', '!=', 1)
            ->orderBy('position')
            ->get();
    return View::make('pages.sections.editSections', array(
        'sections' => $sections,
    ));
  }
  
  public function submitSectionData() {
    $sectionId = Input::get('section_id');
    $name = Input::get('section_name');
    $slug = Helper::slugify($name);
    $email = Input::get('section_email');
    $sectionType = Input::get('section_type');
    $sectionTypeNumber = Input::get('section_type_number');
    $color = Input::get('section_color');
    $la_section = Input::get('section_la_section');
    $de_la_section = Input::get('section_de_la_section');
    $subgroup_name = Input::get('section_subgroup_name');
    
    // Check validity
    $errorMessage = "";
    // Name
    if (!$name)
      $errorMessage .= "Le nom de la section ne peut pas être vide. ";
    elseif (!Helper::hasCorrectCapitals($name))
      $errorMessage .= "L'usage des majuscules dans le nom de la section n'est pas correct. ";
    // Slug
    $identicalSlugCount = Section::where('id', '!=', $sectionId)
            ->where('slug', '=', $slug)
            ->count();
    if ($identicalSlugCount != 0)
      $errorMessage .= "Il y a déjà une section portant ce nom. ";
    // E-mail
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL))
      $errorMessage .= "L'adresse e-mail n'est pas valide.";
    // Color
    if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color))
      $errorMessage .= "La couleur n'est pas valide. ";
    // "la section"
    if (!$la_section)
      $errorMessage .= "\"la section\" ne peut être vide. ";
    // "de la section"
    if (!$de_la_section)
      $errorMessage .= "\"de la section\" ne peut être vide. ";
    // Subgroup name
    $subgroup_name = ucfirst($subgroup_name);
    if ($subgroup_name && !Helper::hasCorrectCapitals($subgroup_name))
      $errorMessage .= "L'usage des majuscules dans le nom des sous-groupes n'est pas correct. ";
    
    if ($errorMessage) {
      return Redirect::route('section_data')
              ->withInput()
              ->with('error_message', $errorMessage);
    }
    
    if ($sectionId) {
      // Modify a section
      if (!$this->user->can(Privilege::$MANAGE_SECTIONS, $sectionId)) {
        return Helper::forbiddenResponse();
      }
      $section = Section::find($sectionId);
      if ($section) {
        $section->name = $name;
        $section->slug = $slug;
        $section->email = $email;
        $section->section_type = $sectionType;
        $section->section_type_number = $sectionTypeNumber;
        $section->color = $color;
        $section->la_section = $la_section;
        $section->de_la_section = $de_la_section;
        $section->subgroup_name = $subgroup_name;

        if (!$errorMessage) {
          try {
            $section->save();
            return Redirect::route('section_data')->with('success_message', "Les changements ont été enregistrés.");
          } catch (Exception $e) {
          }
        }
        return Redirect::route('section_data')
                ->withInput()
                ->with('error_message', "Une erreur est survenue.");
      } else {
        return Redirect::route('section_data')
                  ->with('error_message', "Une erreur est survenue : la section n'existe pas.");
      }
    } else {
      // Create a new section
      if (!$this->user->can(Privilege::$MANAGE_SECTIONS, 1)) {
        return Helper::forbiddenResponse();
      }
      $section = new Section();
      $section->name = $name;
      $section->slug = $slug;
      $section->email = $email;
      $section->section_type = $sectionType;
      $section->section_type_number = $sectionTypeNumber;
      $section->color = $color;
      $section->la_section = $la_section;
      $section->de_la_section = $de_la_section;
      $section->subgroup_name = $subgroup_name;
      try {
        $section->save();
        $section->position = $section->id;
        $section->save();
        return Redirect::route('section_data')->with('success_message', "La section a été créée avec succès. N'oublie pas de mettre à jour l'ordre des sections.");
      } catch (Exception $ex) {
      }
      return Redirect::route('section_data')
                ->withInput()
                ->with('error_message', "Une erreur est survenue. La section n'a pas pu être créée.");
    }
  }
  public function changeSectionOrder() {
    $errorResponse = json_encode(array("result" => "Failure"));
    
    // Check that the user has the right to modify the section order
    if (!$this->user->can(Privilege::$MANAGE_SECTIONS, 1)) {
      return $errorResponse;
    }
    
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
        return $errorResponse;
      }
    }
    
    return json_encode(array('result' => "Success"));

  }
  
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
      return Redirect::route('section_data')
              ->with('success_message', "La section " . $section->name . " a été supprimée avec succès.");
    } catch (Exception $e) {
    }
    return Redirect::route('section_data')
            ->withInput()
            ->with('error_message', "La section " . $section->name . " n'a été supprimée.");
  }
  
}