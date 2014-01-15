<?php

class Email extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public function canBeDeleted() {
    $oneWeekAgo = time() - 7 * 24 * 3600;
    return strtotime($this->created_at) >= $oneWeekAgo;
  }
  
  public function deleteWithAttachments() {
    $this->delete();
    try {
      // TODO delete attachments
    } catch (Exception $ex) {
      
    }
  }
  
}