<?php

class Photo extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $PHOTO_FOLDER_PATH = "../app/storage/site_data/photos/";
  public static $FORMAT_THUMBNAIL = "thumbnail";
  public static $FORMAT_PREVIEW = "preview";
  public static $FORMAT_ORIGINAL = "original";
  
  public function getPhotoURL($format) {
    return URL::route('get_photo', array('format' => $format, 'photo_id' => $this->id));
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
    return self::$PHOTO_FOLDER_PATH . $format . "/";
  }
  
  public function getPhotoPathFilename() {
    return $this->id . ".photo";
  }
  
}