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
 * This Eloquent class represents a news entry displayed on
 * the news page
 * 
 * Columns:
 *   - news_date:  The date the piece of news was written
 *   - section_id: The section this piece of news belongs to
 *   - title:      The name of the news
 *   - body:       The text of the news
 */
class News extends Eloquent {
  
  protected $fillable = array('title', 'body', 'news_date', 'section_id');
  
  /**
   * Returns the section this piece of news belongs to
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  /**
   * Returns the date of the piece of news in a human-readable format ('d/m/Y')
   */
  public function getHumanDate() {
    return date('d/m/Y', strtotime($this->news_date));
  }
  
}
