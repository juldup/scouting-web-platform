<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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
    $eventList = CalendarItem::where('section_id', '=', $this->user->currentSection->id)
            ->where('start_date', '<=', $years[1] . "-7-31")
            ->where('end_date', '>=', $years[0] . "-8-1")
            ->orderBy('start_date')
            ->get();
    $monitoredEvents = array();
    $unmonitoredEvents = array();
    foreach ($eventList as $event) {
      if ($event->attendance_monitored) {
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
          $status["event_" . $event['id']] = $attendance->attended ? true : false;
        }
      }
      $member['status'] = $status;
      $members[] = $member;
    }
    // Render view
    return View::make('pages.attendance.editAttendance', [
        'year' => $year,
        'canEdit' => $this->user->can(Privilege::$MANAGE_ATTENDANCE),
        'members' => $members,
        'monitoredEvents' => $monitoredEvents,
        'unmonitoredEvents' => $unmonitoredEvents,
        'previousYear' => (substr($year, 0, 4)-1) . "-" . substr($year, 0, 4),
    ]);
  }
  
  /**
   * [Ajax] Updates the attendance status
   */
  public function upload($section_slug, $year) {
    try {
      if (!$this->user->can(Privilege::$MANAGE_ATTENDANCE, $this->user->currentSection)) {
        throw new Exception("Attendance edition unauthorized for this user");
      }
      // Get input
      $data = json_decode(Input::get('data'));
      $events = json_decode(Input::get('events'));
      // Update events with monitoring status
      foreach ($events as $event) {
        $calendarItem = CalendarItem::find($event->id);
        if ($calendarItem) {
          // Make sure the event belongs to the same section, for security reasons
          if ($calendarItem->section_id != $this->user->currentSection->id) {
            throw new Exception("Attendance edition unauthorized for this user: wrong section " . $calendarItem->section_id . " != " . $this->user->currentSection->id);
          }
          // Update event
          $calendarItem->attendance_monitored = $event->monitored;
          $calendarItem->save();
        }
      }
      // Update members' attendance status
      foreach ($data as $memberData) {
        foreach ($events as $event) {
          if ($event->monitored) {
            $propertyName = "event_" . $event->id;
            // Get whether the member attended the event
            $attended = false;
            if ($memberData->status && property_exists($memberData->status, $propertyName)) {
              $attended = $memberData->status->$propertyName;
            }
            // Get attendance object for this member and event
            $attendance = Attendance::where('member_id', '=', $memberData->id)
                    ->where('event_id', '=', $event->id)
                    ->first();
            if (!$attendance) {
              // Create new attendance instance
              $attendance = Attendance::create(array(
                  'member_id' => $memberData->id,
                  'event_id' => $event->id,
                  'attended' => $attended ? true : false,
              ));
            } else {
              // Update existing attendance instance
              if ($attendance->attended != $attended) {
                $attendance->attended = $attended;
                $attendance->save();
              }
            }
          }
        }
      }
      LogEntry::log("Présences", "Liste des présences modifiée");
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
  
}
