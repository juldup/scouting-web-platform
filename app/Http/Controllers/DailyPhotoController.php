<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This selects random photos every day and presents them
 */
class DailyPhotoController extends BaseController {
  
  /**
   * [Route] Displays the daily photo page
   */
  public function showPage($date = null) {
    // Make sure this page can be displayed
    if (!Parameter::get(Parameter::$SHOW_DAILY_PHOTOS) || $date > date('Y-m-d')) {
      return App::abort(404);
    }
    // Make sure the current user has access to the photos
    if (!$this->user->isMember() && !$this->user->isFormerLeader() && !Parameter::get(Parameter::$PHOTOS_PUBLIC)) {
      return Helper::forbiddenNotMemberResponse();
    }
    $photos = $this->selectDailyPhotos($date);
    $photoList = array();
    foreach ($photos as $photo) {
      $album = PhotoAlbum::find($photo->album_id);
      $albumSection = Section::find($album->section_id);
      if ($album && $albumSection) {
        $photoList[] = array(
            'photoUrl' => $photo->getPreviewUrl(),
            'albumUrl' => URL::route('photo_album', array('album_id' => $album->id, 'section_slug' => $albumSection->slug)),
            'albumName' => $album->name,
        );
      }
    }
    // Make view
    return View::make('pages.photos.dailyPhoto', array(
        'photos' => $photoList,
        'date' => $date,
        'yesterdayUrl' => URL::route('daily_photos', array('date' => date('Y-m-d', strtotime('-1 day', strtotime($date ? $date : date('Y-m-d')))))),
    ));
  }
  
  /**
   * Returns an array containing the photos selected for the given date
   */
  private function selectDailyPhotos($date) {
    $dailyPhotos = DailyPhoto::getDailyPhotos($date);
    $photos = array();
    foreach ($dailyPhotos as $dailyPhoto) {
      $selectedPhoto = Photo::find($dailyPhoto->photo_id);
      if ($selectedPhoto) {
        $photos[] = $selectedPhoto;
      }
    }
    return $photos;
  }
  
}
