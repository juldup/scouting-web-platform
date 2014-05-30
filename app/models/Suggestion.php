<?php

/**
 * This Eloquent class represents a user suggestion from the suggestion page
 * 
 * Columns:
 *   - body:     Raw text of the suggestion
 *   - response: Reply to the suggestion
 *   - user_id:  User who posted the suggestion (can be null for unlogged visitors)
 */
class Suggestion extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
}
