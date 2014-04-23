<?php

class Email extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected $cachedAttachments = null;
  
  public function canBeDeleted() {
    $oneWeekAgo = time() - 7 * 24 * 3600;
    return strtotime($this->created_at) >= $oneWeekAgo;
  }
  
  public function getAttachments() {
    if (!$this->cachedAttachments) {
      $this->cachedAttachments = EmailAttachment::where('email_id', '=', $this->id)->get();
    }
    return $this->cachedAttachments;
  }
  
  public function hasAttachments() {
    return count($this->getAttachments()) != 0;
  }
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
}