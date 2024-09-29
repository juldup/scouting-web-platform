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
 * This controller provides a way to see the maintenance actions that
 * should be done be the webmaster or the leaders
 */
class MonitoringController extends BaseController {
  
  /**
   * [Route] Shows the monitoring page
   */
  public function showPage() {
    // Make sure the app
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get status
    $emailLastExecution = Parameter::get(Parameter::$CRON_EMAIL_LAST_EXECUTION);
    $healthCardsLastExecution = Parameter::get(Parameter::$CRON_HEALTH_CARDS_LAST_EXECUTION);
    $incrementYearInSectionLastExecution = Parameter::get(Parameter::$CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION);
    $cleanUpUnverifiedAccountsLastExecution = Parameter::get(Parameter::$CRON_CLEAN_UP_UNUSED_ACCOUNTS);
    $updateElasticsearchLastExecution = Parameter::get(Parameter::$CRON_UPDATE_ELASTICSEARCH);
    // Show page
    return View::make('pages.monitoring.monitoring', array(
        "emailLastExecution" => $emailLastExecution,
        "healthCardsLastExecution" => $healthCardsLastExecution,
        "incrementYearInSectionLastExecution" => $incrementYearInSectionLastExecution,
        "cleanUpUnverifiedAccountsLastExecution" => $cleanUpUnverifiedAccountsLastExecution,
        "updateElasticsearchLastExecution" => $updateElasticsearchLastExecution,
        "emailTimedOut" => self::emailTimedOut($emailLastExecution),
        "healthCardsTimedOut" => self::healthCardsTimedOut($healthCardsLastExecution),
        "incrementYearInSectionTimedOut" => self::incrementYearInSectionTimedOut($incrementYearInSectionLastExecution),
        "updateElasticsearchTimedOut" => self::updateElasticsearchTimedOut($updateElasticsearchLastExecution),
    ));
  }
  
  /**
   * Returns true if some cron task have timed out
   */
  public static function cronTaskTimedOut() {
    $emailLastExecution = Parameter::get(Parameter::$CRON_EMAIL_LAST_EXECUTION);
    $healthCardsLastExecution = Parameter::get(Parameter::$CRON_HEALTH_CARDS_LAST_EXECUTION);
    $incrementYearInSectionLastExecution = Parameter::get(Parameter::$CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION);
    $updateElasticsearchLastExecution = Parameter::get(Parameter::$CRON_UPDATE_ELASTICSEARCH);
    return self::emailTimedOut($emailLastExecution) ||
            self::healthCardsTimedOut($healthCardsLastExecution) ||
            self::incrementYearInSectionTimedOut($incrementYearInSectionLastExecution) ||
            self::updateElasticsearchTimedOut($updateElasticsearchLastExecution);
  }
  
  /**
   * Returns true if the email cron task has timed out
   */
  private static function emailTimedOut($emailLastExecution) {
    return !$emailLastExecution || (time() - $emailLastExecution > 3600 * 3); // More than 3 hours ago
  }
  
  /**
   * Returns true if the health cards cron task has timed out
   */
  private static function healthCardsTimedOut($healthCardsLastExecution) {
    return !$healthCardsLastExecution || (time() - $healthCardsLastExecution > 3600 * 24 * 2); // More than 2 days ago
  }
  
  /**
   * Returns true if the increment year in section cron task has timed out
   */
  private static function incrementYearInSectionTimedOut($incrementYearInSectionLastExecution) {
    $year = date('m') < 8 ? date('Y') - 1 : date('Y');
    $lastAugustFirst = strtotime($year . "-08-02");
    return !$incrementYearInSectionLastExecution || $incrementYearInSectionLastExecution < $lastAugustFirst;
  }
  
  /**
   * Returns true if the update elasticsearch cron task has timed out
   */
  private static function updateElasticsearchTimedOut($updateElasticsearchLastExecution) {
    // No timeout if search is disabled
    if (!Parameter::get(Parameter::$SHOW_SEARCH)) return false;
    // Check that the update has been done in the last 26 hours
    return !$updateElasticsearchLastExecution || $updateElasticsearchLastExecution < time() - 3600 * 26;
  }
  
}
