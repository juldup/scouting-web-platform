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
 * The leader corner provides a list of all the possible management actions
 * for the leaders with a short description.
 */
class LeaderCornerController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Displays the leader corner page
   */
  public function showPage() {
    // Make sure the user is a leader
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // List operations of the leaders
    $operations = array(
        "Opérations courantes" => array(
            "Calendrier" => array(
                'url' => $this->user->can(Privilege::$EDIT_CALENDAR) ? URL::route('manage_calendar') : null,
                'help-anchor' => 'calendrier',
                'help' => 'edit-calendar',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
            "Présences" => array(
                'url' => URL::route('edit_attendance'),
                'help-anchor' => 'presences',
                'help' => 'edit-attendance',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
            "Photos" => array(
                'url' => $this->user->can(Privilege::$POST_PHOTOS) ? URL::route('edit_photos') : null,
                'help-anchor' => 'photos',
                'help' => 'edit-photos',
                'condition' => Parameter::$SHOW_PHOTOS,
            ),
            "Documents à télécharger" => array(
                'url' => $this->user->can(Privilege::$EDIT_DOCUMENTS) ? URL::route('manage_documents') : null,
                'help-anchor' => 'documents',
                'help' => 'edit-documents',
                'condition' => Parameter::$SHOW_DOCUMENTS,
            ),
            "Actualités" => array(
                'url' => $this->user->can(Privilege::$EDIT_NEWS) ? URL::route('manage_news') : null,
                'help-anchor' => 'actualites',
                'help' => 'edit-news',
                'condition' => Parameter::$SHOW_NEWS,
            ),
            "E-mail aux parents" => array(
                'url' => $this->user->can(Privilege::$SEND_EMAILS) ? URL::route('send_section_email') : null,
                'help-anchor' => 'emails',
                'help' => 'email-section',
            ),
            "Fiches santé" => array(
                'url' => $this->user->can(Privilege::$VIEW_HEALTH_CARDS) ? URL::route('manage_health_cards') : null,
                'help-anchor' => 'fiches-sante',
                'help' => 'edit-health-cards',
                'condition' => Parameter::$SHOW_HEALTH_CARDS,
            ),
            "Trésorerie" => array(
                'url' => URL::route('accounting'),
                'help-anchor' => 'tresorerie',
                'help' => 'accounting',
            ),
            "Paiements" => array(
                'url' => URL::route('edit_payment'),
                'help-anchor' => 'paiements',
                'help' => 'edit-payment',
                'condition' => Parameter::$SHOW_CALENDAR,
            ),
        ),
        "Opérations annuelles" => array(
            "Inscriptions" => array(
                'url' => $this->user->can(Privilege::$EDIT_LISTING_ALL) || $this->user->can(Privilege::$EDIT_LISTING_LIMITED) ||
                         $this->user->can(Privilege::$SECTION_TRANSFER || $this->user->can(Privilege::$MANAGE_ACCOUNTING)) ? URL::route('manage_registration') : null,
                'help-anchor' => 'inscriptions',
                'help' => 'manage-registration',
            ),
            "Listing" => array(
                'url' => $this->user->can(Privilege::$EDIT_LISTING_ALL) || $this->user->can(Privilege::$EDIT_LISTING_LIMITED) ? URL::route('manage_listing') : null,
                'help-anchor' => 'listing',
                'help' => 'edit-listing',
            ),
            "Listing Desk" => array(
                'url' => $this->user->can(Privilege::$EDIT_LISTING_ALL) || $this->user->can(Privilege::$EDIT_LISTING_LIMITED) ? URL::route('desk_listing') : null,
                'help-anchor' => 'listing-desk',
                'help' => 'desk-listing',
            ),
            "Les animateurs" => array(
                'url' => URL::route('edit_leaders'),
                'help-anchor' => 'animateurs',
                'help' => 'edit-leaders',
            ),
            "Les anciens animateurs" => array(
                'url' => URL::route('edit_archived_leaders', $this->section->slug),
                'help-anchor' => 'anciens-animateurs',
                'help' => 'edit-archived-leaders',
            ),
            "Gérer les sections" => array(
                'url' => URL::route('section_data'),
                'help-anchor' => 'sections',
                'help' => 'manage-sections',
            ),
        ),
        "Contenu du site" => array(
            "Pages du site" => array(
                'url' => URL::route('edit_pages'),
                'help-anchor' => 'pages',
                'help' => 'edit-pages',
                'condition' => Parameter::$SHOW_LINKS,
            ),
            "Liens utiles" => array(
                'url' => $this->user->can(Privilege::$EDIT_PAGES, 1) ? URL::route('edit_links') : null,
                'help-anchor' => 'liens',
                'help' => 'edit-links',
                'condition' => Parameter::$SHOW_LINKS,
            ),
            "Paramètres du site" => array(
                'url' => $this->user->can(Privilege::$EDIT_GLOBAL_PARAMETERS) ? URL::route('edit_parameters') : null,
                'help-anchor' => 'parametres',
                'help' => 'edit-parameters',
            ),
            "Style du site" => array(
                'url' => $this->user->can(Privilege::$EDIT_STYLE) ? URL::route('edit_css') : null,
                'help-anchor' => 'style',
                'help' => 'edit-style',
            ),
        ),
        "Supervision" => array(
//            "Changements récents" => array(
//                'url' => URL::route('view_private_recent_changes'),
//                'help-anchor' => 'changements-recents',
//                'help' => 'recent-changes',
//            ),
            "Liste des utilisateurs" => array(
                'url' => URL::route('user_list'),
                'help-anchor' => 'liste-membres',
                'help' => 'user-list',
            ),
            "Gérer les suggestions" => array(
                'url' => $this->user->can(Privilege::$MANAGE_SUGGESIONS) ? URL::route('edit_suggestions') : null,
                'help-anchor' => 'suggestions',
                'help' => 'suggestions',
                'condition' => Parameter::$SHOW_SUGGESTIONS,
            ),
            "Gérer le livre d'or" => array(
                'url' => $this->user->can(Privilege::$DELETE_GUEST_BOOK_ENTRIES) ? URL::route('edit_guest_book') : null,
                'help-anchor' => 'livre-d-or',
                'help' => 'guest-book',
                'condition' => Parameter::$SHOW_GUEST_BOOK,
            ),
            (MonitoringController::cronTaskTimedOut() ? "<span class='danger'><span class='glyphicon glyphicon-warning-sign'></span></span> " : "") . "Tâches cron" => array(
                'url' => URL::route('monitoring'),
                'help-anchor' => 'supervision-taches',
                'help' => 'monitoring',
            ),
            "Logs" => array(
                'url' => URL::route('logs'),
                'help-anchor' => 'logs',
                'help' => 'logs',
            ),
        )
    );
    // Remove disabled operations
    foreach ($operations as $category=>$ops) {
      foreach ($ops as $operation=>$operationData) {
        if (array_key_exists('condition', $operationData) && !Parameter::get($operationData['condition'])) {
          unset($ops[$operation]);
          $operations[$category] = $ops;
        }
      }
    }
    // Create help section list
    $helpSections = array('general');
    foreach ($operations as $ops) {
      foreach ($ops as $operationData) {
        if (!in_array($operationData['help'], $helpSections)) {
          $helpSections[$operationData['help-anchor']] = $operationData['help'];
        }
      }
    }
    // Make view
    return View::make('pages.leaderCorner.leaderCorner', array(
        'operations' => $operations,
        'help_sections' => $helpSections,
    ));
  }
  
}
