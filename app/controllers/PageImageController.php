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
    try {
      // Get file input data
      $file = Input::file('upload');
      // Make sure the file has been uploaded
      if (!$file->getSize()) {
        return View::make('pages.customPage.uploadImageCKEditor', [
            'funcNum' => Request::get('CKEditorFuncNum'),
            'imageURL' => null,
            'error' => "Le fichier est trop gros et n'a pas pu être ajouté.",
        ]);
      }
      // Create the image object in the database
      $image = PageImage::create(array(
          'page_id' => $page_id,
          'original_name' => $file->getClientOriginalName(),
      ));
      // Save the image in the filesystem
      $file->move($image->getPathFolder(), $image->getPathFilename());

      // Log
      LogEntry::log("Page", "Ajout d'une image à la librairie d'images d'une page", array("Image" => $image->original_name, "Page" => $page_id));
      // Return the response
      return View::make('pages.customPage.uploadImageCKEditor', [
          'funcNum' => Request::get('CKEditorFuncNum'),
          'imageURL' => $image->getURL(),
          'error' => null,
      ]);
    } catch (Exception $e) {
      return View::make('pages.customPage.uploadImageCKEditor', [
          'funcNum' => Request::get('CKEditorFuncNum'),
          'imageURL' => null,
          'error' => "Une erreur est survenue.",
      ]);
    }
  }
  
  /**
   * [Route] Deletes an image from a page's library
   * 
   * Note: this method is no longer used, but could be useful later if a
   * tool for deleting unused images is created.
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
    // Log
    LogEntry::log("Page", "Suppression d'une image de la librairie d'images d'une page", array("Image" => $image->original_name, "Page" => $image->page_id));
    // Return response
    return json_encode(array(
        "result" => "OK",
        "image_id" => $image_id,
    ));
  }
  
}
