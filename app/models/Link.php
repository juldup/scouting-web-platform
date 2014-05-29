<?php

/**
 * This Eloquent class reprents a link instance in the public link page
 * 
 * Columns:
 *   - title:       The name of the link
 *   - url:         The absolute URL of the link
 *   - description: A short description of the link
 */
class Link extends Eloquent {
  
  protected $fillable = array('title', 'url', 'description');
  
}
