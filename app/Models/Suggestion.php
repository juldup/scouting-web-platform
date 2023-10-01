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
 * This Eloquent class represents a user suggestion from the suggestion page
 * 
 * Columns:
 *   - body:     Raw text of the suggestion
 *   - response: Reply to the suggestion
 *   - user_id:  User who posted the suggestion (can be null for unlogged visitors)
 */
class Suggestion extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
}
