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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

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
class LogEntry extends Model {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // False, unless the current task is a cron job
  public static $isCronJobUser = false;
  
  /**
   * Creates a new log entry for the current user and persists it to the database
   * 
   * @param string $category  The category of the action
   * @param string $action    The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data       An array containing the data detailing the log
   */
  public static function log($category, $action, $data = array(), $mergeWithPreviousSimilar = false) {
    self::createLog($category, $action, $data, false, $mergeWithPreviousSimilar);
  }
  
  /**
   * Creates a new log entry tagged as an error for the current user and persists it to the database
   * 
   * @param string $category  The category of the action
   * @param string $action    The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data       An array containing the data detailing the log
   */
  public static function error($category, $action, $data = array(), $mergeWithPreviousSimilar = false) {
    self::createLog($category, $action, $data, true, $mergeWithPreviousSimilar);
  }
  
  /**
   * Creates a new log entry for the current user and persists it to the database
   * 
   * @param string $category                   The category of the action
   * @param string $action                     The action of this log (either a string or an array with 'text' and 'category' keys)
   * @param array $data                        An array containing the data detailing the log
   * @param boolean $isError                   Whether this is an error log
   * @param boolean $mergeWithPreviousSimilar  If true and the previous log is of the same category, user and section, they will be merged
   */
  private static function createLog($category, $action, $data, $isError, $mergeWithPreviousSimilar) {
    try {
      // Construct data array
      $dataArray = array();
      foreach ($data as $key => $value) {
        $dataArray[] = array(
            'key' => $key,
            'value' => $value,
        );
      }
      $userId = self::$isCronJobUser ? 0 : Session::get('user_id');
      $sectionId = View::shared('user') ? View::shared('user')->currentSection->id : null;
      // Check if this log can be merged with the previous log
      if ($mergeWithPreviousSimilar) {
        $lastLog = LogEntry::orderBy("id", "desc")->first();
        if ($lastLog->category == $category && $lastLog->action == $action && $lastLog->user_id == $userId && $lastLog->section_id == $sectionId) {
          // Merge log with the previous log
          $data = json_decode($lastLog->data, true);
          if (!array_key_exists("multiple", $data)) {
            // Previous log was not a multiple log yet, make it one
            $data = ["multiple" => [$data]];
          }
          $data["multiple"][] = $dataArray;
          $lastLog->data = json_encode($data);
          $lastLog->save();
          return;
        }
      }
      // Create a new log entry
      LogEntry::create(array(
          'user_id' => $userId,
          'section_id' => $sectionId,
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

