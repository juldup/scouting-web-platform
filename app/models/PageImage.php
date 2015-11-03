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
 * This Eloquent page represents an image within an editable page (see class Page)
 * of the website
 * 
 * Columns:
 *   - original_name: Filename of the image
 */
class PageImage extends Eloquent {
  
  protected $fillable = array('page_id', 'original_name');
  
  // Folder in the file system (relative to storage folder) where the page images are stored
  protected static $FOLDER_PATH = "site_data/images/pages/";
  
  /**
   * Returns the URL to access this image
   */
  public function getURL() {
    return URL::route('get_page_image', array('image_id' => $this->id));
  }
  
  /**
   * Returns the path of the image in the file system
   */
  public function getPath() {
    return $this->getPathFolder() . $this->getPathFilename();
  }
  
  /**
   * Returns the folder containing the image in the file system
   */
  public function getPathFolder() {
    return storage_path(self::$FOLDER_PATH);
  }
  
  /**
   * Returns the name of the image file in the file system
   */
  public function getPathFilename() {
    return $this->id . ".image";
  }
  
}
