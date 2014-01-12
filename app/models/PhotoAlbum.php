<?php

class PhotoAlbum extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public function updatePhotoCount() {
    $count = Photo::where('album_id', '=', $this->id)->count();
    $this->photo_count = $count;
    $this->save();
  }
  
}