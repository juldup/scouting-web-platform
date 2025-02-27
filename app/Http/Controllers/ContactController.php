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
 * The contact page publicly shows:
 * - the address of the unit's premises (an editable page)
 * - the contact information of the leaders in charge.
 * - a list of external links (see LinkController)
 * 
 * The contacts' e-mail addresses are kept private, and so are the phone numbers that are marked as private.
 */
class ContactController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_address_page";
  }
  protected function getShowRouteName() {
    return "contacts";
  }
  protected function getPageType() {
    return "addresses";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Adresse";
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_ADDRESSES);
  }
  
  /**
   * [Route] Shows the contact page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_CONTACTS)) {
      abort(404);
    }
    // Get page
    $page = $this->getPage();
    $pageBody = $page->body_html;
    // Find unit staff
    $unitLeaders = Member::where('is_leader', '=', true)
            ->where('section_id', '=', '1')
            ->where('validated', '=', true)
            ->orderBy('leader_in_charge', 'desc')
            ->orderBy('list_order', 'asc')
            ->orderBy('leader_name')
            ->get();
    // Find sections' leaders in charge
    $sections = Section::where('id', '!=', 1)
            ->orderBy('position')
            ->get();
    $sectionLeaders = array();
    foreach ($sections as $section) {
      $leader = Member::where('is_leader', '=', true)
              ->where('leader_in_charge', '=', true)
              ->where('validated', '=', true)
              ->where('section_id', '=', $section->id)
              ->first();
      if ($leader) $sectionLeaders[] = $leader;
    }
    // Get links
    $links = Link::all();
    // Make view
    return View::make('pages.contacts.contacts', array(
        "unitLeaders" => $unitLeaders,
        "sectionLeaders" => $sectionLeaders,
        "webmaster" => array(
            "name" => "Julien Dupuis",
            "phone" => "",
        ),
        "links" => $links,
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
        'page_body' => $pageBody,

    ));
  }
  
}
