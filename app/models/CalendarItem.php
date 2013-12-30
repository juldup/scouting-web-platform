<?php

class CalendarItem extends Eloquent {
  
  protected $fillable = array('start_date', 'end_date', 'section_id', 'event', 'description', 'type');
  
  public static $eventTypes = array(
        'normal' => "Réunion normale",
        'special' => "Activité spéciale",
        'break' => "Congé",
        'leaders' => "Animateurs",
        'weekend' => "Week-end",
        'camp' => "Grand camp",
        'bar' => "Bar Pi's",
        'cleaning' => "Nettoyage",
    );
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getIcon() {
    return URL::route('home') . "/images/calendar/" . $this->type . ".png";
  }
  
  public function getStartDay() {
    return substr($this->start_date, 8, 2) + 0;
  }
  
  public function getStartMonth() {
    return substr($this->start_date, 5, 2) + 0;
  }
  
  public function getStartYear() {
    return substr($this->start_date, 0, 4);
  }
  
  public function getDuration() {
    $start = strtotime($this->start_date);
    $end = strtotime($this->end_date) + (12*3600);
    $diff = $end - $start;
    return floor($diff / (3600*24)) + 1;
  }
  
}