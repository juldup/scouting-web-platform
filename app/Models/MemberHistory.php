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
 * This Eloquent class represents a member's history entry (a member, a section, a year)
 * 
 * Columns:
 *   - member_id:                     The member
 *   - section_id:                    The section
 *   - year:                          The year in format YYYY-YYYY
 *   - subgroup:                      The subgroup in the section this member belonged to
 *   - role:                          The role of the member had in the section
 */
class MemberHistory extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public static function getForMember($memberId) {
    return self::where('member_id', '=', $memberId)
            ->orderBy('year', 'asc')
            ->get();
  }
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getSubgroupForDisplay() {
    $section = $this->getSection();
    if ($section) $subgroupName = $section->subgroup_name;
    else $subgroupName = "Groupe";
    return $subgroupName . " : " . $this->subgroup;
  }
  
  /**
   * Creates history entries for all members of the previous year if they don't exist yet
   */
  public static function createHistoryIfNeeded() {
    $lastYear = DateHelper::getLastYearForArchiving();
    $count = self::where('year', '=', $lastYear)
            ->take(1)
            ->count();
    if (!$count) {
      self::createHistoryEntries($lastYear);
      LogEntry::log("Listing", "Création de l'historique", array("Année" => $lastYear));
    }
  }
  
  /**
   * Creates an archived leader entry for each active leaders, with the given $lastYear as year
   */
  private static function createHistoryEntries($lastYear) {
    $members = Member::where('validated', '=', true)->get();
    foreach ($members as $member) {
      $subgroup = ($member->is_leader ? "" : $member->subgroup);
      $role = ($member->is_leader
               ? (($member->gender == 'M' ? "Animateur" : "Animatrice") . ($member->leader_in_charge ? " responsable" : ""))
               : $member->role);
      self::create(array(
          'member_id' => $member->id,
          'year' => $lastYear,
          'section_id' => $member->section_id,
          'section_name_backup' => $member->getSection()->name,
          'subgroup' => $subgroup == null ? "" : $subgroup,
          'role' => $role == null ? "" : $role,
      ));
    }
  }
  
}
