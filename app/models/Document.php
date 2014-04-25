<?php

class Document extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $FOLDER_PATH = "site_data/documents/";
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getURL() {
    return URL::route('download_document', array('document_id' => $this->id));
  }
  
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  public function getPathFolder() {
    return storage_path(self::$FOLDER_PATH);
  }
  
  public function getPathFilename() {
    return $this->id . ".document";
  }
  
  public function setFilename($filename, $alternativeFilename) {
    // Extension given in the filename
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!$ext) $ext = pathinfo($alternativeFilename, PATHINFO_EXTENSION);
    if (!$ext) $ext = pathinfo($this->filename, PATHINFO_EXTENSION);
    $base = pathinfo($filename, PATHINFO_FILENAME);
    if (!$base) $base = pathinfo($alternativeFilename, PATHINFO_FILENAME);
    if (!$base) $base = pathinfo($this->filename, PATHINFO_FILENAME);
    if (!$base) $base = "document";
    $extension = ($ext ? ".$ext" : "");
    $this->filename = $base . $extension;
  }
  
  public function canBeDeleted() {
    return time() - strtotime($this->created_at) < 7 * 24 * 3600;
  }
  
}