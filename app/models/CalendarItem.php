<?php

class CalendarItem extends Eloquent {
  
  protected $fillable = array('start_date', 'end_date', 'section_id', 'event', 'description', 'type');
  
}