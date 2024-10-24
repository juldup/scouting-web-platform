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
 * The unit policy page is a global page with content that can be edited by the leaders.
 */
class LeaderPolicyPageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_leader_policy_page";
  }
  protected function getShowRouteName() {
    return "leader_policy";
  }
  protected function getPageType() {
    return "leader_policy";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Charte des animateurs";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_LEADER_POLICY) && $this->user->isLeader();
  }
  protected function getAdditionalContentSubview() {
    return 'subviews.leaderPolicyForm';
  }
  
  /**
   * [Route] Called when the user submits the signature form
   */
  public function submitSignature(Request $request) {
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    $signed = $request->input('leader_policy_signed') ? true : false;
    $leaders = $this->user->getAssociatedLeaderMembers();
    if (!count($leaders)) return Helper::forbiddenResponse();
    $leaderNameList = "";
    foreach ($leaders as $leader) {
      if (($leader->leader_policy_signed ? "1" : "0") != ($signed ? "1" : "0")) {
        $leaderNameList .= ($leaderNameList ? ", " : "") . $leader->leader_name;
        $leader->leader_policy_signed = $signed ? "1" : "0";
        $leader->save();
      }
    }
    if ($leaderNameList) {
      if ($signed) {
        LogEntry::log("Animateurs", "Charte des animateurs signée par $leaderNameList");
      } else {
        LogEntry::log("Animateurs", "Retrait de la signature de la charte des animateurs par $leaderNameList");
      }
    }
    return redirect()->route('leader_policy')
            ->with('success_message', $signed ? "Tu as signé la charte des animateurs." :
                    "Tu n'adhères pas ou plus à la charte des animateurs.");
    
  }
  
}
