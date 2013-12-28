<?php

class CalendarController extends BaseController {
  
  public function showPage($year = null, $month = null) {
    
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
    $months_short = array("jan", "fév", "mar", "avr", "mai", "juin", "juil", "août", "sep", "oct", "nov", "déc"); // mois
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
      
      for ($day = $startDay; $day <= $endDay; $day++) {
        $events[$day][] = $item;
      }
      
    }
    
    return View::make('pages.calendar.calendar', array(
        'can_edit' => $this->user->can(Privilege::$EDIT_CALENDAR),
        'edit_url' => URL::route('manage_calendar'),
        'blank_days_before' => $blank_days_before,
        'blank_days_after' => $blank_days_after,
        'days_in_month' => $days_in_month,
        'days' => $days,
        'months' => $months,
        'month' => $month,
        'year' => $year,
        'events' => $events,
    ));
  }
  
  public function showEdit() {
    return View::make('pages.calendar.editCalendar', array(
        
    ));
  }
  
}
