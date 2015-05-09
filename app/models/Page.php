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
 * This Eloquent class represents an editable page of the website
 * 
 * Columns:
 *   - type:       Determines which page this is
 *   - section_id: Determines to which section this page belongs
 *   - body_html:  The content of the page in html
 */
class Page extends Eloquent {
  
  protected $fillable = array('type', 'section_id', 'body_html', 'title', 'slug', 'position');
  
}
