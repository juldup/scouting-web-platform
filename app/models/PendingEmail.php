<?php

class PendingEmail extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public static $ACCOUNT_EMAIL_PRIORITY = 1;
  public static $PERSONAL_SENDER_PRIORITY = 2;
  public static $PERSONAL_EMAIL_PRIORITY = 5;
  public static $SECTION_EMAIL_PRIORITY = 10;
  public static $SECTION_SENDER_PRIORITY = 12;
  public static $MAX_PRIORITY = 20;
  
  public function send() {
    $message = unserialize($this->email_object);
    try {
      $result = ScoutMailer::send($message);
    } catch (Exception $ex) {
      $result = false;
    }
    if ($result) {
      $this->sent = true;
    } else {
      $this->priority = $this->priority + 1;
    }
    $this->save();
  }
  
}