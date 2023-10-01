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
 * This Eloquent class represents an entry of attendance (by a member to an event)
 * 
 * Columns:
 *   - member_id:  The member that was present or absent to the event
 *   - event_id:   The event attended or not
 *   - section_id: Id of the section this belongs to (usually the member's section for the current year)
 *   - attended:   Attendance status by this member (0 = absent, 1 = present, 2 = excused)
 */
class Attendance extends Eloquent {
  
  protected $fillable = array('member_id', 'event_id', 'section_id', 'attended');
  
}
