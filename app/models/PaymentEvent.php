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
 * This Eloquent class represents an event for which payments can be made. The payments
 * of the members are stored using the class Payment.
 * 
 * Columns:
 *   - section_id: Id of the section this belongs to (usually the member's section for the current year)
 *   - year:       The YYYY-YYYY year of the event
 *   - name:       The name of the event (must be distinct for different events of the same year)
 */
class PaymentEvent extends Eloquent {
  
  protected $fillable = array('name', 'section_id', 'year');
  
}
