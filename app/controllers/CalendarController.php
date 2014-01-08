<?php

class CalendarController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    return $this->showCalendar($year, $month, false);
  }
  
  private function showCalendar($year = null, $month = null, $editing = false) {
    
    // TODO Display birthdays
    
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
    
    $query = CalendarItem::where('start_date', '<=', "$year-$month-$days_in_month")
            ->where('end_date', '>=', "$year-$month-1");
    if ($this->section->id != 1) {
      $query = $query->where('section_id', '=', $this->section->id);
    }
    $calendarItems = $query->get();
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
        'event_types' => $eventTypes,
        'calendar_items' => $calendarItems,
    ));
  }
  
  public function showEdit($year = null, $month = null) {
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $this->user->currentSection)) {
      return Helper::forbiddenResponse();
    }
    
    return $this->showCalendar($year, $month, true);
  }
  
  public function submitItem($year, $month, $section_slug) {
    
    $eventId = Input::get('event_id');
    $startDateTimestamp = strtotime(Input::get('start_date_year') . "-" . Input::get('start_date_month') . "-" . Input::get('start_date_day'));
    $startDate = date('Y-m-d', $startDateTimestamp);
    $duration = Input::get('duration_in_days');
    $endDate = date('Y-m-d', $startDateTimestamp + 3600 * 24 * ($duration-1) + 2 * 3600);
    $eventName = Input::get('event_name');
    $description = Input::get('description');
    $eventType = Input::get('event_type');
    $sectionId = Input::get('section');
    
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $sectionId)) {
      return Helper::forbiddenResponse();
    }
    
    if (!$eventName) {
      $eventName = CalendarItem::$eventTypes[$eventType];
    }
    
    $success = false;
    if (date('Y', $startDateTimestamp) != Input::get('start_date_year') ||
            date('m', $startDateTimestamp) != Input::get('start_date_month') ||
            date('d', $startDateTimestamp) != Input::get('start_date_day')) {
      $success = false;
      $message = "L'événement n'a pas été enregistré : la date de début n'est pas une date correcte.";
    } elseif (!is_numeric ($duration) || $duration <= 0) {
      $success = false;
      $message = "La durée n'est pas valide. Elle doit être au minimum <strong>1</strong>.";
    } else {
      if ($eventId) {
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
    
    $redirect = Redirect::route('manage_calendar_month', array(
        "year" => $year,
        "month" => $month,
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    
    if ($success) return $redirect;
    else return $redirect->withInput();
    
  }
  
  public function deleteItem($year, $month, $section_slug, $event_id) {
    
    $calendarItem = CalendarItem::find($event_id);
    
    if (!$calendarItem) {
      throw new NotFoundException("Cet événement n'existe pas");
    }
    
    if (!$this->user->can(Privilege::$EDIT_CALENDAR, $calendarItem->section_id)) {
      return Helper::forbiddenResponse();
    }
    
    try {
      $calendarItem->delete();
      $success = true;
      $message = "L'événement a été supprimé.";
    } catch (Illuminate\Database\QueryException $e) {
      $success = false;
      $message = "Une erreur s'est produite. L'événement n'a pas été supprimé.";
    }
    
    return Redirect::route('manage_calendar_month', array(
        "year" => $year,
        "month" => $month,
        "section_slug" => $section_slug,
    ))->with($success ? "success_message" : "error_message", $message);
    
  }
  
}
