<?php

/**
 * This Eloquent class represents a photo album. A photo album belongs
 * to a section and contains photos.
 * 
 * Columns:
 *   - section_id:  The section this album belongs to
 *   - name:        The title of the album
 *   - photo_count: The number of photos in the album (to avoid recomputing it each time)
 *   - position:    The order of the album in the album list
 *   - date:        The date the album was created (used for auto-archiving)
 *   - archived:    Whether the album has been archived
 *   - last_update: The last date at which the album has been modified (TODO currently unused and never updated)
 */
class PhotoAlbum extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  /**
   * Counts how many photos this album contains and updates the photo_count field
   */
  public function updatePhotoCount() {
    $count = Photo::where('album_id', '=', $this->id)->count();
    $this->photo_count = $count;
    $this->save();
  }
  
  /**
   * Returns the section that owns this album
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
}
