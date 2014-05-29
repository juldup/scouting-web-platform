<?php

/**
 * This eloquent class represents an entry associated with an e-mail address,
 * with a field telling whether the e-mail address is banned and no mail should be
 * sent to it.
 * 
 * Columns:
 *   - email:    The e-mail address
 *   - ban_code: A code sent along the e-mails in a link to ban the e-mail address
 *   - banned:   Whether this e-mail address is banned
 */
class BannedEmail extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Returns the ban code associated with an e-mail address, creating
   * a new entry if none exists
   */
  public static function getCodeForEmail($email) {
    $banned = self::where('email', '=', $email)->first();
    if (!$banned) {
      $banned = BannedEmail::create(array(
          'email' => $email,
          'ban_code' => self::generateBanCode($email),
          'banned' => false,
      ));
    }
    return $banned->ban_code;
  }
  
  /**
   * Returns whether the given e-mail address is banned
   */
  public static function isBanned($email) {
    $banned = self::where('email', '=', $email)
          ->where('banned', '=', true)
          ->first();
    if ($banned) return true;
    return false;
  }
  
  /**
   * Generates a new ban code
   */
  private static function generateBanCode($email) {
    return hash('sha256', rand() . $email) . time();
  }
  
}
