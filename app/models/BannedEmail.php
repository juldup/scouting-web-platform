<?php

class BannedEmail extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  public static function getCodeForEmail($email) {
    $banned = self::where('email', '=', $email)->first();
    if (!$banned) {
      $banned = BannedEmail::create(array(
          'email' => $email,
          'ban_code' => self::generateVerificationCode($email),
          'banned' => false,
      ));
    }
    return $banned->ban_code;
  }
  
  public static function isBanned($email) {
    $banned = self::where('email', '=', $email)
          ->where('banned', '=', true)
          ->first();
    if ($banned) return true;
    return false;
  }
  
  private static function generateVerificationCode($email) {
    return hash('sha256', rand() . $email) . time(); // TODO Change to base64 for shorter validation link
  }
  
}