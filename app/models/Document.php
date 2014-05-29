<?php

/**
 * This Eloquent class represents a document that can be
 * downloaded from the download page
 * 
 * Columns:
 *   - doc_date:    The date this document was created (used for automatic archiving)
 *   - section_id:  The section this document belongs to
 *   - title:       The name of the document
 *   - description: A short description of the document
 *   - category:    The category of the document
 *   - filename:    The name of the file this document is saved in
 *   - public:      Whether the document is public or private
 *   - archived:    Whether the document has been archived
 */
class Document extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Folder within in the storage folder where the documents are saved
  protected static $FOLDER_PATH = "site_data/documents/";
  
  /**
   * Returns the section of this document
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  /**
   * Returns the download URL of this document
   */
  public function getURL() {
    return URL::route('download_document', array('document_id' => $this->id));
  }
  
  /**
   * Returns the path of the file containing this document
   */
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  /**
   * Returns the path of the folder containing this document's file
   */
  public function getPathFolder() {
    return storage_path(self::$FOLDER_PATH);
  }
  
  /**
   * Returns the filename of this document
   */
  public function getPathFilename() {
    return $this->id . ".document";
  }
  
  /**
   * Updates the filename field, keeping the extension if no extension is provided
   * (the document does not get saved and must be saved after calling this function)
   * 
   * @param string $filename  The new filename (might contain an extension)
   * @param string $alternativeFilename  The previous filename (should contain an extension)
   */
  public function setFilename($filename, $alternativeFilename) {
    // Extension given in the filename
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    // Get extension if the filename contains no extension
    if (!$ext) $ext = pathinfo($alternativeFilename, PATHINFO_EXTENSION);
    if (!$ext) $ext = pathinfo($this->filename, PATHINFO_EXTENSION);
    // Get base name
    $base = pathinfo($filename, PATHINFO_FILENAME);
    // Get a default basename if the filename contains no basename
    if (!$base) $base = pathinfo($alternativeFilename, PATHINFO_FILENAME);
    if (!$base) $base = pathinfo($this->filename, PATHINFO_FILENAME);
    if (!$base) $base = "document";
    // Generate and update filename
    $extension = ($ext ? ".$ext" : "");
    $this->filename = $base . $extension;
  }
  
  /**
   * Returns whether the document can be deleted (i.e. if it is less than 7 days old)
   */
  public function canBeDeleted() {
    return time() - strtotime($this->created_at) < 7 * 24 * 3600;
  }
  
}
