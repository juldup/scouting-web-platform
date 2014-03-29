<?php

class PhotoController extends BaseController {
  
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_PHOTOS)) {
      return App::abort(404);
    }
    
    if (!$this->user->isMember()) {
      return Helper::forbiddenNotMemberResponse();
    }
    
    $albumId = Route::input('album_id');
    
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->where('photo_count', '!=', 0)
            ->orderBy('position')
            ->get();
    
    $currentAlbum = null;
    $photos = null;
    if ($albumId) {
      $currentAlbum = PhotoAlbum::where('id', '=', $albumId)
              ->where('archived', '=', false)
              ->where('section_id', '=', $this->section->id)
              ->where('photo_count', '!=', 0)
              ->first();
      if (!$currentAlbum) {
        return Redirect::route('photos', array('section_slug' => $this->section->slug));
      }
    }
    if (count($albums)) {
      if (!$currentAlbum) $currentAlbum = $albums[0];
      $photos = Photo::where('album_id', '=', $currentAlbum->id)
              ->orderBy('position')
              ->get();
    }
    
    return View::make('pages.photos.photos', array(
        'albums' => $albums,
        'current_album' => $currentAlbum,
        'photos' => $photos,
        'can_manage' => $this->user->can(Privilege::$POST_PHOTOS, $this->section),
    ));
  }
  
  public function getPhoto($format, $photo_id) {
    if (!$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    $photo = Photo::find($photo_id);
    if (!$photo) App::abort(404, "La photo n'existe plus.");
    $path = $photo->getPhotoPath($format);
    if (file_exists($path)) {
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    } else {
      throw App::abort(404, "La photo n'existe plus.");
    }
  }
  
  public function downloadAlbum($album_id) {
    // Check that the user is allowed to download photos
    if (!$this->user->isMember()) {
      return Helper::forbiddenResponse();
    }
    // Gather photos
    $photos = Photo::where('album_id', '=', $album_id)
              ->orderBy('position')
              ->get();
    if (!count($photos)) {
      return App::abort(404, "Cet album est vide.");
    }
    // Create zip file in temporary folder
    $filename = tempnam(sys_get_temp_dir(), "photos.zip.");
    $zip = new ZipArchive();
    $zip->open($filename);
    // Add each photo in the zip file
    foreach ($photos as $photo) {
      $zip->addFile($photo->getPhotoPath(Photo::$FORMAT_ORIGINAL), $photo->filename);
    }
    $zip->close();
    if (file_exists($filename)) {
      // Output file
      $response = Illuminate\Http\Response::create(file_get_contents($filename), 200, array(
          "Content-Type" => "application/octet-stream",
          "Content-Length" => filesize($filename),
          "Content-Disposition" => 'attachment; filename="photos.zip"',
      ));
      unlink($filename);
      return $response;
    } else {
      throw App::abort(404, "La photo n'existe plus.");
    }
  }
  
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_PHOTOS)) {
      return App::abort(404);
    }
    
    if (!$this->user->can(Privilege::$POST_PHOTOS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('position')
            ->get();
    
    $selectedAlbumId = Session::get('album_id', null);
    
    return View::make('pages.photos.editPhotos', array(
        'albums' => $albums,
        'selected_album_id' => $selectedAlbumId,
    ));
    
  }
  
  public function createPhotoAlbum() {
    if (!$this->user->can(Privilege::$POST_PHOTOS, $this->section)) {
      return Helper::forbiddenResponse();
    }
    try {
      $album = PhotoAlbum::create(array(
          'section_id' => $this->section->id,
          'name' => "Album du " . Helper::dateToHuman(date('Y-m-d')),
      ));
      $album->position = $album->id;
      $album->save();
    } catch (Exception $ex) {
      if ($album) $album.delete();
      return Redirect::route('edit_photos')
              ->with('error_message', "Une erreur est survenue. L'album n'a pas pu être créé.");
    }
    return Redirect::route('edit_photos')
            ->with('album_id', $album->id);
  }
  
  public function changeAlbumOrder() {
    
    $errorResponse = json_encode(array("result" => "Failure"));
    
    $albumIdsInOrder = Input::get('album_order');
    $albumIdsInOrderArray = explode(" ", $albumIdsInOrder);
    
    // Retrieve albums
    $albums = PhotoAlbum::where('archived', '=', false)
            ->where(function($query) use ($albumIdsInOrderArray) {
              foreach ($albumIdsInOrderArray as $albumId) {
                $query->orWhere('id', '=', $albumId);
              }
            })->get();
    // Check that the number of albums corresponds
    if (count($albumIdsInOrderArray) != count($albums)) {
      return $errorResponse;
    }
    // Check that all albums belong to the same section
    $sectionId = 0;
    foreach ($albums as $album) {
      if (!$sectionId) $sectionId = $album->section_id;
      if ($sectionId != $album->section_id) {
        return $errorResponse;
      }
    }
    if (!$sectionId) return $errorResponse;
    // Check that the user has the right to modify this section's albums
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Get the list of positions
    $positions = array();
    foreach ($albums as $album) {
      $positions[] = $album->position;
    }
    sort($positions);
    // Assign new positions
    foreach ($albums as $album) {
      // Get new order of this album
      $index = array_search($album->id, $albumIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $album->position = $positions[$index];
    }
    // Save all albums
    foreach ($albums as $album) {
      try {
        $album->save();
      } catch (Exception $ex) {
        return $errorResponse;
      }
    }
    
    return json_encode(array('result' => "Success"));
  }
  
  public function deletePhotoAlbum($album_id) {
    $album = PhotoAlbum::find($album_id);
    if (!$album) App::abort(404, "Cet album n'existe pas.");
    $sectionId = $album->section_id;
    if (!$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    if ($album->photo_count != 0) {
      return Redirect::route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('error_message', "Cet album n'est pas vide et ne peut pas être supprimé.");
    }
    try {
      $album->delete();
    } catch (Exception $ex) {
      return Redirect::route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('error_message', "Une erreur est survenue. L'album n'as pas été supprimé.");
    }
    return Redirect::route('edit_photos', array('section_slug', Section::find($sectionId)->slug))
              ->with('success_message', "L'album a été supprimé.");
  }
  
  public function changeAlbumName() {
    $errorResponse = json_encode(array("result" => "Failure"));
    $albumId = Input::get('id');
    $newName = Input::get('value');
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    if (!$sectionId || !$newName || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    try {
      $album->name = $newName;
      $album->save();
    } catch (Exception $ex) {
      return $errorResponse;
    }
    return json_encode(array('result' => "Success"));
  }
  
  public function showEditAlbum($album_id) {
    $album = PhotoAlbum::find($album_id);
    if (!$album) App::abort(404, "Cet album n'existe pas.");
    if (!$this->user->can(Privilege::$POST_PHOTOS, $album->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    // If the section does not correspond (i.e. a new tab has been selected), redirect to edit photos page
    if ($album->section_id != $this->section->id) {
      return Redirect::route('edit_photos');
    }
    
    $photos = Photo::where('album_id', '=', $album->id)
              ->orderBy('position')
              ->get();
    
    return View::make('pages.photos.editAlbum', array(
        'album' => $album,
        'photos' => $photos,
    ));
  }
  
  public function changePhotoOrder() {
    $errorResponse = json_encode(array("result" => "Failure"));
    $photoIdsInOrder = Input::get('photo_order');
    $photoIdsInOrderArray = explode(" ", $photoIdsInOrder);
    // Retrieve photos
    $photos = Photo::where(function($query) use ($photoIdsInOrderArray) {
              foreach ($photoIdsInOrderArray as $photoId) {
                $query->orWhere('id', '=', $photoId);
              }
            })->get();
    // Check that the number of photos corresponds
    if (count($photoIdsInOrderArray) != count($photos)) {
      return $errorResponse;
    }
    // Check that all albums belong to the same album
    $albumId = 0;
    foreach ($photos as $photo) {
      if (!$albumId) $albumId = $photo->album_id;
      if ($albumId != $photo->album_id) {
        return $errorResponse;
      }
    }
    if (!$albumId) return $errorResponse;
    // Check that the user has the right to modify this album
    $album = PhotoAlbum::find($albumId);
    if (!$album || !$this->user->can(Privilege::$POST_PHOTOS, $album->section_id)) {
      return $errorResponse;
    }
    // Get list of positions
    $positions = array();
    foreach ($photos as $photo) {
      $positions[] = $photo->position;
    }
    sort($positions);
    // Assign new positions
    foreach ($photos as $photo) {
      // Get new order of this album
      $index = array_search($photo->id, $photoIdsInOrderArray);
      if ($index === false) return $errorResponse;
      // Assign position
      $photo->position = $positions[$index];
    }
    // Save all photos
    foreach ($photos as $photo) {
      try {
        $photo->save();
      } catch (Exception $ex) {
        return $errorResponse;
      }
    }
    // Everything went well
    return json_encode(array('result' => "Success"));
  }
  
  public function deletePhoto() {
    $photoId = Input::get('photo_id');
    $photo = Photo::find($photoId);
    $album = $photo ? PhotoAlbum::find($photo->album_id) : null;
    $sectionId = $album ? $album->section_id : null;
    if ($sectionId && $this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      try {
        $photo->delete();
        // Update album photo count
        try {
          $album->updatePhotoCount();
        } catch (Exception $ex) {
          // Never mind
        }
        // Remove actual files
        unlink($photo->getPhotoPath(Photo::$FORMAT_ORIGINAL));
        unlink($photo->getPhotoPath(Photo::$FORMAT_PREVIEW));
        unlink($photo->getPhotoPath(Photo::$FORMAT_THUMBNAIL));
        // Return success response
        return json_encode(array('result' => "Success"));
      } catch (Exception $ex) {
        // Do nothing
      }
    }
    // If reaching here, the photo has not been deleted
    return json_encode(array('result' => "Failure"));
  }
  
  public function addPhoto() {
    // Get input data
    try {
    $file = Input::file('file');
    $uploadId = Input::get('id', 0);
    $albumId = Input::get('album_id');
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    // Prepare error response
    $errorResponse = json_encode(array("result" => "Failure", "id" => $uploadId));
    // Check if user is allowed to upload a photo
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    // Check that the image exists
    if ($file == null || !$file->getSize()) {
      return $errorResponse;
    }
    try {
      // Create photo
      $photo = Photo::create(array(
          'album_id' => $albumId,
          'filename' => $file->getClientOriginalName(),
      ));
      // Set position to last
      $photo->position = $photo->id;
      $photo->save();
      // Move file
      $file->move($photo->getPhotoPathFolder(Photo::$FORMAT_ORIGINAL), $photo->getPhotoPathFilename());
      // Create thumbnail and preview pictures
      $photo->createThumbnailPicture();
      $photo->createPreviewPicture();
    } catch (Exception $ex) {
      die ($ex);
      // Revert if possible
      try {
        if ($photo != null) $photo->delete();
      } catch (Exception $e) {
      }
      return $errorResponse;
    }
    // Update album photo count
    try {
      $album->updatePhotoCount();
    } catch (Exception $ex) {
      // Never mind
    }
    // Return success response
    return json_encode(array(
        "result" => "Success",
        "id" => $uploadId,
        "photo_id" => $photo->id,
        "photo_thumbnail_url" => $photo->getThumbnailURL(),
    ));
    } catch (Exception $e) {
      return json_encode(array("result" => "Failure", "message" => "$e"));
    }
    
  }
  
  public function changePhotoCaption() {
    $errorResponse = json_encode(array("result" => "Failure"));
    $photoId = Input::get('id');
    $newCaption = Input::get('value', "");
    $photo = Photo::find($photoId);
    $albumId = $photo ? $photo->album_id : null;
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    try {
      $photo->caption = $newCaption;
      $photo->save();
    } catch (Exception $ex) {
      return $errorResponse;
    }
    return json_encode(array('result' => "Success"));
  }
  
  public function rotatePhoto() {
    $errorResponse = json_encode(array("result" => "Failure"));
    $photoId = Input::get('photo_id');
    $clockwise = Input::get('clockwise') == "true" ? true : false;
    $photo = Photo::find($photoId);
    $albumId = $photo ? $photo->album_id : null;
    $album = PhotoAlbum::find($albumId);
    $sectionId = $album ? $album->section_id : null;
    if (!$sectionId || !$this->user->can(Privilege::$POST_PHOTOS, $sectionId)) {
      return $errorResponse;
    }
    try {
      $photo->rotate($clockwise);
    } catch (Exception $e) {
      return $errorResponse;
    }
    return json_encode(array("result" => "Success"));
  }
  
}
