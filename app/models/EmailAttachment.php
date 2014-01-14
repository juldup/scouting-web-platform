<?php

class EmailAttachment extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $FOLDER_PATH = "../app/storage/site_data/email_attachments/";
  
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
  
  public function getURL() {
    return URL::route('get_email_attachment', array('attachment_id' => $this->id, 'filename' => $this->filename));
  }
  
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  public function getPathFolder() {
    return self::$FOLDER_PATH;
  }
  
  public function getPathFilename() {
    return $this->id . ".attachment";
  }
  
}