<?php

class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  protected static $PICTURE_FOLDER_PATH = "../app/storage/site_data/leader_pictures/";
  
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
  
}
