<?php

class PageImage extends Eloquent {
  
  protected static $FOLDER_PATH = "images/pages/";

  protected $fillable = array('page_id', 'original_name');
  
  public function getURL() {
    return URL::route('get_page_image', array('image_id' => $this->id));
  }
  
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  public function getPathFolder() {
    return self::$FOLDER_PATH;
  }
  
  public function getPathFilename() {
    return $this->id . ".image";
  }
  
}