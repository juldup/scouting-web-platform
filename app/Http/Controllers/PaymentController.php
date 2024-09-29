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
use Exception;
use Illuminate\Support\Facades\Log;


/**
 * The payment management allows leaders to keep track of who has paid for activities, events, etc.
 */
class PaymentController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows the page to edit payment
   */
  public function editPayment($section_slug = null, $year = false) {
    // Init year with default value
    if (!$year) $year = Helper::thisYear();
    // Make sure the user is a leader
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Create list of events
    $eventList = PaymentEvent::where('section_id', '=', $this->user->currentSection->id)
            ->where('year', '=', $year)
            ->orderBy('id')
            ->get();
    $events = array();
    foreach ($eventList as $event) {
      $events[] = array("id" => $event->id, "name" => $event->name);
    }
    // Create list of members
    $memberList = Member::where('validated', '=', 1)
            ->where('section_id', '=', $this->section->id)
            ->orderBy('is_leader', 'ASC')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    $members = array();
    foreach ($memberList as $memberObject) {
      $member = array("id" => $memberObject->id, "name" => $memberObject->last_name . " " . $memberObject->first_name, "isFemale" => $memberObject->gender == 'F');
      // Add payment list
      $status = array();
      foreach ($events as $event) {
        $payment = Payment::where('member_id', '=', $memberObject->id)
                ->where('event_id', '=', $event['id'])
                ->first();
        if ($payment) {
          $status["event_" . $event['id']] = $payment->paid ? true : false;
        } else {
          $status["event_" . $event['id']] = false;
        }
      }
      $member['status'] = $status;
      $members[] = $member;
    }
    // Render view
    return View::make('pages.payment.editPayment', array(
        'year' => $year,
        'canEdit' => $this->user->can(Privilege::$MANAGE_EVENT_PAYMENTS),
        'members' => $members,
        'events' => $events,
        'previousYear' => (substr($year, 0, 4)-1) . "-" . substr($year, 0, 4),
    ));
  }
  
  /**
   * [Ajax] Updates the payment status
   */
  public function upload(Request $request, $section_slug, $year) {
    try {
      if (!$this->user->can(Privilege::$MANAGE_EVENT_PAYMENTS, $this->user->currentSection)) {
        throw new Exception("Payment edition unauthorized for this user");
      }
      // Get input
      $data = json_decode($request->input('data'));
      // Get event list
      $eventList = PaymentEvent::where('section_id', '=', $this->user->currentSection->id)
            ->where('year', '=', $year)
            ->orderBy('id')
            ->get();
      // Update members' payment status
      $changesMade = "";
      foreach ($eventList as $event) {
        $eventChanged = false;
        $paidList = "";
        $unpaidList = "";
        foreach ($data as $memberData) {
          $propertyName = "event_" . $event->id;
          // Get whether the member paid for this event
          $paid = false;
          if ($memberData->status && property_exists($memberData->status, $propertyName)) {
            $paid = $memberData->status->$propertyName;
          }
          // Get payment object for this member and event
          $payment = Payment::where('member_id', '=', $memberData->id)
                  ->where('event_id', '=', $event->id)
                  ->first();
          // Get member
          $member = Member::find($memberData->id);
          if ($member) {
            if (!$payment) {
              // Create new payment instance
              $payment = Payment::create(array(
                  'member_id' => $memberData->id,
                  'event_id' => $event->id,
                  'paid' => $paid ? true : false,
              ));
              if ($paid) {
                $paidList .= ($paidList ? ", " : "") . "<ins>" . $member->getFullName() . "</ins>";
                $unpaidList .= ($unpaidList ? ", " : "") . "<del>" . $member->getFullName() . "</del>";
              }
              $eventChanged = true;
            } else {
              // Update existing payment instance
              if ($payment->paid != $paid) {
                $payment->paid = $paid;
                $payment->save();
                $eventChanged = true;
                if ($paid) {
                  $paidList .= ($paidList ? ", " : "") . "<ins>" . $member->getFullName() . "</ins>";
                  $unpaidList .= ($unpaidList ? ", " : "") . "<del>" . $member->getFullName() . "</del>";
                } else {
                  $paidList .= ($paidList ? ", " : "") . "<del>" . $member->getFullName() . "</del>";
                  $unpaidList .= ($unpaidList ? ", " : "") . "<ins>" . $member->getFullName() . "</ins>";
                }
              }
            }
          }
        }
        if ($eventChanged) {
          $changesMade .= "- Modification de <strong>" . $event->name . " (" . $event->year . ")</strong><br />" . 
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Payé&nbsp;: " . $paidList . "<br />" .
                  "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Non payé&nbsp;: " . $unpaidList . "<br />";
        }
      }
      if ($changesMade) {
        LogEntry::log("Paiements", "Liste des paiements modifiée", ["Changements" => $changesMade], true);
      }
      // Return response
      return json_encode(array(
          "result" => "Success",
      ));
    } catch (Exception $e) {
      // An error has occurred
      Log::error($e);
      return json_encode(array(
          "result" => "Failure",
      ));
    }
  }
  
  /**
   * [Ajax] Adds a new event
   */
  public function addNewEvent(Request $request, $section_slug, $year) {
    // Check authorization
    if (!$this->user->can(Privilege::$MANAGE_EVENT_PAYMENTS, $this->user->currentSection)) {
      return response()->json(array(
          "errorMessage" => "Vous n'avez pas le droit d'ajouter une activité.",
      ), 401);
    }
    // Check input data
    $newEventName = trim($request->input('name'));
    if (!$newEventName) {
      return response()->json(array(
          "errorMessage" => "Le nom de l'activité ne peut être vide.",
      ), 400);
    }
    if (!$year) {
      return response()->json(array(
          "errorMessage" => "Une erreur est survenue.",
      ), 400);
    }
    // Check that the event does not exist
    $existingEvent = PaymentEvent::where('section_id', '=', $this->user->currentSection->id)
            ->where('year', '=', $year)
            ->where('name', '=', $newEventName)
            ->first();
    if ($existingEvent) {
      return response()->json(array(
          "errorMessage" => "Cette activité existe déjà.",
      ), 400);
    }
    // Create event
    $event = PaymentEvent::create(array(
        'name' => $newEventName,
        'section_id' => $this->user->currentSection->id,
        'year' => $year,
    ));
    LogEntry::log("Paiements", "Liste des paiements modifiée",
            ["Changements" => "- Ajout de l'activité <strong><ins>" . $event->name . " (" . $year . ")</ins></strong>"], true);
    return response()->json(array(
        'id' => $event->id,
    ), 200);
  }
  
  /**
   * [Ajax] Deletes an event
   */
  public function deleteEvent(Request $request, $section_slug, $year) {
    // Check authorization
    if (!$this->user->can(Privilege::$MANAGE_EVENT_PAYMENTS, $this->user->currentSection)) {
      return response()->json(array(
          "errorMessage" => "Vous n'avez pas le droit d'ajouter une activité.",
      ), 401);
    }
    // Check input data
    $eventId = trim($request->input('eventId'));
    // Get event
    $event = PaymentEvent::where('section_id', '=', $this->user->currentSection->id)
            ->where('year', '=', $year)
            ->where('id', '=', $eventId)
            ->first();
    if (!$event) {
      return response()->json(array(
          "errorMessage" => "Cette activité n'existe plus.",
      ), 400);
    }
    // Delete event
    $event->delete();
    LogEntry::log("Paiements", "Liste des paiements modifiée",
            ["Changements" => "- Suppression de l'activité <strong><del>" . $event->name . " (" . $year . ")</del></strong>"], true);
    return response()->json(array(), 200);
  }
  
}
