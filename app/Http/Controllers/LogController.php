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

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\CalendarPDF;
use App\Helpers\DateHelper;
use App\Helpers\ElasticsearchHelper;
use App\Helpers\EnvelopsPDF;
use App\Helpers\Form;
use App\Helpers\HealthCardPDF;
use App\Helpers\Helper;
use App\Helpers\ListingComparison;
use App\Helpers\ListingPDF;
use App\Helpers\Resizer;
use App\Helpers\ScoutMailer;
use App\Models\Absence;
use App\Models\AccountingItem;
use App\Models\AccountingLock;
use App\Models\ArchivedLeader;
use App\Models\Attendance;
use App\Models\BannedEmail;
use App\Models\CalendarItem;
use App\Models\Comment;
use App\Models\DailyPhoto;
use App\Models\Document;
use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\GuestBookEntry;
use App\Models\HealthCard;
use App\Models\Link;
use App\Models\LogEntry;
use App\Models\Member;
use App\Models\MemberHistory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Parameter;
use App\Models\PasswordRecovery;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\PendingEmail;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Privilege;
use App\Models\Section;
use App\Models\Suggestion;
use App\Models\TemporaryRegistrationLink;
use App\Models\User;

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
          'user' => $user ? $user->username : ($log->user_id === "0" || $log->user_id === 0 ? "Cron job" : "Visiteur"),
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
