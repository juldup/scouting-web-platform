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
 * Page
 */
class RegistrationInactiveController extends GenericPageController {
  
  protected function currentPageAdaptToSections() {
    return $this->user->isLeader();
  }
  
  protected function getEditRouteName() {
    return "edit_registration_inative_page";
  }
  protected function getShowRouteName() {
    return "registration";
  }
  protected function getPageType() {
    return "registration_inactive";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "Inscription dans l'unité";
  }
  protected function getPageEditTitle() {
    return "Inscription dans l'unité (lorsque les inscriptions sont désactivées)";
  }
  protected function getAdditionalEditInformationSubview() {
    return 'subviews.registrationEditInformation';
  }
  protected function canDisplayPage() {
    return Parameter::get(Parameter::$SHOW_REGISTRATION);
  }
  
  /**
   * [Route] Shows the public registration information page
   */
  public function showMain() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_REGISTRATION)) {
      abort(404);
    }
    // Redirect to active registration page if activated
    if (Parameter::registrationIsActive()) {
      return redirect()->route('registration');
    }
    // Get page text and update it with the parametric values
    $page = $this->getPage();
    $pageBody = $page->body_html;
    $pageBody = str_replace("(PRIX UN ENFANT)", Parameter::get(Parameter::$PRICE_1_CHILD), $pageBody);
    $pageBody = str_replace("(PRIX DEUX ENFANTS)", Parameter::get(Parameter::$PRICE_2_CHILDREN), $pageBody);
    $pageBody = str_replace("(PRIX TROIS ENFANTS)", Parameter::get(Parameter::$PRICE_3_CHILDREN), $pageBody);
    $pageBody = str_replace("(PRIX UN ANIMATEUR)", Parameter::get(Parameter::$PRICE_1_LEADER), $pageBody);
    $pageBody = str_replace("(PRIX DEUX ANIMATEURS)", Parameter::get(Parameter::$PRICE_2_LEADERS), $pageBody);
    $pageBody = str_replace("(PRIX TROIS ANIMATEURS)", Parameter::get(Parameter::$PRICE_3_LEADERS), $pageBody);
    $pageBody = str_replace("BEXX-XXXX-XXXX-XXXX", Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), $pageBody);
    $pageBody = str_replace("(ACCES CHARTE)", '<a href="' . URL::route('unit_policy') . '">charte d&apos;unité</a>', $pageBody);
    $pageBody = str_replace("(ACCES RGPD)", '<a href="' . URL::route('gdpr') . '">RGPD</a>', $pageBody);
    $pageBody = str_replace("(ACCES CONTACT)", '<a href="' . URL::route('contacts') . '">contact</a>', $pageBody);
    $pageBody = str_replace("(ACCES FORMULAIRE)", '<a href="' . URL::route('registration_form') . '">formulaire d&apos;inscription</a>', $pageBody);
    // The registration are not active, show a default page
    return View::make('pages.registration.registrationInactive', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_PAGES, 1),
        'can_manage' => $this->user->can(Privilege::$EDIT_LISTING_ALL, $this->section)
                            || $this->user->can(Privilege::$EDIT_LISTING_LIMITED, $this->section)
                            || $this->user->can(Privilege::$SECTION_TRANSFER, 1),
        'page_title' => $this->getPageTitle(),
        'page_body' => $pageBody,
    ));
  }
  
}
