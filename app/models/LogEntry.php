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
 * This Eloquent class reprents a log entry that logs a user action on the website
 * 
 * Columns:
 *   - user_id:    The id of the user that commited the action (can be null, keeps its value if the user is deleted)
 *   - section_id: The id of the section of this action (if any)
 *   - category:   The category of the action (used for filtering)
 *   - action:     A description of the action made by the user
 *   - data:       Data associated with the log (key-values in json format)
 *   - is_error:   Whether this log is for an error
 */
class LogEntry extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Creates a new log entry for the current user and persists it to the database
   * 
   * @param string $category  The category of the action
   * @param string $action    The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data       An array containing the data detailing the log
   */
  public static function log($category, $action, $data = null) {
    self::createLog($category, $action, $data, false);
  }
  
  /**
   * Creates a new log entry tagged as an error for the current user and persists it to the database
   * 
   * @param string $category  The category of the action
   * @param string $action    The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data       An array containing the data detailing the log
   */
  public static function error($category, $action, $data = null) {
    self::createLog($category, $action, $data, true);
  }
  
  /**
   * Creates a new log entry for the current user and persists it to the database
   * 
   * @param string $category  The category of the action
   * @param string $action    The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data       An array containing the data detailing the log
   * @param boolean $isError      Whether this is an error log
   */
  private static function createLog($category, $action, $data, $isError) {
    try {
      // Construct data array
      $dataArray = array();
      foreach ($data as $key => $value) {
        $dataArray[] = array(
            'key' => $key,
            'value' => $value,
        );
      }
      // Save log
      LogEntry::create(array(
          'user_id' => Session::get('user_id'),
          'section_id' => View::shared('user') ? View::shared('user')->currentSection->id : null,
          'category' => $category,
          'action' => $action,
          'data' => json_encode($dataArray),
          'is_error' => $isError,
      ));
    } catch (Exception $ex) {
      Log::error($ex);
    }
  }
  
}

