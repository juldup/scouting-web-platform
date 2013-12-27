<?php

class Member extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
}
