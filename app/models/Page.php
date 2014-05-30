<?php

/**
 * This Eloquent class represents an editable page of the website
 * 
 * Columns:
 *   - type:       Determines which page this is
 *   - section_id: Determines to which section this page belongs
 *   - body_html:  The content of the page in html
 */
class Page extends Eloquent {
  
  protected $fillable = array('type', 'section_id', 'body_html');
  
}
