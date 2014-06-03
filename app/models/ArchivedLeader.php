<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This Eloquent class represents the data of a former leader. Each leader of
 * a previous year has one instance of this class for each year they were a leader.
 * 
 * Columns:
 *   - member_id:            The active member if the leader is still registered, null if the leader has left the unit
 *   - year:                 The year this member was a leader
 *   - first_name:           The first name of the leader
 *   - last_name:            The last name of the leader
 *   - gender:               The gender M/F of the leader
 *   - totem:                The totem of the leader (if any)
 *   - quali:                The quali of the leader (if any)
 *   - section_id:           The section this leader was in
 *   - phone_member:         The phone number of the leader
 *   - phone_member_private: Whether the phone number is private
 *   - email_member:         The e-mail address of the leader
 *   - leader_in_charge:     Whether they were leader in charge of the section
 *   - leader_name:          Their leader name (how they were called in the section)
 *   - leader_description:   A short description of the leader
 *   - leader_role:          The role the leader was playing in the section
 *   - has_picture:          Whether there is a picture for this leader
 *   - picture_filename:     The file name of the picture (if any)
 */
class ArchivedLeader extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Creates archived leader entries for all leaders of the previous year if they don't exist yet
   */
  public static function archiveLeadersIfNeeded() {
    $lastYear = self::getLastYear();
    $count = ArchivedLeader::where('year', '=', $lastYear)
            ->take(1)
            ->count();
    if (!$count) {
      self::archiveLeaders($lastYear);
    }
  }
  
  /**
   * Creates an archived leader entry for each active leaders, with the given $lastYear as year
   */
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
  
  /**
   * Returns the 'YYYY-YYYY' string representation of the previous scouting year
   */
  private static function getLastYear() {
    $month = date('m');
    $startYear = date('Y') - 1;
    if ($month <= 8) $startYear--;
    return $startYear . "-" . ($startYear + 1);
  }
  
  /**
   * Returns the URL of the picture of this leader
   */
  public function getPictureURL() {
    return URL::route('get_archived_leader_picture', array('archived_leader_id' => $this->id));
  }
  
  /**
   * Returns the path of the picture in the file system
   */
  public function getPicturePath() {
    return storage_path(Member::$PICTURE_FOLDER_PATH) . $this->picture_filename;
  }
  
}
