<?php

/**
 * This Eloquent class represents a file attached to a section e-mail
 * 
 * Columns:
 *   - email_id: The e-mail this file is attached to
 *   - filename: The name of the file stored in the file system
 */
class EmailAttachment extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Folder (relative to storage folder) in which the e-mail attachment are
  // stored in the file system
  protected static $FOLDER_PATH = "site_data/email_attachments/";
  
  /**
   * Creates a new instance of EmailAttachment for the given file
   * 
   * @param Symfony\Component\HttpFoundation\File\UploadedFile $file  The uploaded file to transform into an attachment
   */
  public static function newFromFile($file) {
    // Check that file is not empty
    if (!$file->getSize()) {
      throw new Exception("La pièce jointe n'a pas été chargée");
    }
    // Create attachment
    $attachment = self::create(array(
        'filename' => $file->getClientOriginalName(),
    ));
    // Save file
    $file->move($attachment->getPathFolder(), $attachment->getPathFilename());
    return $attachment;
  }
  
  /**
   * Returns the download URL for this file
   */
  public function getURL() {
    return URL::route('get_email_attachment', array('attachment_id' => $this->id, 'filename' => $this->filename));
  }
  
  /**
   * Returns the path of the file in the file system
   */
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  /**
   * Returns the path of the folder this file is stored in
   */
  public function getPathFolder() {
    return storage_path(self::$FOLDER_PATH);
  }
  
  /**
   * Returns the filename of this file in the file system
   */
  public function getPathFilename() {
    return $this->id . ".attachment";
  }
  
}
