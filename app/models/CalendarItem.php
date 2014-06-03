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
 * This Eloquent class represents an event in the calendar
 * 
 * Columns:
 *   - start_date:  The date at which the event starts
 *   - end_date:    The date at which the event ends
 *   - section_id:  The section of this event
 *   - event:       The event title
 *   - description: A short description of the event
 *   - type:        The type (category) of event
 */
class CalendarItem extends Eloquent {
  
  protected $fillable = array('start_date', 'end_date', 'section_id', 'event', 'description', 'type');
  
  // List of allowed event types, with their display name
  public static $eventTypes = array(
        'normal' => "Réunion normale",
        'special' => "Activité spéciale",
        'break' => "Congé",
        'weekend' => "Week-end",
        'camp' => "Grand camp",
        'bar' => "Bar Pi's",
        'leaders' => "Animateurs (privé)",
        'cleaning' => "Nettoyage (privé)",
    );
  
  /**
   * Returns the section of this event
   */
  public function getSection() {
    return Section::find($this->section_id);
  }
  
  /**
   * Returns the URL of the icon of this event's type
   */
  public function getIcon() {
    return URL::route('home') . "/images/calendar/" . $this->type . ".png";
  }
  
  /**
   * Returns the day of the month of the start date
   */
  public function getStartDay() {
    return substr($this->start_date, 8, 2) + 0;
  }
  
  /**
   * Returns the month of the start date
   */
  public function getStartMonth() {
    return substr($this->start_date, 5, 2) + 0;
  }
  
  /**
   * Returns the year of the start date
   */
  public function getStartYear() {
    return substr($this->start_date, 0, 4);
  }
  
  /**
   * Returns the number of days this event lasts (including the first and the last)
   */
  public function getDuration() {
    $start = strtotime($this->start_date);
    $end = strtotime($this->end_date) + (12*3600);
    $diff = $end - $start;
    return floor($diff / (3600*24)) + 1;
  }
  
  /**
   * Returns an Eloquent query that can be extended
   * searching for all public events 
   */
  public static function visibleToAllMembers() {
    return CalendarItem::where('type', '!=', 'leaders')
            ->where('type', '!=', 'cleaning');
  }
  
}
