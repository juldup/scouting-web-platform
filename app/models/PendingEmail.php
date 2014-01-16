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
    $message = Swift_Message::newInstance();
    $message->setSubject($this->subject);
    $message->setFrom($this->sender_email, $this->sender_name ? $this->sender_name : null);
    $message->setTo($this->recipient);
    if ($this->section_email_id) {
      $email = Email::find($this->section_email_id);
      $attachments = EmailAttachment::where('email_id', '=', $email->id)->get();
      foreach ($attachments as $attachment) {
        $message->attach(Swift_Attachment::newInstance(file_get_contents($attachment->getPath()), $attachment->filename));
      }
      $message->setBody($email->body_html, 'text/html', 'utf-8');
    } else {
      $message->setBody($this->raw_body);
    }
    
    try {
      $result = ScoutMailer::send($message);
    } catch (Exception $ex) {
      echo $ex;
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