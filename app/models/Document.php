<?php

class Document extends Eloquent {
  
  protected $fillable = array('title', 'description', 'doc_date', 'section_id');
  
  protected static $FOLDER_PATH = "../app/storage/site_data/documents/";
  
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
    return self::$FOLDER_PATH;
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
  
}