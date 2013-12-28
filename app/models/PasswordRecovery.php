<?php

class PasswordRecovery extends Eloquent {
  
  protected $fillable = array('user_id', 'code', 'timestamp');
  
  public static function createForUser(User $user) {
    return self::create(array(
        'user_id' => $user->id,
        'code' => self::generateCode(),
        'timestamp' => time()
    ));
  }
  
  public static function generateCode() {
    return sha1(rand() . time()) . time();
  }
  
  public function getUser() {
    return User::find($this->user_id);
  }
  
}
