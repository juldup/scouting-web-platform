<?php

class ScoutMailer {
  
  protected static $mailer;
  
  public static function send(Swift_Message $message) {
    $mailer = self::getMailer();
    return $mailer->send($message);
  }
  
  protected static function getMailer() {
    if (!self::$mailer) {
      $transport = Swift_SmtpTransport::newInstance(
              Parameter::get(Parameter::$SMTP_HOST),
              Parameter::get(Parameter::$SMTP_PORT),
              Parameter::get(Parameter::$SMTP_SECURITY))
              ->setUsername(Parameter::get(Parameter::$SMTP_USERNAME))
              ->setPassword(Parameter::get(Parameter::$SMTP_PASSWORD));
      self::$mailer = Swift_Mailer::newInstance($transport);
    }
    return self::$mailer;
  }
  
  public static function sendPendingEmails($limit = 10) {
    // Current time
    $time = time();    
    // Delete all e-mails sent more than one hour ago
    $oneHourAgo = $time - 3600;
    DB::table('pending_emails')
            ->where('sent', '=', 1)
            ->where('last_retry', '<', $oneHourAgo)
            ->delete();
    // Select e-mails (with no too recent retry)
    $twoMinutesAgo = $time - 120;
    $emails = PendingEmail::where('sent', '=', 0)
            ->where('last_retry', '<', $twoMinutesAgo)
            ->where('priority', '<=', PendingEmail::$MAX_PRIORITY)
            ->orderBy('priority')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();
    // Try sending e-mails (if not locked by another process)
    foreach ($emails as $email) {
      // Lock with last_retry to avoid collision
      $count = DB::table('pending_emails')
              ->where('id', '=', $email->id)
              ->where('last_retry', '<', $twoMinutesAgo)
              ->update(array('last_retry' => $time));
      if ($count) {
        $email->send();
      }
    }
  }
  
}