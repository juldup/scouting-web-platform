<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * This Eloquent class represents an entry of payment for a member for a given event
 * 
 * Columns:
 *   - member_id:  The member for which the fee has been paid or not
 *   - event_id:   Id of the event this belongs to
 *   - paid:       Whether the fee has been paid
 */
class Payment extends Model {
  
  protected $fillable = array('member_id', 'event_id', 'paid');
  
}
