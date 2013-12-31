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
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
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
  
  public function sendByEmail() {
    $email = Input::get('email');
    $document_id = Input::get('document_id');
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
    
    $docId = Input::get('document_id');
    $title = Input::get('title');
    $description = Input::get('description');
    $file = Input::file('document_file');
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $this->section)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    $success = false;
    if (!$title) {
      $success = false;
      $message = "Tu dois entrer un titre.";
    } else {
      if ($docId) {
        $document = Document::find($docId);
        if ($document) {
          $document->title = $title;
          $document->description = $description;
          // TODO $document->file = $file;
          try {
            $news->save();
            $success = true;
            $message = "La nouvelle a été mise à jour.";
            $section_slug = $news->getSection()->slug;
          } catch (Illuminate\Database\QueryException $e) {
            $success = false;
            $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
          }
        } else {
          $success = false;
          $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
        }
      } else {
        if (!$file) {
          $success = false;
          $message = "Tu n'as pas joint de document.";
        } else {
          try {
            $news = News::create(array(
                'news_date' => date('Y-m-d'),
                'title' => $title,
                'content' => $content,
                'section_id' => $sectionId,
            ));
            $section_slug = $news->getSection()->slug;
            $success = true;
            $message = "La nouvelle a été créée.";
          } catch (Illuminate\Database\QueryException $e) {
            $success = false;
            throw $e;
            $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
          }
        }
      }
    }
    
    $response = Redirect::route('manage_news', array(
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) return $response;
    else return $response->withInput();
  }
  
  public function deleteNews($news_id) {
    
    $news = News::find($news_id);
    
    if (!$news) {
      throw new NotFoundHttpException("Cette nouvelle n'existe pas");
    }
    
    if (!$this->user->can(Privilege::$EDIT_NEWS, $news->section_id)) {
      return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
    }
    
    try {
      $news->delete();
      $success = true;
      $message = "La nouvelle a été supprimée.";
    } catch (Illuminate\Database\QueryException $e) {
      $success = false;
      $message = "Une erreur s'est produite. La nouvelle n'a pas été enregistrée.";
    }
    
    return Redirect::route('manage_news', array(
        "section_slug" => $news->getSection()->slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
