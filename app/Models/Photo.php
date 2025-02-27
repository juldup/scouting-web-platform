<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use App\Helpers\Resizer;

/**
 * This Eloquent class represents a photo in the photo page. Each photo
 * belongs to a photo album (PhotoAlbum).
 * 
 * Columns:
 *   - album_id: The album this photo belongs to
 *   - filename: The original filename of the photo
 *   - caption:  A short text that accompanies the photo
 *   - position: The order of the photo within the album
 */
class Photo extends Model {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Path of the folder (within the storage folder) that contains all the photos
  protected static $PHOTO_FOLDER_PATH = "app/site_data/photos/";
  
  // Photo formats
  public static $FORMAT_THUMBNAIL = "thumbnail";
  public static $FORMAT_PREVIEW = "preview";
  public static $FORMAT_ORIGINAL = "original";
  
  // Format sizes
  public static $THUMBNAIL_WIDTH = 150;
  public static $THUMBNAIL_HEIGHT = 100;
  public static $PREVIEW_WIDTH = 1200;
  public static $PREVIEW_HEIGHT = 800;
  
  /**
   * Returns the URL that accesses the photo in a given format
   */
  public function getPhotoURL($format) {
    return URL::route('get_photo', array('format' => $format, 'photo_id' => $this->id, 'filename' => $this->filename));
  }
  
  /**
   * Returns the URL that accesses the photo's thumbnail
   */
  public function getThumbnailURL() {
    return $this->getPhotoURL(self::$FORMAT_THUMBNAIL);
  }
  
  /**
   * Returns the URL that accesses the photo's preview
   */
  public function getPreviewURL() {
    return $this->getPhotoURL(self::$FORMAT_PREVIEW);
  }
  
  /**
   * Returns the URL that accesses the original photo
   */
  public function getOriginalURL() {
    return $this->getPhotoURL(self::$FORMAT_ORIGINAL);
  }
  
  /**
   * Returns the path of the photo file for the given format
   */
  public function getPhotoPath($format) {
    return $this->getPhotoPathFolder($format) . $this->getPhotoPathFilename();
  }
  
  /**
   * Returns the path of the folder containing the photo file in the given format
   */
  public function getPhotoPathFolder($format) {
    return storage_path(self::$PHOTO_FOLDER_PATH . $format . "/");
  }
  
  /**
   * Returns the filename of the stored photo (for any format)
   */
  public function getPhotoPathFilename() {
    return $this->id . ".photo";
  }
  
  /**
   * Creates and saves the thumbnail picture
   */
  public function createThumbnailPicture() {
    $thumbnail = new Resizer($this->getPhotoPath(Photo::$FORMAT_ORIGINAL));
    $thumbnail->resizeImage(Photo::$THUMBNAIL_WIDTH, Photo::$THUMBNAIL_HEIGHT, "crop");
    // Create directory
    $folder = $this->getPhotoPathFolder(Photo::$FORMAT_THUMBNAIL);
    if (!file_exists($folder)) {
      mkdir($folder, 0777, true);
    }
    // Save image
    $thumbnail->saveImage($this->getPhotoPath(Photo::$FORMAT_THUMBNAIL));
  }
  
  /**
   * Creates and saves the preview picture
   */
  public function createPreviewPicture() {
    $preview = new Resizer($this->getPhotoPath(Photo::$FORMAT_ORIGINAL));
    $preview->resizeImage(Photo::$PREVIEW_WIDTH, Photo::$PREVIEW_HEIGHT, "portrait");
    // Create directory
    $folder = $this->getPhotoPathFolder(Photo::$FORMAT_PREVIEW);
    if (!file_exists($folder)) {
      mkdir($folder, 0777, true);
    }
    // Save image
    $preview->saveImage($this->getPhotoPath(Photo::$FORMAT_PREVIEW));
  }
  
  /**
   * Rotates the photo 90° clockwise or counterclockwise
   * 
   * @param type $clockwise  True = clockwise, false = counterclockwise
   */
  public function rotate($clockwise) {
    // Convert direction in degrees
    $degrees = $clockwise ? 270 : 90;
    // Get original path
    $filename = $this->getPhotoPath("original");
    try {
      // Try creating the image from jpeg
      $isJpeg = true;
      $source = imagecreatefromjpeg($filename);
    } catch (ErrorException $e) {
      // Not jpeg ? Try png
      $isJpeg = false;
      $source = imagecreatefrompng($filename);
      // If not png, let the error run, the image cannot be rotated
    }
    // Rotate the image
    $rotate = imagerotate($source, $degrees, 0);
    // Save the new image
    if ($isJpeg) {
      imagejpeg($rotate, $filename);
    } else {
      imagepng($rotate, $filename);
    }
    // Destroy temporary images
    imagedestroy($source);
    imagedestroy($rotate);
    // Create preview and thumbnail
    $this->createPreviewPicture();
    $this->createThumbnailPicture();
  }
  
  /**
   * Returns the list of comments on this photo
   */
  public function getComments() {
    if (!$this->comments) {
      $this->comments = Comment::listFor("photo", $this->id);
    }
    return $this->comments;
  }
  
}
