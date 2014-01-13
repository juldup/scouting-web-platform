<?php

class DocumentController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    
    $thisYear = Helper::thisYear();
    
    $documents = Document::where(function($query) use ($thisYear) {
      $query->where('archive', '=', $thisYear);
      $query->orWhere('archive', '=', '');
    })->where('section_id', '=', $this->section->id)->get();
    
    $documentSelectList = array();
    foreach ($documents as $document) {
      $documentSelectList[$document->id] = $document->title;
    }
    
    return View::make('pages.documents.documents', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_DOCUMENTS, $this->section),
        'edit_url' => URL::route('manage_documents', array('section_slug' => $this->section->slug)),
        'documents' => $documents,
        'documentSelectList' => $documentSelectList,
    ));
  }
  
  public function showEdit() {
    
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    
    $thisYear = Helper::thisYear();
    
    $documents = Document::where(function($query) use ($thisYear) {
      $query->where('archive', '=', $thisYear);
      $query->orWhere('archive', '=', '');
    })->where('section_id', '=', $this->section->id)->get();
    
    return View::make('pages.documents.editDocuments', array(
        'page_url' => URL::route('documents', array('section_slug' => $this->section->slug)),
        'documents' => $documents,
    ));
  }
  
  public function downloadDocument($document_id) {
    $document = Document::find($document_id);
    if (!$document) throw new NotFoundException();
    
    if (!$document->public && !$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    
    $path = $document->getPath();
    $filename = str_replace("\"", "", $document->filename);
    if (file_exists($path)) {
      return Response::make(file_get_contents($path), 200, array(
          'Content-Type' => 'application/octet-stream',
          'Content-length' => filesize($path),
          'Content-Transfer-Encoding' => 'Binary',
          'Content-disposition' => "attachment; filename=\"$filename\"",
      ));
    } else {
      return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe plus");
    }
    
  }
  
  public function sendByEmail() {
    $email = strtolower(Input::get('email'));
    $documentId = Input::get('document_id');
    if ($email == "") {
      return Redirect::to(URL::previous())->with('error_message', "Veuillez entrer une adresse e-mail pour recevoir le document.")->withInput();
    } if (Member::existWithEmail($email)) {
      // Send document by e-mail
      return Redirect::to(URL::previous())->with('success_message', "Le document vous a été envoyé à l'adresse <strong>$email</strong>.");
    } else {
      return Redirect::to(URL::previous())->with('error_message', "Désolés, l'adresse <strong>$email</strong> ne fait pas partie de notre listing.")->withInput();
    }
  }
  
  public function submitDocument($section_slug) {
    
    $docId = Input::get('doc_id');
    $title = Input::get('doc_title');
    $description = Input::get('description');
    $public = Input::get('public');
    $file = Input::file('document');
    $filename = Input::get('filename');
    $actualFileName = ($file ? $file->getClientOriginalName() : null);
    
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $success = false;
    if (!$title) {
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else {
      if ($docId) {
        $document = Document::find($docId);
        if ($document) {
          if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $document->getSection())) {
            return Helper::forbiddenResponse();
          }
          $document->title = $title;
          $document->description = $description;
          $document->public = $public;
          $document->setFileName($filename, $actualFileName);
          try {
            $document->save();
            $success = true;
            
            // Move file
            if ($file != null) {
              $file->move($document->getPathFolder(), $document->getPathFilename());
            }
            
            $message = "Le document a été mis a jour.";
            $section_slug = $document->getSection()->slug;
          } catch (Exception $e) {
            $success = false;
            $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
          }
        } else {
          $success = false;
          $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
        }
      } else {
        if ($file == null) {
          $success = false;
          $message = "Tu n'as pas joint de document.";
        } else {
          $document = null;
          try {
            // Create document
            $document = Document::create(array(
                'doc_date' => date('Y-m-d'),
                'title' => $title,
                'description' => $description,
                'public' => $public,
                'filename' => 'document',
                'section_id' => $this->section->id,
            ));
            // Move file
            $file->move($document->getPathFolder(), $document->getPathFilename());
            // Save filename
            $document->setFilename($filename, $actualFileName);
            $document->save();
            $success = true;
            $message = "Le document a été créé.";
          } catch (Exception $e) {
            $success = false;
            $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
            // Try deleting created document if it exists
            if ($document) {
              try {
                $document->delete();
              } catch (Exception $e) {
              }
            }
          }
        }
      }
    }
    
    $response = Redirect::route('manage_documents', array(
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) return $response;
    else return $response->withInput();
  }
  
  public function deleteDocument($document_id) {
    
    $document = Document::find($document_id);
    
    if (!$document) {
      App::abort(404, "Ce document n'existe pas.");
    }
    
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $document->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    try {
      unlink($document->getPath());
      $document->delete();
      $success = true;
      $message = "Le document a été supprimé.";
    } catch (Exception $e) {
      $success = false;
      $message = "Une erreur s'est produite. Le document n'a pas été supprimé.";
    }
    
    return Redirect::route('manage_documents', array(
        "section_slug" => $document->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
