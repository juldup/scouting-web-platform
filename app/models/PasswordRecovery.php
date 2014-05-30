<?php

/**
 * This Eloquent class reprents a code to reset a forgotten password
 * 
 * Columns:
 *   - user_id:   The user that wants to change his/her password
 *   - code:      An access code to update the password
 *   - timestamp: The time at which the password recovery was requested (the code is only valid for a few hours or days)
 */
class PasswordRecovery extends Eloquent {
  
  protected $fillable = array('user_id', 'code', 'timestamp');
  
  /**
   * Creates, saves and returns a new password recovery instance for a given user
   */
  public static function createForUser(User $user) {
    return self::create(array(
        'user_id' => $user->id,
        'code' => self::generateCode(),
        'timestamp' => time()
    ));
  }
  
  /**
   * Generates a new password recovery code
   */
  public static function generateCode() {
    return sha1(rand() . time()) . time();
  }
  
  /**
   * Returns the user associated with this password recovery instance
   */
  public function getUser() {
    return User::find($this->user_id);
  }
  
}
