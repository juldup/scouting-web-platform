<?php

class HealthCard extends Eloquent {
  
  protected $guarded = array('id', 'signatory_id', 'signatory_email',
      'reminder_sent', 'signature_date', 'created_at', 'updated_at');
  
  public function getMember() {
    return Member::find($this->member_id);
  }
  
  public function daysBeforeDeletion() {
    $seconds_diff = strtotime($this->signature_date) + 365*24*3600 - time();
    return ceil($seconds_diff / (3600 * 24));
  }
  
}