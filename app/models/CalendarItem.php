<?php

class CalendarItem extends Eloquent {
  
  protected $fillable = array('start_date', 'end_date', 'section_id', 'event', 'description', 'type');
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getIcon() {
    return URL::route('home') . "/images/calendar/" . $this->type . ".png";
  }
  
}