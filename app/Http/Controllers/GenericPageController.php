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
 * Several pages of the website can be edited by the leaders. This class
 * provides a generic page controller that is extended for each editable
 * page of the website.
 */
abstract class GenericPageController extends BaseController {
  
  // The following methods must be implemented in the child class
  
  /**
   * Returns the name of the route that shows the page
   */
  protected abstract function getShowRouteName();
  
  /**
   * Returns a list of parameters that will be given to the show route
   */
  protected function getShowRouteParameters() {
    return array();
  }
  
  /**
   * Returns the name of the route that shows the page in edition mode
   */
  protected abstract function getEditRouteName();
  
  /**
   * Returns a list of parameters that will be given to the edit route
   */
  protected function getEditRouteParameters() {
    return array();
  }
  
  /**
   * Returns whether the page belongs to a section or is common to all sections
   */
  protected abstract function isSectionPage();
  
  /**
   * Returns an identifier of the type of page
   */
  protected abstract function getPageType();
  
  /**
   * Returns the title that is displayed at the top of the page
   * (if empty, there will be no title displayed on the page, and
   * the page's meta title will be the name of the unit)
   */
  protected abstract function getPageTitle();
  
  /**
   * Returns the title that is displayed at the top of the page
   * when in edit mode. By default, this is the same as the normal
   * page title
   */
  protected function getPageEditTitle() {
    return $this->getPageTitle();
  }
  
  /**
   * Returns a view with additional information to be displayed
   * on the edit page
   */
  protected function getAdditionalEditInformationSubview() {
    return null;
  }
  
  /**
   * Returns a view with additional information to be displayed
   * on the page
   */
  protected function getAdditionalContentSubview() {
    return null;
  }
  
  /**
   * Returns true if the page can be displayed (cf. site parameters)
   */
  protected abstract function canDisplayPage();
  
  /**
   * True only if this page is the home page (overriden in the home page controller)
   */
  protected $isHomePage = false;
  
  /**
   * [Route] Displays the page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!$this->canDisplayPage()) {
      abort(404);
    }
    // Get the page
    $page = $this->getPage();
    // Generate edit route
    $routeParameters = $this->getEditRouteParameters();
    if ($this->isSectionPage()) {
      // For section pages, add the section slug in the route parameters
      $routeParameters["section_slug"] = $this->user->currentSection->slug;
    } else {
      $routeParameters["section_slug"] = 'unite';
    }
    $editURL = URL::route($this->getEditRouteName(), $routeParameters);
    // Make view
    return View::make('pages.customPage.page', array(
        'page_body' => $page->body_html,
        'page_title' => $this->getPageTitle(),
        'page_slug' => $page->type . ($this->isSectionPage() ? "-" . Section::find($page->section_id)->slug : ""),
        'edit_url' => $editURL,
        'can_edit' => $this->canEdit(),
        'is_home_page' => $this->isHomePage,
        'additional_content' => $this->getAdditionalContentSubview(),
    ));
  }
  
  /**
   * [Route] Shows the page in edit mode
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!$this->canDisplayPage()) {
      abort(404);
    }
    // Make sure the user can edit this page
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    // Get the page and its images
    $page = $this->getPage();
    // Make view
    return View::make('pages.customPage.editPage')
            ->with('page_body', $page->body_html)
            ->with('page_title', $this->getPageEditTitle())
            ->with('page_id', $page->id)
            ->with('additional_information_subview', $this->getAdditionalEditInformationSubview())
            ->with('original_page_url', URL::route($this->getShowRouteName(), $this->getShowRouteParameters()));
  }
  
  /**
   * [Route] Saves the page that has been modified
   */
  public function savePage(Request $request) {
    // Make sure the user can edit this page
    if (!$this->canEdit()) {
      return Helper::forbiddenResponse();
    }
    // Get new page content from request
    $newBody = stripslashes($request->input('page_body'));
    // These next 3 lines fix a html/css bug in ckeditor
    $patterns = '/(<img .*) height="\d*"/';
    $replace = "\\1";
    $newBody = preg_replace($patterns, $replace, $newBody);
    // Update page
    $page = $this->getPage();
    $page->body_html = $newBody;
    $page->save();
    // Redirect back to page
    $routeParameters = $this->getShowRouteParameters();
    if ($this->isSectionPage()) {
      $routeParameters["section_slug"] = View::shared('user')->currentSection->slug;
    }
    LogEntry::log("Page", "Modification d'une page", array("Page" => $this->getPageTitle() ?: "Page d'accueil")); // TODO improve log message
    return redirect()->route($this->getShowRouteName(), $routeParameters);
  }
  
  /**
   * Fetches the page from the database and returns it. If the page
   * does not exist, a new one is created.
   */
  protected function getPage() {
    // Get section id
    if ($this->isSectionPage()) {
      $sectionId = View::shared('user')->currentSection->id;
    } else {
      $sectionId = 1;
    }
    // Get page
    $page = Page::where('section_id', '=', $sectionId)->where('type', '=' , $this->getPageType())->first();
    // Generate a new page if it does not exist
    if (!$page) {
      $page = Page::create(array(
          "type" => $this->getPageType(),
          "section_id" => $sectionId,
          "body_html" => "<p><span class='glyphicon glyphicon glyphicon-time'></span> En construction...</p>",
      ));
    }
    // Return the page
    return $page;
  }
  
  /**
   * Returns whether the user is allowed to edit this page
   */
  protected function canEdit() {
    if ($this->isSectionPage()) {
      return $this->user->can(Privilege::$EDIT_PAGES, $this->user->currentSection);
    } else {
      return $this->user->can(Privilege::$EDIT_PAGES, 1);
    }
  }
  
}
