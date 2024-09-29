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
 * The home page is a unit-wide simple page with content that can be edited by the leaders.
 * 
 * This class also provides a route to get the website logo.
 */
class HomePageController extends GenericPageController {
  
  protected function getEditRouteName() {
    return "edit_home_page";
  }
  protected function getShowRouteName() {
    return "home";
  }
  protected function getPageType() {
    return "home";
  }
  protected function isSectionPage() {
    return false;
  }
  protected function getPageTitle() {
    return "";
  }
  protected function canDisplayPage() {
    return true;
  }
  
  /**
   * Override variable in parent: this is the home page
   */
  protected $isHomePage = true;
  
  /**
   * [Route] Overrides the parent's showPage method to redirect to a specific section's
   * home page if a section is selected via the section menu
   */
  public function showPage() {
    // Check if the site is being initialized
    if (!Parameter::get(Parameter::$BOOTSTRAPPING_DONE)) {
      // Website bootstrapping process still in progress
      return redirect()->route('bootstrapping');
    }
    // Redirect to a section's home page if a section is specified
    $routeParameters = Route::current()->parameters();
    if (array_key_exists("section_slug", $routeParameters)) {
      if ($this->section->id != 1) {
        return redirect()->route('section', array('section_slug' => $routeParameters['section_slug']));
      }
    }
    // No section specified, show page normally
    return parent::showPage();
  }
  
  /**
   * [Route] Returns the website's logo image
   */
  public function websiteLogo() {
    $logoName = Parameter::get(Parameter::$LOGO_IMAGE);
    if ($logoName) {
      $path = storage_path(Parameter::$LOGO_IMAGE_FOLDER . $logoName);
      return response(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
          "Cache-control" => "public, max-age=3600"
      ));
    }
    abort(204); // No content
  }
  
  /**
   * [Route] Returns the website's icon image
   */
  public function websiteIcon() {
    $iconName = Parameter::get(Parameter::$ICON_IMAGE);
    if ($iconName) {
      $path = storage_path(Parameter::$ICON_IMAGE_FOLDER . $iconName);
      return response(file_get_contents($path), 200, array(
          "Content-Type" => "image",
          "Content-Length" => filesize($path),
          "Cache-control" => "public, max-age=3600"
      ));
    }
    abort(204); // No content
  }
  
}
