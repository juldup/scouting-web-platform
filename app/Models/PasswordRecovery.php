<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * This Eloquent class reprents a code to reset a forgotten password
 * 
 * Columns:
 *   - user_id:   The user that wants to change his/her password
 *   - code:      An access code to update the password
 *   - timestamp: The time at which the password recovery was requested (the code is only valid for a few hours or days)
 */
class PasswordRecovery extends Model {
  
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
