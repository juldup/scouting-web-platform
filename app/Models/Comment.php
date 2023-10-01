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
 * This Eloquent class represents a comment on an object on the website
 * 
 * Columns:
 *   - user_id:       The user who wrote this comment
 *   - referent_id:   The id of object (news, photo, etc.) to which this comment belongs
 *   - referent_type: The class of the object this comment belongs to ('news', 'photo', etc.)
 *   - body:          The content of the comment
 */
class Comment extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Returns the list of comments on the given item
   */
  public static function listFor($referentType, $referentId) {
    return self::where('referent_type', '=', $referentType)
            ->where('referent_id', '=', $referentId)
            ->orderBy('created_at')
            ->get();
  }
  
  /**
   * Returns the username of the author of this comment
   */
  public function getUserName() {
    $user = User::find($this->user_id);
    if ($user) return $user->username;
    return "anonyme";
  }
  
  /**
   * Returns the moment the comment was written relative to now
   */
  public function getHumanDate() {
    return DateHelper::distanceofTimeInWords(strtotime($this->created_at));
  }
  
}
