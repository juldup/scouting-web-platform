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
 * This Eloquent class represents a temporary access to the registration form
 * 
 * Columns:
 *   - code:       The code to access the registration form
 *   - expiration: The date and time for expiration
 */
class TemporaryRegistrationLink extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Creates a new instance of this in the database, with the expiration date
   * set $days days in the future from now, at 23:59:59
   */
  public static function createWithDays($days) {
    $date = strtotime("+$days day", time());
    $formattedDate = date('Y-m-d', $date) . " 23:59:59";
    $code = substr(hash('sha256', rand() . time()),0,40);
    $link = self::create(array(
          'code' => $code,
          'expiration' => $formattedDate,
      ));
    return $link;
  }
  
  /**
   * Returns whether a code is valid, i.e. if it corresponds to a temporary
   * registration link that has not expired
   */
  public static function codeIsValid($code) {
    $link = TemporaryRegistrationLink::where('code', '=', $code)->first();
    if ($link) {
      $date = $link->expiration;
      if (date('Y-m-d H:i:s') > $date) {
        return false;
      }
      return true;
    }
    return false;
  }
  
}
