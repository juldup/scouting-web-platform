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
 * This eloquent class represents an entry associated with an e-mail address,
 * with a field telling whether the e-mail address is banned and no mail should be
 * sent to it.
 * 
 * Columns:
 *   - email:    The e-mail address
 *   - ban_code: A code sent along the e-mails in a link to ban the e-mail address
 *   - banned:   Whether this e-mail address is banned
 */
class BannedEmail extends Model {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Returns the ban code associated with an e-mail address, creating
   * a new entry if none exists
   */
  public static function getCodeForEmail($email) {
    $banned = self::where('email', '=', $email)->first();
    if (!$banned) {
      $banned = BannedEmail::create(array(
          'email' => $email,
          'ban_code' => self::generateBanCode($email),
          'banned' => false,
      ));
    }
    return $banned->ban_code;
  }
  
  /**
   * Returns whether the given e-mail address is banned
   */
  public static function isBanned($email) {
    $banned = self::where('email', '=', $email)
          ->where('banned', '=', true)
          ->first();
    if ($banned) return true;
    return false;
  }
  
  /**
   * Generates a new ban code
   */
  private static function generateBanCode($email) {
    return hash('sha256', rand() . $email) . time();
  }
  
}
