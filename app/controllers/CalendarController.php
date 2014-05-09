<?php

/**
 * The calendar shows the unit's event (meeting, etc.) to the visitors.
 * It has public access.
 * 
 * This controller generates the calendar page and allows the leaders
 * to edit the calendar's events.
 */
class CalendarController extends BaseController {
  
  /**
   * [Route] Shows the public calendar page
   */
  public function showPage($year = null, $month = null) {
    return $this->showCalendar($year, $month, false);
  }
  
  /**
   * Shows the calendar page in public mode or in edit mode
   * 
   * @param string $year  The year to show
   * @param string $month  The month to show
   * @param boolean $editing  True if edit mode
   */
  private function showCalendar($year = null, $month = null, $editing = false) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_CALENDAR)) {
      return App::abort(404);
    }
    // Select default year
    if ($year == null || $month == null) {
      $year = date('Y');
      $month = date('m');
    }
    // Date of today within the month (1-31)
    $today = date('j');
    // Shift to make week start on Monday
    $day_offset = 1;
    // Name of the days
    $days = array("Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
    // Name of the months
    $months = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
    // Short names of the months
    $months_short = array("Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc");
    // Number of days in the month
    $days_in_month = date("t", strtotime("$year-$month-1"));
    // Day (0=Sun, 6=Sat) of the first of the month
    $start_day_number = date("w", strtotime("$year-$month-1"));
    // Day (0=Sun, 6=Sat) of the last of the month
    $end_day_number = date("w", strtotime("$year-$month-$days_in_month"));
    // Number of days in the previous month
    $nbDaysInPrevMonth = date("t", strtotime("$year-$month-1") - 48 * 3600);
    // Number of days of the week before the first of the month
    $blank_days_before = ($start_day_number - $day_offset + 7) % 7;
    // Number of days of the week after the last of the month
    $blank_days_after = (6 - $end_day_number + $day_offset) % 7;
    // Select calendar items
    if ($this->user->isLeader()) {
      // For leaders, also show the private events
      $query = CalendarItem::where('start_date', '<=', "$year-$month-$days_in_month")
              ->where('end_date', '>=', "$year-$month-1");
    } else {
      // For visitors, only show the public events
      $query = CalendarItem::visibleToAllMembers()
              ->where('start_date', '<=', "$year-$month-$days_in_month")
              ->where('end_date', '>=', "$year-$month-1");
    }
    // Filter by the current section
    if ($this->section->id != 1) {
      $query = $query->where('section_id', '=', $this->section->id);
    }
    // Get items
    $calendarItems = $query->get();
    // Generate event list per day of the month
    $events = array();
    for ($day = 1; $day <= $days_in_month; $day++) {
      $events[$day] = array();
    }
    foreach ($calendarItems as $item) {
      $itemStartDate = explode('-', $item->start_date);
      $startDay = ($itemStartDate[0] == $year && $itemStartDate[1] == $month) ? $itemStartDate[2] : 1;
      $itemEndDate = explode('-', $item->end_date);
      $endDay = ($itemEndDate[0] == $year && $itemEndDate[1] == $month) ? $itemEndDate[2] : $days_in_month;
      for ($day = $startDay + 0; $day <= $endDay; $day++) {
        $events[$day][] = $item;
      }
    }
    // Get section list for section selection
    $sections = array();
    if ($editing) {
      $sections = Section::getSectionsForSelect();
    }
    // Event type list for select
    $eventTypes = CalendarItem::$eventTypes;
    // Return view
    return View::make('pages.calendar.calendar', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_CALENDAR),
        'edit_url' => URL::route('manage_calendar_month', array('year' => $year, 'month' => $month, 'section_slug' => $this->section->slug)),
        'page_url' => URL::route('calendar_month', array('year' => $year, 'month' => $month, 'section_slug' => $this->section->slug)),
        'route_month' => $editing ? 'manage_calendar_month' : 'calendar_month',
        'editing' => $editing,
        'blank_days_before' => $blank_days_before,
        'blank_days_after' => $blank_days_after,
        'days_in_month' => $days_in_month,
        'days' => $days,
        'months' => $months,
        'months_short' => $months_short,
        'month' => $month,
        'year' => $year,
        'events' => $events,
        'today_day' => date('d'),
        'today_month' => date('m'),
        'today_year' => date('Y'),
        'sections' => $sections,
        'sectionList' => Section::where('id', '!=', 1)->get(),
        'event_types' => $eventTypes,
        'calendar_items' => $calendarItems,
        'include_second_semester_by_default' => date('m') <= 7,
    ));
  }
  
  /**
   * [Route] Downloads the calendar in PDF format
   */
  public function downloadCalendar() {
    // Get semester(s)
    $firstSemester = Input::has('semester_1');
    $secondSemester = Input::has('semester_2');
    if (!$firstSemester && !$secondSemester) {
      return Redirect::route('calendar')->with('error_message', "Vous n'avez sélectionné aucun semestre.");
    }
    // Get section(s)
    $sections = array();
    foreach (Section::orderBy('position')->get() as $section) {
      if (Input::has("section_" . $section->id)) {
        $sections[] = $section;
      }
    }
    // Generate calendar
    CalendarPDF::downloadCalendarFor($sections, $firstSemester, $secondSemester);
  }
  
  /**
   * [Route] Shows the calendar edition page (leaders only)
   */
  public function showEdit($year = null, $month = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_CALENDAR)) {
      return App::abort(404);
    }
    // Make sure the user has access the calendar edition mode for this section
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    // Show calendar
    return $this->showCalendar($year, $month, true);
  }
  
  /**
   * Updates or creates a calendar event in the database
   */
  public function submitItem($year, $month, $section_slug) {
    // Get input data
    $eventId = Input::get('event_id');
    $startDateTimestamp = strtotime(Input::get('start_date_year') . "-" . Input::get('start_date_month') . "-" . Input::get('start_date_day'));
    $startDate = date('Y-m-d', $startDateTimestamp);
    $duration = Input::get('duration_in_days');
    $endDate = date('Y-m-d', $startDateTimestamp + 3600 * 24 * ($duration-1) + 2 * 3600);
    $eventName = Input::get('event_name');
    $description = Input::get('description');
    $eventType = Input::get('event_type');
    $sectionId = Input::get('section');
    // Mark sure the user can edit the calendar for this section
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    // Set default event name if the event name is missing
    if (!$eventName) {
      $eventName = CalendarItem::$eventTypes[$eventType];
    }
    // Make some basic tests on the input
    $success = false;
    if (date('Y', $startDateTimestamp) != Input::get('start_date_year') ||
            date('m', $startDateTimestamp) != Input::get('start_date_month') ||
            date('d', $startDateTimestamp) != Input::get('start_date_day')) {
      // Wrong start date
      $success = false;
      $message = "L'événement n'a pas été enregistré : la date de début n'est pas une date correcte.";
    } elseif (!is_numeric ($duration) || $duration <= 0) {
      // Wrong duration
      $success = false;
      $message = "La durée n'est pas valide. Elle doit être au minimum <strong>1</strong>.";
    } else {
      // Tests passed
      if ($eventId) {
        // The event already exists, update it
        $calendarItem = CalendarItem::find($eventId);
        if ($calendarItem) {
          $calendarItem->start_date = $startDate;
          $calendarItem->end_date = $endDate;
          $calendarItem->event = $eventName;
          $calendarItem->description = $description;
          $calendarItem->type = $eventType;
          $calendarItem->section_id = $sectionId;
          try {
            $calendarItem->save();
            $success = true;
            $message = "L'événement a été mis à jour.";
          } catch (Illuminate\Database\QueryException $e) {
            $success = false;
            $message = "Une erreur s'est produite. L'événement n'a pas été enregistré.";
          }
        } else {
          $success = false;
          $message = "Une erreur s'est produite. L'événement n'a pas été enregistré.";
        }
      } else {
        // Creating a new event
        try {
          CalendarItem::create(array(
              'start_date' => $startDate,
              'end_date' => $endDate,
              'event' => $eventName,
              'description' => $description,
              'type' => $eventType,
              'section_id' => $sectionId,
          ));
          $success = true;
          $message = "L'événement a été créé.";
        } catch (Illuminate\Database\QueryException $e) {
          $success = false;
          $message = "Une erreur s'est produite. L'événement n'a pas été enregistré.";
        }
      }
    }
    // Redirect back to calendar edition page
    $redirect = Redirect::route('manage_calendar_month', array(
        "year" => $year,
        "month" => $month,
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    if ($success) return $redirect;
    else return $redirect->withInput();
  }
  
  /**
   * Deletes an event from the calendar
   * 
   * @param string $event_id  The id of the event to delete
   */
  public function deleteItem($year, $month, $section_slug, $event_id) {
    // Get calendar event
    $calendarItem = CalendarItem::find($event_id);
    // Check that the event exists
    if (!$calendarItem) {
      throw new NotFoundException("Cet événement n'existe pas");
    }
    // Make sure the user can delete an event from this section
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $calendarItem->section_id)) {
      return Helper::forbiddenResponse();
    }
    // Delete event
    try {
      $calendarItem->delete();
      $success = true;
      $message = "L'événement a été supprimé.";
    } catch (Illuminate\Database\QueryException $e) {
      $success = false;
      $message = "Une erreur s'est produite. L'événement n'a pas été supprimé.";
    }
    // Redirect back to calendar edition page
    return Redirect::route('manage_calendar_month', array(
        "year" => $year,
        "month" => $month,
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
  }
  
}
