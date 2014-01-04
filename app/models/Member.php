<?php

class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $PICTURE_FOLDER_PATH = "../app/storage/site_data/leader_pictures/";
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public static function existWithEmail($email) {
    if (!$email) return false;
    $aMember = Member::where(function($query) use ($email) {
      $query->where('email1', '=', $email);
      $query->orWhere('email2', '=', $email);
      $query->orWhere('email3', '=', $email);
      $query->orWhere('email_member', '=', $email);
    })->where('validated', '=', true)
            ->first();
    if ($aMember) return true;
    else return false;
  }
  
  public function getPictureURL() {
    return URL::route('get_leader_picture', array('leader_id' => $this->id));
  }
  
  public function getPicturePath() {
    return $this->getPicturePathFolder() . $this->getPicturePathFilename();
  }
  
  public function getPicturePathFolder() {
    return self::$PICTURE_FOLDER_PATH;
  }
  
  public function getPicturePathFilename() {
    return $this->id . ".picture";
  }
  
  public static function getFamilyOtherUnitsForSelect() {
    return array('0' => "Aucun", '1' => "1", '2' => '2 ou plus');
  }
  
  public function getPublicPhone() {
    if ($this->phone1 && !$this->phone1_private) return $this->phone1;
    if ($this->phone2 && !$this->phone2_private) return $this->phone2;
    if ($this->phone3 && !$this->phone3_private) return $this->phone3;
    if ($this->phone_member && !$this->phone_member_private) return $this->phone_member;
    return "";
  }
  
}
