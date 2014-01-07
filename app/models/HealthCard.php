<?php

class HealthCard extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public function getMember() {
    return Member::find($this->member_id);
  }
  
}