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
  
}