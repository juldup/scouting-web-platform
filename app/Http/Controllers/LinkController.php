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
 * Edition of the links that are present in the contact page.
 */
class LinkController extends BaseController {
  
  /**
   * [Route] Displays the link management page
   */
  public function showEdit() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_LINKS)) {
      abort(404);
    }
    // Make sure the current user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get links
    $links = Link::all();
    // Make view
    return View::make('pages.links.editLinks', array(
        'links' => $links,
    ));
  }
  
  /**
   * [Route] Updates or creates a link
   */
  public function submitLink(Request $request) {
    // Make sure the user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get input data
    $linkId = $request->input('link_id');
    $title = $request->input('link_title');
    $url = $request->input('link_url');
    $description = $request->input('link_description');
    // Check that the title and url are not empty
    if (!$title) {
      $success = false;
      $message = "Le titre ne peut pas être vide.";
    } elseif (!$url) {
      $success = false;
      $message = "L'url ne peut pas être vide.";
    } else {
      // Prepend "http://" to the url if missing
      if (strpos($url, "//") === false) {
        $url = "http://" . $url;
      }
      // Update database
      if ($linkId) {
        // Modifying existing link
        $link = Link::find($linkId);
        if ($link) {
          $link->title = $title;
          $link->url = $url;
          $link->description = $description;
          try {
            $link->save();
            $success = true;
            $message = "Le lien a été mis a jour.";
          } catch (Exception $e) {
            Log::error($e);
            $success = false;
            $message = "Une erreur est survenue. Les changements n'ont pas été enregistrés.";
            LogEntry::error("Liens", "Erreur lors de l'enregistrement d'un lien", array("Erreur" => $ex->getMessage()));
          }
        } else {
          $success = false;
          $message = "Une erreur est survenue. Les changements n'ont pas été enregistrés.";
          LogEntry::error("Liens", "Erreur lors de l'enregistrement d'un lien", array("Erreur" => "Le lien $linkId n'existe pas"));
        }
      } else {
        // Creating new link
        try {
          Link::create(array(
              'title' => $title,
              'url' => $url,
              'description' => $description,
          ));
          $success = true;
          $message = "Le lien a été créé.";
        } catch (Exception $ex) {
          Log::error($ex);
          $success = false;
          $message = "Une erreur est survenue. Le lien n'a pas été créé.";
          LogEntry::error("Liens", "Erreur lors de l'enregistrement d'un lien", array("Erreur" => $ex->getMessage()));
        }
      }
    }
    // Redirect with status message
    $redirect = redirect()->route('edit_links')
            ->with($success ? "success_message" : "error_message", $message);
    if ($success) {
      LogEntry::log("Liens", $linkId ? "Modification d'un lien" : "Ajout d'un nouveau lien", array("Title" => $title, "Description" => $description, "URL" => $url)); // TODO improve log message
      return $redirect;
    } else {
      return $redirect->withInput();
    }
  }
  
  /**
   * [Route] Deletes a link
   */
  public function deleteLink($link_id) {
    // Make sure the user can edit links
    if (!$this->user->can(Privilege::$EDIT_PAGES, 1)) {
      return Helper::forbiddenResponse();
    }
    // Delete link
    $link = Link::find($link_id);
    try {
      if (!$link) throw new Exception("Link $link_id does not exist");
      $link->delete();
      LogEntry::log("Liens", "Suppression d'un lien", array("Lien" => $link->title, "Description" => $link->description, "URL" => $link->url));
      return redirect()->route('edit_links')
              ->with('success_message', "Le lien vers " . $link->url . " a été supprimé");
    } catch (Exception $ex) {
      Log::error($ex);
      LogEntry::error("Liens", "Erreur lors de la suppression d'un lien", array("Erreur" => $ex->getMessage()));
      return redirect()->route('edit_links')
              ->with('error_message', "Une erreur est survenue. Le lien n'a pas été supprimé.");
    }
  }
  
}
