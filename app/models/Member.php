<?php

class Page extends Eloquent {
  
  protected $fillable = array('type', 'section_id', 'content_html', 'content_markdown');
  
}
