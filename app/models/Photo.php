<?php

class Photo extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $PHOTO_FOLDER_PATH = "site_data/photos/";
  public static $FORMAT_THUMBNAIL = "thumbnail";
  public static $FORMAT_PREVIEW = "preview";
  public static $FORMAT_ORIGINAL = "original";
  
  public static $THUMBNAIL_WIDTH = 150;
  public static $THUMBNAIL_HEIGHT = 100;
  public static $PREVIEW_WIDTH = 600;
  public static $PREVIEW_HEIGHT = 400;
  
  public function getPhotoURL($format) {
    return URL::route('get_photo', array('format' => $format, 'photo_id' => $this->id, 'filename' => $this->filename));
  }
  
  public function getThumbnailURL() {
    return $this->getPhotoURL(self::$FORMAT_THUMBNAIL);
  }
  
  public function getPreviewURL() {
    return $this->getPhotoURL(self::$FORMAT_PREVIEW);
  }
  
  public function getOriginalURL() {
    return $this->getPhotoURL(self::$FORMAT_ORIGINAL);
  }
  
  public function getPhotoPath($format) {
    return $this->getPhotoPathFolder($format) . $this->getPhotoPathFilename();
  }
  
  public function getPhotoPathFolder($format) {
    return storage_path(self::$PHOTO_FOLDER_PATH . $format . "/");
  }
  
  public function getPhotoPathFilename() {
    return $this->id . ".photo";
  }
  
  // Create thumbnail picture
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
  
  // Create preview picture
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
  
  // Rotates the photo 90° clockwise or counterclockwise
  public function rotate($clockwise) {
    $degrees = $clockwise ? 270 : 90;
    $filename = $this->getPhotoPath("original");
    $isJpeg = true;
    try {
      $source = imagecreatefromjpeg($filename);
    } catch (ErrorException $e) {
      // Not jpeg ? Try png
      $isJpeg = false;
      $source = imagecreatefrompng($filename);
      // If not png, let the error run, the image cannot be rotated
    }
    $rotate = imagerotate($source, $degrees, 0);
    if ($isJpeg) {
      imagejpeg($rotate, $filename);
    } else {
      imagepng($rotate, $filename);
    }
    imagedestroy($source);
    imagedestroy($rotate);
    $this->createPreviewPicture();
    $this->createThumbnailPicture();
  }
  
}