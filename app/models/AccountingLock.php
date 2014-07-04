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
 * This Eloquent class represents a lock made by a user on editing the accounting of a section/year.
 * Locks are automatic and prevent concurrent edition of the accounting data. Only one lock can be
 * active (i.e. have a timestamp more recent than 30 seconds ago).
 * If the same user opens a new window for editing the accounting data (e.g. refreshes the page), then
 * the previous lock becomes invalidated and a new lock is created for the current page (to avoid concurrence
 * between tabs for the same user).
 * If another user tries to edit the accounting data, they can only view the data and get a warning message.
 * The webpage must send keepalive requests every 10 seconds in order for the lock to be renewed.
 * If a page tries to update data with an invalidated lock, a warning is shown to the user.
 * 
 * Columns:
 *   - year:        The 'YYYY-YYYY' scouting year (e.g 2014-2015)
 *   - section_id:  The section this transaction belongs to
 *   - user_id:     The user owning the lock
 *   - timestamp:   The timestamp of the lock
 *   - invalidated: Whether this lock has been invalidated
 */
class AccountingLock extends Eloquent {
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
}
