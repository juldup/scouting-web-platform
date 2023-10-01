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
 * This Eloquent class represents the selected daily photo of a given date. Daily
 * photos are picked randomly within the photo database.
 * 
 * Columns:
 *   - date:     The date
 *   - photo_id: The selected photo for the date
 */
class DailyPhoto extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  // Number of photos shown every day
  public static $DAILY_PHOTO_COUNT = 2;
  
  /**
   * Returns the photos selected for the given date (today if null).
   * If no photo is selected yet, new photos are picked randomly.
   */
  public static function getDailyPhotos($date = null) {
    if (!$date) $date = date('Y-m-d');
    $dailyPhotos = DailyPhoto::where('date', '=', $date)->limit(2)->get();
    if (!count($dailyPhotos)) {
      $dailyPhotos = array();
      for ($i = 0; $i < self::$DAILY_PHOTO_COUNT; $i++) {
        $dailyPhoto = self::generateDailyPhoto($date);
        if ($dailyPhoto) $dailyPhotos[] = $dailyPhoto;
      }
    }
    return $dailyPhotos;
  }
  
  /**
   * Picks a random photo and creates the DailyPhoto corresponding.
   * 
   * @param string $date      Date for which a daily photo is created
   * @param integer $retries  Number of times the selection is done again if an already-picked photo is selected
   */
  public static function generateDailyPhoto($date, $retries = 20) {
    // Get all photos before the given date
    $photos = Photo::where('created_at', '<=', "$date 23:59:59")->get();
    if (!count($photos)) {
      // No photo found
      return null;
    }
    // Select a random photo
    $selectedPhoto = $photos[rand(0, count($photos) - 1)];
    // Try to select another photo if this one has already been chosen another day
    if ($retries > 0) {
      $duplicateDailyPhoto = DailyPhoto::where('photo_id', '=', $selectedPhoto->id)->first();
      if ($duplicateDailyPhoto) return self::generateDailyPhoto ($date, $retries - 1);
    }
    // Create and return daily photo instance
    $dailyPhoto = DailyPhoto::create(array(
        'date' => $date,
        'photo_id' => $selectedPhoto->id,
    ));
    return $dailyPhoto;
  }
  
}
