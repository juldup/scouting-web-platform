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
 * The relevant actions on the website are logged. This controller fills the page
 * that allows the leaders to view the logs
 */
class LogController extends BaseController {
  
  protected $pagesAdaptToSections = false;
  
  /**
   * [Route] Shows the log page
   */
  public function showPage() {
    // Make sure the user can see the logs
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    return View::make('pages.logs.logs', array(
        'logs_per_request' => 500,
    ));
  }
  
  /**
   * [Route] Ajax call to load more logs to the bottom of the list
   */
  public function loadMoreLogs($lastKnownLogId, $limit) {
    // Make sure the user can see the logs
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get logs
    if (!$lastKnownLogId) $lastKnownLogId = PHP_INT_MAX;
    $logs = LogEntry::where('id', '<', $lastKnownLogId)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();
    // Construct log list
    $response = array();
    foreach ($logs as $log) {
      $user = $log->user_id ? User::find($log->user_id) : null;
      $section = $log->section_id ? Section::find($log->section_id) : null;
      $response[] = array(
          'id' => $log->id,
          'date' => date('Y/m/d H:i:s', strtotime($log->created_at)),
          'userEmail' => $user ? $user->email : "",
          'user' => $user ? $user->username : "Visiteur",
          'category' => $log->category,
          'action' => $log->action,
          'data' => json_decode($log->data, true),
          'section' => $section ? $section->name : $section,
          'isError' => $log->is_error ? true : false,
      );
    }
    // Return log list
    return json_encode($response);
  }
  
}
