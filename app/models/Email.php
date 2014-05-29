<?php

/**
 * This Eloquent class represents an e-mail sent to a section and displayed
 * in the e-mail page of the website
 * 
 * Columns
 *   - section_id:     The section this e-mail belongs to
 *   - date:           The date this e-mail was sent
 *   - time:           The time at which this e-mail was sent
 *   - subject:        The subject of the e-mail
 *   - body_html:      The body (in html) of the e-mail
 *   - recipient_list: The comma-separated list of e-mail addresses this e-mail was sent to
 *   - sender_name:    The name of the sender
 *   - sender_email:   The e-mail address of the sender
 *   - archived:       Whether this e-mail has been archived
 *   - deleted:        Whether this e-mail has been deleted and should no longer be displayed
 */
class Email extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // The list of attachments of this e-mail (stored here for re-use)
  protected $cachedAttachments = null;
  
  /**
   * Returns whether this e-mail can be deleted (i.e. is less than 7 days old)
   */
  public function canBeDeleted() {
    $oneWeekAgo = time() - 7 * 24 * 3600;
    return strtotime($this->created_at) >= $oneWeekAgo;
  }
  
  /**
   * Returns the list of files attached (EmailAttachment instances) to this e-mail
   */
  public function getAttachments() {
    if (!$this->cachedAttachments) {
      $this->cachedAttachments = EmailAttachment::where('email_id', '=', $this->id)->get();
    }
    return $this->cachedAttachments;
  }
  
  /**
   * Returns whether this e-mail has at least one attachment
   */
  public function hasAttachments() {
    return count($this->getAttachments()) != 0;
  }
  
  /**
   * Returns the section this e-mail belongs to
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
}
