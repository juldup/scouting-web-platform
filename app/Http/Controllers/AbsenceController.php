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
use Illuminate\Support\Facades\DB;

/**
 * Parents can fill in a form to inform the leaders of an absence. This controller
 * provides the tools to submit a form and view the list of absent scouts.
 */
class AbsenceController extends BaseController {
  
  protected function currentPageAdaptToSections() {
    return $this->user->isLeader();
  }
  
  /**
   * [Route] Shows the absence page
   */
  public function showPage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_ABSENCES)) {
      abort(404);
    }
    // Get list of members owned by the current user
    $ownedMembers = $this->user->getAssociatedMembers();
    // Gather calendar information for each member
    $members = [];
    foreach ($ownedMembers as $member) {
      // Select all future events for this member
      $today = "2020-04-19";
      $calendarItems = CalendarItem::visibleToAllMembers()
            ->where('start_date', '>=', $today)
            ->where(function($query) use ($member) {
              $query->where('section_id', '=', 1)
                    ->orWhere('section_id', '=', $member->getSection()->id);
            })->orderBy('start_date','ASC')->get();
      // Create event list
      $events = [];
      foreach ($calendarItems as $calendarItem) {
        $eventDate = Helper::dateToHuman($calendarItem->start_date);
        if ($calendarItem->end_date != $calendarItem->start_date) {
          $eventDate .= " au " . Helper::dateToHuman($calendarItem->end_date);
        }
        $events[$calendarItem->id] = $calendarItem->event . " du " . $eventDate
                  . " (" . $calendarItem->getSection()->name . ")";
      }
      $events[0] = "Autre activité";
      // Add member to member list
      $members[] = array(
          'id' => $member->id,
          'full_name' => $member->getFullName(),
          'events' => $events,
      );
    }
    // Make view
    return View::make('pages.absences.absences', array(
        'members' => $members,
        'can_manage' => $this->user->isLeader(),
    ));
  }
  
  /**
   * [Route] Submits an absence
   */
  public function submit(Request $request) {
    // Get the member id
    $memberId = $request->input('member_id');
    // Make sure the current user owns this member
    if (!$this->user->isOwnerOfMember($memberId)) {
      return Helper::forbiddenResponse();
    }
    // Get all input
    $eventId = $request->input('event_id' . $memberId);
    $otherEvent = $request->input('other_event'. $memberId);
    $explanation = $request->input('explanation'. $memberId);
    // Save the absence
    try {
      Absence::create(array(
          'member_id' => $memberId,
          'event_id' => $eventId != 0 ? $eventId : null,
          'other_event' => $eventId == 0 ? $otherEvent : null,
          'explanation' => $explanation,
      ));
      // Get member
      $member = Member::find($memberId);
      // Get name and date of event
      if ($eventId) {
        $calendarItem = CalendarItem::find($eventId);
        $eventDate = Helper::dateToHuman($calendarItem->start_date);
        if ($calendarItem->end_date != $calendarItem->start_date) {
          $eventDate .= " au " . Helper::dateToHuman($calendarItem->end_date);
        }
        $eventName = $calendarItem->event . " du " . $eventDate
                . " (" . $calendarItem->getSection()->name . ")";
      } else {
        $eventName = $otherEvent;
      }
      // Send e-mail to leaders
      $leaderForEmail = Member::where('section_id', '=', $member->section_id)
              ->where('is_leader', '=', true)
              ->where('receive_absence_emails', '=', true)
              ->get();
      foreach ($leaderForEmail as $leader) {
        $recipient = $leader->email_member;
        $emailContent = Helper::renderEmail('absenceNotified', $recipient, array(
            'member' => $member,
            'event' => $eventName,
            'explanation' => $explanation,
        ));
        $email = PendingEmail::create(array(
            'subject' => "Absence de " . $member->getFullName(),
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
            'recipient' => $recipient,
            'priority' => PendingEmail::$ABSENCE_EMAIL_PRIORITY,
        ));
      }
      // Send confirmation e-mail to user
      $recipient = $this->user->email;
      $emailContent = Helper::renderEmail('absenceConfirmation', $recipient, array(
          'member' => $member,
          'event' => $eventName,
          'explanation' => $explanation,
      ));
      $email = PendingEmail::create(array(
          'subject' => "Absence de " . $member->getFullName(),
          'raw_body' => $emailContent['txt'],
          'html_body' => $emailContent['html'],
          'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
          'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
          'recipient' => $recipient,
          'priority' => PendingEmail::$ABSENCE_EMAIL_PRIORITY,
      ));
      // Log
      LogEntry::log("Absence", "Absence à un évenement signalée", array(
          "Membre" => $member->getFullName(),
          "Activité" => $eventName,
          "Justification" => $explanation,
      ));
      // Success
      return redirect(URL::route('absences'))->with('success_message', "L'absence a été signalée. Merci.");
    } catch(Exception $e) {
      // Log error
      LogEntry::error("Absences", "Erreur lors de l'enregistrement d'une absence", array('Erreur' => $e->getMessage()));
      // Redirect with status message
      return redirect(URL::route('absences'))->with('error_message', "Une erreur inconnue s'est produite. Veuillez réessayer ou contacter directement les animateurs.");
    }
  }
  
  /**
   * [Route] Shows the absence management page for leaders
   */
  public function showManage() {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_ABSENCES)) {
      abort(404);
    }
    // Make sure the user has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get all events for this section
    $sectionId = $this->user->currentSection->id;
    if ($sectionId == 1) {
      $calendarItems = CalendarItem::where('start_date', '>=', Helper::startOfThisYear())
              ->where('section_id', '=', 1)
              ->get();
    } else {
      $calendarItems = CalendarItem::where('start_date', '>=', Helper::startOfThisYear())
              ->where('section_id', '=', $sectionId)
              ->orWhere('section_id', '=', 1)
              ->get();
    }
    // Get list of absences for each event
    $events = [];
    if ($sectionId == 1) {
      foreach ($calendarItems as $calendarItem) {
        $events[$calendarItem->id] = Absence::where('event_id', '=', $calendarItem->id)->get();
        $otherAbsences = [];
      }
    } else {
      foreach ($calendarItems as $calendarItem) {
        $events[$calendarItem->id] = Absence::where('event_id', '=', $calendarItem->id)
                ->whereExists(function($query) use ($sectionId) {
                       $query->select(DB::raw(1))
                      ->from('members')
                      ->where('members.section_id', '=', $sectionId)
                      ->whereRaw('members.id = absences.member_id');
                })->get();
      }
      $otherAbsences = Absence::where('event_id', '=', null)
              ->where('created_at', '>=', Helper::startOfThisYear())
              ->whereExists(function($query) use ($sectionId) {
                     $query->select(DB::raw(1))
                    ->from('members')
                    ->where('members.section_id', '=', $sectionId)
                    ->whereRaw('members.id = absences.member_id');
              })->get();
    }
    if (count($otherAbsences)) {
      $events[0] = $otherAbsences;
    }
    // Make view
    return View::make('pages.absences.manageAbsences', array(
        'events' => $events,
        'associated_leaders' => $this->user->getAssociatedLeaderMembers(),
    ));
  }
  
  /**
   * [Route] Change the leader's preference to receive automatic e-mails when an
   * absence is submitted
   */
  public function registerToAbsenceEmails($member_id) {
    // Make sure the user has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    if ($this->user->isOwnerOfMember($member_id)) {
      $member = Member::find($member_id);
      $member->receive_absence_emails = true;
      $member->save();
    }
    return redirect(URL::route('manage_absences'));
  }
  
  /**
   * [Route] Change the leader's preference to stop receiving automatic e-mails when an
   * absence is submitted
   */
  public function unregisterFromAbsenceEmails($member_id) {
    // Make sure the user has access to this page
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    if ($this->user->isOwnerOfMember($member_id)) {
      $member = Member::find($member_id);
      $member->receive_absence_emails = false;
      $member->save();
    }
    return redirect(URL::route('manage_absences'));
  }
  
}
