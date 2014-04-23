<?php

class ArchivedLeader extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  public static function archiveLeadersIfNeeded() {
    $lastYear = self::getLastYear();
    $count = ArchivedLeader::where('year', '=', $lastYear)
            ->take(1)
            ->count();
    if (!$count) {
      self::archiveLeaders($lastYear);
    }
  }
  
  private static function archiveLeaders($lastYear) {
    $leaders = Member::where('validated', '=', true)
            ->where('is_leader', '=', true)
            ->get();
    foreach ($leaders as $leader) {
      ArchivedLeader::create(array(
          'member_id' => $leader->id,
          'year' => $lastYear,
          'first_name' => $leader->first_name,
          'last_name' => $leader->last_name,
          'gender' => $leader->gender,
          'totem' => $leader->totem,
          'quali' => $leader->quali,
          'section_id' => $leader->section_id,
          'phone_member' => $leader->phone_member,
          'phone_member_private' => $leader->phone_member_private,
          'email_member' => $leader->email_member,
          'leader_in_charge' => $leader->leader_in_charge,
          'leader_name' => $leader->leader_name,
          'leader_description' => $leader->leader_description,
          'leader_role' => $leader->leader_role,
          'has_picture' => $leader->has_picture,
          'picture_filename' => $leader->getPicturePathFilename(),
      ));
    }
  }
  
  private static function getLastYear() {
    $month = date('m');
    $startYear = date('Y') - 1;
    if ($month <= 8) $startYear--;
    return $startYear . "-" . ($startYear + 1);
  }
  
  public function getPictureURL() {
    return URL::route('get_archived_leader_picture', array('archived_leader_id' => $this->id));
  }
  
  public function getPicturePath() {
    return storage_path(Member::$PICTURE_FOLDER_PATH) . $this->picture_filename;
  }
  
}