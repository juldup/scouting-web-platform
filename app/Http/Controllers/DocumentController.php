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
 * Leaders can share documents with the parents and scouts. Documents are filed
 * by section, and sorted by category. They can be public (access for anybody) or
 * private (access for members only).
 * 
 * There are two pages: the download page and the management page. The latter is
 * only accessible to leaders.
 */
class DocumentController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows the document page
   * 
   * @param boolean $showArchives  Whether archived documents are being shown
   * @param integer $page  The archive page being shown (starts at 0)
   */
  public function showPage($section_slug = null, $showArchives = false, $page = 0) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_DOCUMENTS)) {
      return App::abort(404);
    }
    // Get documents
    if ($showArchives) {
      // Archived documents
      $pageSize = 30;
      $documents = Document::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('doc_date', '<', Helper::oneYearAgo());
              })
                      ->where('section_id', '=', $this->section->id)
                      ->orderBy('doc_date', 'desc')
                      ->skip($page * $pageSize)
                      ->take($pageSize)
                      ->get();
      // Determine whether there are more archives
      $hasArchives = Document::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('doc_date', '<', Helper::oneYearAgo());
              })
                      ->where('section_id', '=', $this->section->id)
                      ->count() > ($page + 1) * $pageSize;
      // Generate category list
      $documentsInCategories = array(
          "Documents archivés" => $documents,
      );
    } else {
      // Current documents
      $documents = Document::where('archived', '=', false)
              ->where('doc_date', '>=', Helper::oneYearAgo())
              ->where('section_id', '=', $this->section->id)->get();
      // Get archives
      $hasArchives = Document::where(function($query) {
                  $query->where('archived', '=', true);
                  $query->orWhere('doc_date', '<', Helper::oneYearAgo());
              })
                      ->where('section_id', '=', $this->section->id)
                      ->count();
      // Generate category list
      $documentsInCategories = $this->generateCategoryList($documents);
    }
    // Generate document selector
    $documentSelectList = array();
    foreach ($documents as $document) {
      $documentSelectList[$document->id] = $document->title;
    }
    // Make view
    return View::make('pages.documents.documents', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_DOCUMENTS, $this->section),
        'edit_url' => URL::route('manage_documents', array('section_slug' => $this->section->slug)),
        'documents' => $documentsInCategories,
        'documentSelectList' => $documentSelectList,
        'has_archives' => $hasArchives,
        'showing_archives' => $showArchives,
        'next_page' => $page + 1,
    ));
  }
  
  /**
   * [Route] Shows the archived documents page
   */
  public function showArchives($section_slug = null) {
    $page = Input::get('page');
    if (!$page) $page = 0;
    return $this->showPage($section_slug, true, $page);
  }
  
  /**
   * [Route] Shows the document management page
   */
  public function showEdit() {
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    // Get documents
    $documents = Document::where('archived', '=', false)
            ->where('doc_date', '>=', Helper::oneYearAgo())
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
  
  /**
   * Transforms the category name "Pour les scouts" to a category name
   * depending on the current section (e.g. "Pour les baladins")
   */
  private function updateCategoryName($category) {
    if ($category == "Pour les scouts") {
      $category = "Pour les " . $this->section->getScoutName();
    }
    return $category;
  }
  
  /**
   * Sorts the documents by category and returns an array
   * of {category name}=>{array of documents in this category}.
   * All documents that don't belong to a predefined catogory
   * are put in the "Divers" category.
   */
  private function generateCategoryList($documents) {
    // Create an array per category
    $documentsInCategories = array();
    $categories = explode(";", Parameter::get(Parameter::$DOCUMENT_CATEGORIES));
    foreach ($categories as $category) {
      $documentsInCategories[$this->updateCategoryName($category)] = array();
    }
    $documentsInCategories["Divers"] = array();
    // Put documents in categories
    foreach ($documents as $document) {
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
  
  /**
   * [Route] Outputs the selected document for download
   */
  public function downloadDocument($document_id) {
    // Get document
    $document = Document::find($document_id);
    if (!$document) App::abort("Ce document n'existe plus.");
    // Make sure the user has access to this document
    if (!$document->public && !$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    // Output document
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
      // File does not exist, redirect to previous page with error message
      return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe plus.");
    }
  }
  
  /**
   * [Route] Sends the selected document by e-mail to the desired recipient
   * if they are a member of the unit
   */
  public function sendByEmail() {
    // Get e-mail address and document id from input
    $emailAddress = strtolower(Input::get('email'));
    $documentId = Input::get('document_id');
    // Make sure the e-mail address is non-empty
    if ($emailAddress == "") {
      return Redirect::to(URL::previous())
              ->with('error_message', "Veuillez entrer une adresse e-mail pour recevoir le document.")
              ->withInput();
    }
    // Test if the given e-mail address belongs to a member
    if (Member::existWithEmail($emailAddress)) {
      // Send document by e-mail
      $document = Document::find($documentId);
      if (!$document) return Redirect::to(URL::previous())->with('error_message', "Ce document n'existe pas.")->withInput();
      $emailContent = Helper::renderEmail('sendDocument', $emailAddress, array(
          'document' => $document,
      ));
      $email = PendingEmail::create(array(
          'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Document " . $document->title,
          'raw_body' => $emailContent['txt'],
          'html_body' => $emailContent['html'],
          'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
          'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
          'recipient' => $emailAddress,
          'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
          'attached_document_id' => $documentId,
      ));
      $email->send();
      // Log
      LogEntry::log("Documents", "Envoi d'un document par e-mail", array('Document' => $document->title, 'Destinataire' => $emailAddress));
      // Redirect to previous page with success message
      return Redirect::to(URL::previous())->with('success_message', "Le document vous a été envoyé à l'adresse <strong>$emailAddress</strong>.");
    } else {
      // The e-mail address does not belong to a member, redirect to previous page with an error message
      return Redirect::to(URL::previous())
              ->with('error_message', "Désolés, l'adresse <strong>$emailAddress</strong> ne fait pas partie de notre listing.")
              ->withInput();
    }
  }
  
  /**
   * [Route] Used to submit a new or modified document by a leader
   */
  public function submitDocument($section_slug) {
    // Get input data
    $docId = Input::get('doc_id');
    $title = Input::get('doc_title');
    $description = Input::get('description');
    $category = Input::get('category');
    $public = Input::get('public') ? true : false;
    $file = Input::file('document');
    $filename = Input::get('filename');
    $actualFileName = ($file ? $file->getClientOriginalName() : null);
    // Make sure the user can edit documents
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    // Update or create document
    $success = false;
    if (!$title) {
      // The title is empty
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else {
      $docData = array(
          'title' => $title,
          'description' => $description,
          'category' => $category,
          'public' => $public,
      );
      if ($docId) {
        // The document already exists and is being updated
        $document = Document::find($docId);
        if ($document) {
          // The document has been found, check that the user can modify documents of this section
          if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $document->getSection())) {
            return Helper::forbiddenResponse();
          }
          // Update the document
          $document->update($docData);
          $document->setFileName($filename, $actualFileName);
          try {
            $document->save();
            $success = true;
            // Move file if a new file was uploaded
            if ($file != null) {
              $file->move($document->getPathFolder(), $document->getPathFilename());
            }
            $message = "Le document a été mis a jour.";
            $section_slug = $document->getSection()->slug;
          } catch (Exception $e) {
            Log::error($e);
            $success = false;
            $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
          }
        } else {
          // The document has not been found
          $success = false;
          $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
        }
      } else {
        // The document is a new one
        if ($file == null) {
          // No file has been uploaded
          $success = false;
          $message = "Tu n'as pas joint de document.";
        } else {
          // Create the document
          $document = null;
          try {
            // Create document
            $docDataFull = array_merge(array(
              'doc_date' => date('Y-m-d'),
              'filename' => 'document',
              'section_id' => $this->section->id,
            ), $docData);
            $document = Document::create($docDataFull);
            // Move file
            $file->move($document->getPathFolder(), $document->getPathFilename());
            // Save filename
            $document->setFilename($filename, $actualFileName);
            $document->save();
            $success = true;
            $message = "Le document a été créé.";
          } catch (Exception $e) {
            Log::error($e);
            $success = false;
            $message = "Une erreur s'est produite. Le document n'a pas été enregistré.";
            // Try deleting created document if it exists
            if ($document) {
              try {
                $document->delete();
              } catch (Exception $e) {
                Log::error($e);
              }
            }
          }
        }
      }
    }
    // Redirect with success or error message accordingly
    $response = Redirect::route('manage_documents', array(
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) {
      LogEntry::log("Documents", $docId ? "Modification d'un document" : "Ajout d'un document",
              array("Titre" => $title, "Description" => $description, "Categorie" => $category, "Public" => $public ? "Oui" : "Non"));
      return $response;
    } else {
      LogEntry::error("Documents", "Erreur lors de la sauvegarde d'un document"); // TODO improve log message
      return $response->withInput();
    }
  }
  
  /**
   * [Route] Deletes an existing document, if it not older than
   * one week
   */
  public function deleteDocument($document_id) {
    // Get document
    $document = Document::find($document_id);
    if (!$document) {
      App::abort(404, "Ce document n'existe pas.");
    }
    // Make sure the user can delete documents of this section
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $document->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Make sure the document can still be deleted
    if (!$document->canBeDeleted()) {
      // Document is too old and can no longer be deleted
      $success = false;
      $message = "Ce document est vieux de plus d'une semaine. Il ne peut pas être supprimé, mais peut être archivé.";
    } else {
      // Delete document
      try {
        unlink($document->getPath());
        $document->delete();
        $success = true;
        $message = "Le document a été supprimé.";
        LogEntry::log("Documents", "Suppression d'un document", array("Document" => $document->title));
      } catch (Exception $e) {
        Log::error($e);
        $success = false;
        $message = "Une erreur s'est produite. Le document n'a pas été supprimé.";
        LogEntry::error("Documents", "Erreur lors de la suppression d'un document", array("Erreur" => $e->getMessage()));
      }
    }
    // Redirect to previous page with success or error message
    return Redirect::route('manage_documents', array(
        "section_slug" => $document->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
  /**
   * [Route] Marks a document as archived
   */
  public function archiveDocument($section_slug, $document_id) {
    // Get the document
    $document = Document::find($document_id);
    if (!$document) {
      App::abort(404, "Ce document n'existe pas.");
    }
    // Make sure the user can archive documents of this section
    if (!$this->user->can(Privilege::$EDIT_DOCUMENTS, $document->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Archive document
    try {
      $document->archived = true;
      $document->save();
      $success = true;
      $message = "Le document a été archivé.";
      LogEntry::log("Documents", "Archivage d'un document", array("Document" => $document->title));
    } catch (Exception $e) {
      Log::error($e);
      $success = false;
      $message = "Une erreur s'est produite. Le document n'a pas été archivé.";
      LogEntry::error("Documents", "Erreur lors de l'archivage d'un document", array("Erreur" => $e->getMessage()));
    }
    // Redirect with status message
    return Redirect::route('manage_documents', array(
        "section_slug" => $document->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
