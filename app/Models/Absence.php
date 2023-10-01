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
 * This Eloquent class represents an entry of absence (by a scout to an event)
 * 
 * Columns:
 *   - member_id:   The scout whose absence is notified
 *   - event_id:    The event not attended (can be null)
 *   - other_event: Name and date of the event if event_id is null
 *   - explanation: Small text explaining why the scout is not attending the event
 */
class Absence extends Eloquent {
  
  protected $fillable = array('member_id', 'event_id', 'other_event', 'explanation');
  
  /**
   * Returns the member of this event
   */
  public function getMember() {
    return Member::find($this->member_id);
  }
  
  /**
   * Returns the calendar item of this event (or null if it does not exist)
   */
  public function getCalendarItem() {
    if ($this->event_id)
      return CalendarItem::find($this->event_id);
    else
      return null;
  }
  
}
