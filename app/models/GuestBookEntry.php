<?php

/**
 * This Eloquent class represents an entry in the guest book
 * 
 * Columns:
 *   - body:   The text of the guest book entry
 *   - author: The name of the author
 */
class GuestBookEntry extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
}
