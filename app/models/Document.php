<?php

class Document extends Eloquent {
  
  protected $fillable = array('title', 'description', 'doc_date', 'section_id');
  
  public function getSection() {
    return Section::find($this->section_id);
  }
  
}