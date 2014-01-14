<?php

class PendingEmail extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public static $ACCOUNT_EMAIL_PRIORITY = 1;
  public static $PERSONAL_SENDER_PRIORITY = 2;
  public static $PERSONAL_EMAIL_PRIORITY = 5;
  public static $SECTION_EMAIL_PRIORITY = 10;
  public static $SECTION_SENDER_PRIORITY = 12;
  
  public function send() {
    $message = unserialize($this->email_object);
    $result = ScoutMailer::send($message);
    if ($result) {
      $this->sent = true;
    } else {
      $this->priority = $this->priority + 1;
    }
    $this->save();
  }
  
}