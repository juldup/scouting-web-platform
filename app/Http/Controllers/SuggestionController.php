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
 * Visitors can leave suggestions about the website or the unit's activities.
 * 
 * This controller allows visitors to view and post suggestions, and leaders
 * to manage them.
 */
class SuggestionController extends BaseController {
  
  /**
   * [Route] Displays the suggestion page
   * 
   * @param boolean $managing  Whether the page is being shown in management mode
   */
  public function showPage($section_slug = null, $managing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      abort(404);
    }
    // Get list of suggestions
    $suggestions = Suggestion::orderBy('id', 'DESC')->get();
    // Make view
    return View::make('pages.suggestions.suggestions', array(
        'suggestions' => $suggestions,
        'managing' => $managing,
        'can_manage' => $this->user->can(Privilege::$MANAGE_SUGGESIONS, 1),
    ));
  }
  
  /**
   * [Route] Used to submit a new suggestion
   */
  public function submit(Request $request) {
    // Get suggestion text
    $body = $request->input('body');
    if (!$body) return redirect()->route('suggestions');
    // Create suggestion
    Suggestion::create(array(
        'body' => $body,
        'user_id' => $this->user->isConnected() ? $this->user->id : null,
    ));
    // Log
    LogEntry::log("Suggestions", "Ajout d'une suggestion", array("Suggestion" => $body));
    // Redirect back with success message
    return redirect()->route('suggestions')
            ->with('success_message', "Votre suggestion a été enregistrée.");
  }
  
  /**
   * [Route] Shows the suggestion page in management mode
   */
  public function showEdit($section_slug = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_SUGGESTIONS)) {
      abort(404);
    }
    // Make sure the user can manage the suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Show page in management mode
    return $this->showPage($section_slug, true);
  }
  
  /**
   * [Route] Deletes a suggestion
   */
  public function deleteSuggestion($suggestion_id) {
    // Make sure the user can delete suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get suggestion
    $suggestion = Suggestion::find($suggestion_id);
    // Delete suggestion
    if ($suggestion) {
      try {
        $suggestion->delete();
        LogEntry::log("Suggestions", "Suppression d'une suggestion", array("Suggestion" => $suggestion->body));
        return redirect()->route('edit_suggestions')
                ->with('success_message', "La suggestion a été supprimée.");
      } catch (Exception $e) {
        Log::error($e);
        LogEntry::error("Suggestions", "Erreur lors de la suppression d'une suggestion", array("Erreur" => $e->getMessage()));
      }
    }
    return redirect()->route('edit_suggestions')
          ->with('error_message', "La suggestion n'a pas été supprimée.");
  }
  
  /**
   * [Route] Used to submit a response to a suggestion
   */
  public function submitResponse(Request $request, $suggestion_id) {
    // Make sure the user can post responses to suggestions
    if (!$this->user->can(Privilege::$MANAGE_SUGGESIONS, 1)) {
      return Helper::forbiddenResponse();
    }
    // Get suggestion
    $suggestion = Suggestion::find($suggestion_id);
    if ($suggestion) {
      // Save response
      try {
        $response = $request->input("response_$suggestion_id");
        $suggestion->response = $response;
        $suggestion->save();
        LogEntry::log("Suggestions", "Réponse à une suggestion", array("Suggestion" => $suggestion->body, "Réponse" => $suggestion->response));
        return redirect()->route('edit_suggestions')
              ->with('success_message', "La réponse a été enregistrée.");
      } catch (Exception $ex) {
        Log::error($ex);
        LogEntry::error("Suggestions", "Erreur lors de la réponse à une suggestion", array("Erreur" => $ex->getMessage()));
      }
    }
    return redirect()->route('edit_suggestions')
          ->with('error_message', "Une erreur est survenue. La réponse n'a pas été enregistrée.");
  }
  
}
