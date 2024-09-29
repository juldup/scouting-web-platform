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
 * The ban e-mail address tool allows e-mail recipients to ban their e-mail address
 * from our recipient list. If so, they will never receive any e-mail from this website.
 * This is useful to prevent people from reporting our e-mail as spam.
 * 
 * Each e-mail address has an associated ban code in table 'banned_emails'. The ban code
 * is only ever sent to the given e-mail address.
 */
class BanEmailAddressController extends BaseController {
  
  /**
   * [Route] Shows a view for the user to confirm banning their e-mail address.
   * 
   * @param string $ban_code  The code associated to the e-mail address to ban
   */
  public function banEmailAddress($ban_code) {
    $banned = BannedEmail::where('ban_code', '=', $ban_code)->first();
    if (!$banned) {
      abort(404);
    }
    if (Helper::emailIsInListing($banned->email)) {
      return View::make('pages.banEmailAddress.emailInListing', array(
          'email' => $banned->email,
      ));
    }
    if ($banned->banned) {
      // The e-mail address is already banned, show unban page
      return View::make('pages.banEmailAddress.unbanEmailAddress', array(
          'email' => $banned->email,
          'ban_code' => $ban_code,
      ));
    }
    return View::make('pages.banEmailAddress.banEmailAddress', array(
        'email' => $banned->email,
        'ban_code' => $ban_code,
    ));
  }
  
  /**
   * [Route] Called when the user confirms the ban of the e-mail address.
   * Returns a confirmation page.
   * 
   * @param string $ban_code  The code associated to the e-mail address to ban
   */
  public function confirmBanEmailAddress($ban_code) {
    $banned = BannedEmail::where('ban_code', '=', $ban_code)->first();
    if (!$banned) {
      abort(404);
    }
    // Mark e-mail address as banned
    $banned->banned = true;
    $banned->save();
    // Save log
    LogEntry::log("Ban", "Plus aucun e-mail ne sera envoyé à une adresse e-mail", array('Adresse e-mail' => $banned->email));
    // Return view
    return View::make('pages.banEmailAddress.confirmBan', array(
        'email' => $banned->email,
        'ban_code' => $ban_code,
    ));
  }
  
  /**
   * [Route] Called when the user cancels the ban of the e-mail address.
   * Returns a confirmation page.
   * 
   * @param string $ban_code  The code associated to the e-mail address to ban
   */
  public function cancelBanEmailAddress($ban_code) {
    $banned = BannedEmail::where('ban_code', '=', $ban_code)->first();
    if (!$banned) {
      abort(404);
    }
    // Mark e-mail address as unbanned
    $banned->banned = false;
    $banned->save();
    // Save log
    LogEntry::log("Ban", "Annulation", array('Adresse e-mail' => $banned->email));
    // Return view
    return View::make('pages.banEmailAddress.confirmUnban', array(
        'email' => $banned->email,
        'ban_code' => $ban_code,
    ));
  }
  
}
