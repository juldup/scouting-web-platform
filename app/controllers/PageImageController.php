<?php

class PageImageController extends BaseController {
  
  public function getImage($image_id) {
    $image = PageImage::find($image_id);
    if ($image) {
      $path = $image->getPath();
      return Illuminate\Http\Response::create(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
      ));
    }
  }
  
  public function uploadImage($page_id) {
    
    $file = Input::file('Filedata');
    
    if (!$file->getSize()) {
      return json_encode(array(
          "result" => "KO",
          "message" => "Le fichier est trop gros et n'a pas pu être ajouté.'"
      ));
    }
    
    $image = PageImage::create(array(
        'page_id' => $page_id,
        'original_name' => $file->getClientOriginalName(),
    ));
    $file->move($image->getPathFolder(), $image->getPathFilename());
    
    return json_encode(array(
        "result" => "OK",
        "image_id" => $image->id,
        "url" => $image->getURL(),
    ));
  }
  
  public function removeImage($image_id) {
    $image = PageImage::find($image_id);
    if (!$image) {
      throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Image does not exist");
    }
    if (file_exists($image->getPath())) {
      unlink($image->getPath());
    }
    $image->delete();
    return json_encode(array(
        "result" => "OK",
        "image_id" => $image_id,
    ));
  }
  
}
