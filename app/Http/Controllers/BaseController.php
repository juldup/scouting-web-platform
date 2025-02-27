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
use Illuminate\Routing\Controller;
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
 * This class is the parent of all controllers and offers some common
 * functionalities: it logs in the user and set the selected section.
 */
abstract class BaseController extends Controller {
  
  // The current user
  protected $user;
  
  // The current section
  protected $section;
  
  // True when the current controller's page(s) adapt to the sections
  protected $pagesAdaptToSections = false;
  
  /**
   * Can be overriden by a controller when some of its page are section pages and some are not
   */
  protected function currentPageAdaptToSections() {
    return $this->pagesAdaptToSections;
  }
  
  /**
   * Creates layout used by the controller.
   */
  protected function setupLayout() {
    if (!is_null($this->layout)) {
      $this->layout = View::make($this->layout);
    }
  }
  
  /**
   * Constructor: logs in the user and retrieves the currently selected section
   */
  public function __construct() {
    $this->middleware(function (Request $request, $next) {
      $this->user = self::getUser();
      // Retrieve section slug in route parameters
      $routeParameters = Route::current()->parameters();
      $sectionSlug = "";
      if (array_key_exists("section_slug", $routeParameters)) {
        $sectionSlug = $routeParameters['section_slug'];
      }
      // Select current tab
      $this->selectSection($sectionSlug);
      // Determine whether the current page is a section specific page
      View::share('section_page', $this->currentPageAdaptToSections());
      return $next($request);
    });
  }
  
  /**
   * Tries logging in the user and returns it. Returns a dummy user if
   * the user is not logged in.
   */
  public static function getUser() {
    // Retrieve user id from session
    $userId = Session::get('user_id', null);
    // Retrieve user id from cookies
    if ($userId === null) {
      $username = Cookie::get(User::getCookieUsernameName());
      $password = Cookie::get(User::getCookiePasswordName());
      if ($username && $password) {
        $user = User::getWithUsernameAndPassword($username, $password);
        if ($user) {
          $userId = $user->id;
          Session::put('user_id', $userId);
        }
      }
    }
    $resultUser = null;
    if ($userId) {
      // Find user
      $resultUser = User::find($userId);
      if ($resultUser) {
        if ($resultUser->last_visit < time() - 3600) {
          $resultUser->last_visit = time();
          $resultUser->save();
        }
      }
    }
    if ($resultUser == null) {
      // Load dummy user
      $resultUser = User::disconnectedUser();
    }
    View::share('user', $resultUser);
    return $resultUser;
  }
  
  /**
   * Sets the given section as selected
   * 
   * @param string/null $section_slug  The selected section's slug, or null
   */
  protected function selectSection($section_slug) {
    // Determine currently selected section
    $section = null;
    // A specific section is selected
    if ($section_slug) {
      $sections = Section::where('slug', '=', $section_slug)->get();
      if (count($sections)) {
        $section = $sections[0];
      }
    }
    // Use section from current session
    if ($section === null && Session::has('currentSection')) {
      $section = Section::find(Session::get('currentSection', '1'));
    }
    // Use default section for the user
    if ($section === null && isset($this->user->default_section)) {
      $section = Section::find($this->user->default_section);
    }
    // Use main section
    if ($section == null) {
      $section = Section::find(1);
    }
    $this->user->currentSection = $section;
    $this->section = $section;
    // Save selected section to session
    Session::put('currentSection', $section->id);
  }
  
}
