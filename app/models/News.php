<?php

/**
 * This Eloquent class represents a news entry displayed on
 * the news page
 * 
 * Columns:
 *   - news_date:  The date the piece of news was written
 *   - section_id: The section this piece of news belongs to
 *   - title:      The name of the news
 *   - body:       The text of the news
 */
class News extends Eloquent {
  
  protected $fillable = array('title', 'body', 'news_date', 'section_id');
  
  /**
   * Returns the section this piece of news belongs to
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  /**
   * Returns the date of the piece of news in a human-readable format ('d/m/Y')
   */
  public function getHumanDate() {
    return date('d/m/Y', strtotime($this->news_date));
  }
  
}
