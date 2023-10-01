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
  public function uploadImage() {
    if (!$this->user->isLeader()) return;
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
          'original_name' => $file->getClientOriginalName(),
      ));
      // Save the image in the filesystem
      $file->move($image->getPathFolder(), $image->getPathFilename());

      // Log
      LogEntry::log("Page", "Ajout d'une image à la librairie d'images", array("Image" => $image->original_name));
      // Return the response
      return View::make('pages.customPage.uploadImageCKEditor', [
          'funcNum' => Request::get('CKEditorFuncNum'),
          'imageURL' => $image->getURL(),
          'error' => null,
      ]);
    } catch (Exception $e) {
      Log::error($e);
      return View::make('pages.customPage.uploadImageCKEditor', [
          'funcNum' => Request::get('CKEditorFuncNum'),
          'imageURL' => null,
          'error' => "Une erreur est survenue.",
      ]);
    }
  }
  
}
