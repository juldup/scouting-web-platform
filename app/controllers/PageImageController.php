<?php

/**
 * This controller provides routes to upload content-edited page images
 * and to output these image for the visitors.
 */
class PageImageController extends BaseController {
  
  /**
   * [Route] Outputs the given image
   */
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
  
  /**
   * [Route] Adds an image to the image library of a page
   */
  public function uploadImage($page_id) {
    // Get file input data
    $file = Input::file('Filedata');
    // Make sure the file has been uploaded
    if (!$file->getSize()) {
      return json_encode(array(
          "result" => "KO",
          "message" => "Le fichier est trop gros et n'a pas pu être ajouté.'"
      ));
    }
    // Create the image object in the database
    $image = PageImage::create(array(
        'page_id' => $page_id,
        'original_name' => $file->getClientOriginalName(),
    ));
    // Save the image in the filesystem
    $file->move($image->getPathFolder(), $image->getPathFilename());
    // Return the response
    return json_encode(array(
        "result" => "OK",
        "image_id" => $image->id,
        "url" => $image->getURL(),
    ));
  }
  
  /**
   * [Route] Deletes an image from a page's library
   */
  public function removeImage($image_id) {
    // Get the image to delete
    $image = PageImage::find($image_id);
    if (!$image) {
      App::abort(404, "Image does not exist");
    }
    // Remove the image from the filesystem
    if (file_exists($image->getPath())) {
      unlink($image->getPath());
    }
    // Delete the image object from the database
    $image->delete();
    // Return response
    return json_encode(array(
        "result" => "OK",
        "image_id" => $image_id,
    ));
  }
  
}
