<?php

class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public static function existWithEmail($email) {
    if (!$email) return false;
    $aMember = Member::where('email1', '=', $email)
            ->orWhere('email2', '=', $email)
            ->orWhere('email3', '=', $email)
            ->orWhere('email_member', '=', $email)->first();
    if ($aMember) return true;
    else return false;
  }
  
}
