<?php

class PhotoController extends BaseController {
  
  public function showPage() {
    
    $albums = PhotoAlbum::where('archive', '=', '')
            ->where('section_id', '=', $this->section->id)
            ->where('photo_count', '!=', 0)
            ->orderBy('position')
            ->get();
    
    if (count($albums)) {
      $currentAlbum = $albums[0];
      $photos = Photo::where('album_id', '=', $currentAlbum->id)
              ->orderBy('position')
              ->get();
    } else {
      $currentAlbum = null;
      $photos = null;
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
    if (!$photo) throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException("La photo n'existe plus.");
    $path = $photo->getPhotoPath($format);
    if (file_exists($path)) {
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    } else {
      throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException("La photo n'existe plus.");
    }
  }
  
  public function downloadAlbum($album_id) {
    throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Il est pour le moment impossible de télécharger les albums de photos.");
  }
  
  public function showEdit() {
    
    $albums = PhotoAlbum::where('archive', '=', '')
            ->where('section_id', '=', $this->section->id)
            ->orderBy('position')
            ->get();
    
    return View::make('pages.photos.editPhotos', array(
        'albums' => $albums,
    ));
    
  }
  
  public function changeAlbumOrder() {
    
    $errorResponse = json_encode(array("result" => "Failure"));
    
    $albumIdsInOrder = Input::get('album_order');
    $albumIdsInOrderArray = explode(" ", $albumIdsInOrder);
    
    // Retrieve albums
    $albums = PhotoAlbum::where('archive', '=', '')
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
    // Get list of positions
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
  
  public function showEditAlbum($album_id) {
    $album = PhotoAlbum::find($album_id);
    if (!$album) throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Cet album n'existe pas.");
    if (!$this->user->can(Privilege::$POST_PHOTOS, $album->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    $photos = Photo::where('album_id', '=', $album->id)
              ->orderBy('position')
              ->get();
    
    return View::make('pages.photos.editAlbum', array(
        'album' => $album,
        'photos' => $photos,
    ));
  }
  
}