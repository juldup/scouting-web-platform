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
  
}