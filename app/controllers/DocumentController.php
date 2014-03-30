<?php

class DocumentController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_DOCUMENTS)) {
      return App::abort(404);
    }
    
    $documents = Document::where('archived', '=', false)
            ->where('section_id', '=', $this->section->id)->get();
    
    // Generate document selector
    $documentSelectList = array();
    foreach ($documents as $document) {
      $documentSelectList[$document->id] = $document->title;
    }
    
    // Generate category list
    $documentsInCategories = $this->generateCategoryList($documents);
    
    return View::make('pages.documents.documents', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_DOCUMENTS, $this->section),
        'edit_url' => URL::route('manage_documents', array('section_slug' => $this->section->slug)),
        'documents' => $documentsInCategories,
        'documentSelectList' => $documentSelectList,
    ));
  }
  
  public function updateCategoryName($category) {
    if ($category == "Pour les scouts") {
      $category = "Pour les " . $this->section->getScoutName();
    }
    return $category;
  }
  
  public function generateCategoryList($documents) {
    // Create an array per category
    $documentsInCategories = array();
    $categories = explode(";", Parameter::get(Parameter::$DOCUMENT_CATEGORIES));
    foreach ($categories as $category) {
      $documentsInCategories[$this->updateCategoryName($category)] = array();
    }
    $documentsInCategories["Divers"] = array();
    // Put documents in categories and generate document selector
    $documentSelectList = array();
    foreach ($documents as $document) {
      $documentSelectList[$document->id] = $document->title;
      $category = $this->updateCategoryName($document->category);
      if (array_key_exists($category, $documentsInCategories)) {
        $documentsInCategories[$category][] = $document;
      } else {
        $documentsInCategories["Divers"][] = $document;
      }
    }
    // Remove empty categories
    foreach ($documentsInCategories as $category=>$docs) {
      if (count($docs) == 0) unset($documentsInCategories[$category]);
    }
    return $documentsInCategories;
  }
  
  public function showEdit() {
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    // Get documents
    $documents = Document::where('archived', '=', false)
            ->where('section_id', '=', $this->section->id)->get();
    // Sort documents per category
    $documentsInCategories = $this->generateCategoryList($documents);
    // Generate category list for select
    $categoriesRaw = explode(";", Parameter::get(Parameter::$DOCUMENT_CATEGORIES));
    $categories = array();
    foreach ($categoriesRaw as $category) {
      $categories[$category] = $this->updateCategoryName($category);
    }
    $categories["Divers"] = "Divers";
    // Generate view
    return View::make('pages.documents.editDocuments', array(
        'page_url' => URL::route('documents', array('section_slug' => $this->section->slug)),
        'documents' => $documents,
        'documents_in_categories' => $documentsInCategories,
        'categories' => $categories,
    ));
  }
  
  public function downloadDocument($document_id) {
    $document = Document::find($document_id);
    if (!$document) App::abort("Ce document n'existe plus.");
    
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
      return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe plus.");
    }
  }
  
  public function sendByEmail() {
    $emailAddress = strtolower(Input::get('email'));
    $documentId = Input::get('document_id');
    if ($emailAddress == "") {
      return Redirect::to(URL::previous())->with('error_message', "Veuillez entrer une adresse e-mail pour recevoir le document.")->withInput();
    } if (Member::existWithEmail($emailAddress)) {
      // Send document by e-mail
      $document = Document::find($documentId);
      if (!$document) return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe pas.")->withInput();
      $body = View::make('emails.sendDocument', array(
          'document' => $document,
          'website_name' => Parameter::get(Parameter::$UNIT_SHORT_NAME),
      ))->render();
      $email = PendingEmail::create(array(
          'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Document " . $document->title,
          'raw_body' => $body,
          'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
          'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
          'recipient' => $emailAddress,
          'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
          'attached_document_id' => $documentId,
      ));
      $email->send();
      return Redirect::to(URL::previous())->with('success_message', "Le document vous a été envoyé à l'adresse <strong>$emailAddress</strong>.");
    } else {
      return Redirect::to(URL::previous())->with('error_message', "Désolés, l'adresse <strong>$emailAddress</strong> ne fait pas partie de notre listing.")->withInput();
    }
  }
  
  public function submitDocument($section_slug) {
    
    $docId = Input::get('doc_id');
    $title = Input::get('doc_title');
    $description = Input::get('description');
    $category = Input::get('category');
    $public = Input::get('public') ? true : false;
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
          $document->category = $category;
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
                'category' => $category,
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
