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
 * The Attendance management allows leaders to keep track of who was present/absent
 * during the activities
 */
class AttendanceController extends BaseController {
  
  protected $pagesAdaptToSections = true;
  
  /**
   * [Route] Shows the page to edit attendance
   */
  public function editAttendance($section_slug = null, $year = false) {
    // Init year with default value
    if (!$year) $year = Helper::thisYear();
    // Make sure the user is a leader
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Create list of events
    $years = explode('-', $year);
    $eventList = CalendarItem::whereIn('section_id', array($this->user->currentSection->id, 1))
            ->where('start_date', '<=', $years[1] . "-7-31")
            ->where('end_date', '>=', $years[0] . "-8-1")
            ->orderBy('start_date')
            ->get();
    // Sort events by monitored/unmonitored
    $monitoredEvents = array();
    $unmonitoredEvents = array();
    foreach ($eventList as $event) {
//      $first = Attendance::where('section_id', '=', $this->user->currentSection->id)
//              ->where('event_id', '=', $event->id)
//              ->first();
      $first = Attendance::where('event_id', '=', $event->id)
              ->first();
      if ($first) {
        $monitoredEvents[] = array("id" => $event->id, "date" => $event->start_date, "title" => $event->event);
      } else {
        $unmonitoredEvents[] = array("id" => $event->id, "date" => $event->start_date, "title" => $event->event);
      }
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
      // Add attendance list
      $status = array();
      foreach ($monitoredEvents as $event) {
        $attendance = Attendance::where('member_id', '=', $memberObject->id)
                ->where('event_id', '=', $event['id'])
                ->first();
        if ($attendance) {
          $status["event_" . $event['id']] = $attendance->attended;
        } else {
          $status["event_" . $event['id']] = 0;
        }
      }
      $member['status'] = $status;
      $members[] = $member;
    }
    // Render view
    return View::make('pages.attendance.editAttendance', array(
        'year' => $year,
        'canEdit' => $this->user->can(Privilege::$MANAGE_ATTENDANCE),
        'members' => $members,
        'monitoredEvents' => $monitoredEvents,
        'unmonitoredEvents' => $unmonitoredEvents,
        'previousYear' => (substr($year, 0, 4)-1) . "-" . substr($year, 0, 4),
    ));
  }
  
  /**
   * [Ajax] Updates the attendance status
   */
  public function upload(Request $request, $section_slug, $year) {
    try {
      if (!$this->user->can(Privilege::$MANAGE_ATTENDANCE, $this->user->currentSection)) {
        throw new Exception("Attendance edition unauthorized for this user");
      }
      // Get input
      $data = json_decode($request->input('data'));
      $events = json_decode($request->input('events'));
      // Update members' attendance status
      $changesMade = "";
      $newExcused = [];
      foreach ($events as $event) {
        $calendarEvent = CalendarItem::find($event->id);
        if ($event->monitored) {
          $changeStrings = [0 => "", 1 => "", 2 => ""];
          $eventChanged = false;
          $isNewEvent = true;
          foreach ($data as $memberData) {
            $propertyName = "event_" . $event->id;
            // Get whether the member attended the event
            $attended = 0;
            if ($memberData->status && property_exists($memberData->status, $propertyName)) {
              $attended = $memberData->status->$propertyName;
            }
            // Get attendance object for this member and event
            $attendance = Attendance::where('member_id', '=', $memberData->id)
                    ->where('event_id', '=', $event->id)
                    //->where('section_id', '=', $this->user->currentSection->id)
                    ->first();
            // Get member
            $member = Member::find($memberData->id);
            if ($member) {
              if (!$attendance) {
                // Check if the absence has not been notified by the parents
                if ($attended == 0) {
                  $absence = Absence::where('event_id', '=', $event->id)
                          ->where('member_id', '=', $memberData->id)
                          ->first();
                  // Set status as 'excused'
                  if ($absence) {
                    $attended = 2;
                    $newExcused[] = $event->id . ":" . $memberData->id;
                  }
                }
                // Create new attendance instance
                $attendance = Attendance::create(array(
                    'member_id' => $memberData->id,
                    'event_id' => $event->id,
                    //'section_id' => $this->user->currentSection->id,
                    'attended' => $attended,
                ));
                if ($attended != 0) {
                  $eventChanged = true;
                  $changeStrings[0] .= ($changeStrings[0] ? ", " : "") . "<del>" . $member->getFullName() . "</del>";
                  $changeStrings[$attended] .= ($changeStrings[$attended] ? ", " : "") . "<ins>" . $member->getFullName() . "</ins>";
                }
              } else {
                // Update existing attendance instance
                if ($attendance->attended != $attended) {
                  $eventChanged = true;
                  $changeStrings[$attendance->attended] .= ($changeStrings[$attendance->attended] ? ", " : "") . "<del>" . $member->getFullName() . "</del>";
                  $changeStrings[$attended] .= ($changeStrings[$attended] ? ", " : "") . "<ins>" . $member->getFullName() . "</ins>";
                  $attendance->attended = $attended;
                  $attendance->save();
                }
                $isNewEvent = false;
              }
            }
          }
          // Update change list
          if ($isNewEvent) {
            $changesMade .= "- Ajout de l'événement <strong><ins>" . $calendarEvent->stringRepresentation() . "</ins></strong><br />";
          } elseif ($eventChanged) {
            $changesMade .= "- Modification des présences de l'événement <strong>" . $calendarEvent->stringRepresentation() . "</strong><br />";
            if ($changeStrings[1]) $changesMade .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Présent&nbsp;: " . $changeStrings[1] . "<br />";
            if ($changeStrings[2]) $changesMade .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Excusé&nbsp;: " . $changeStrings[2] . "<br />";
            if ($changeStrings[0]) $changesMade .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Absent&nbsp;: " . $changeStrings[0] . "<br />";
          }
        } else {
          // Remove all corresponding attendance entries
          $deletedRows = Attendance::where('event_id', '=', $event->id)
                  //->where('section_id', '=', $this->user->currentSection->id)
                  ->delete();
          if ($deletedRows) {
            $changesMade .= "- Suppression de l'événement <strong><del>" . $calendarEvent->stringRepresentation() . "</del></strong><br />";
          }
        }
      }
      if ($changesMade) {
        LogEntry::log("Présences", "Liste des présences modifiée", ["Changements" => $changesMade], true);
      }
      // Return response
      return json_encode(array(
          "result" => "Success",
          "newExcused" => $newExcused,
      ));
    } catch (Exception $e) {
      // An error has occurred
      Log::error($e);
      return json_encode(array(
          "result" => "Failure",
      ));
    }
  }
  
}
