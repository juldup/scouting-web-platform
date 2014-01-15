<?php

class News extends Eloquent {
  
  protected $fillable = array('title', 'body', 'news_date', 'section_id');
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  public function getHumanDate() {
    return date('d/m/Y', strtotime($this->news_date));
  }
  
}