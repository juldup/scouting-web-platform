{{--
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
--}}
<form action="{{ URL::route('search') }}" method='POST' class='search-box'>
  <input type="text" class="form-control" name="search_string" placeholder="Rechercher" />
  <span class="nav-link">
    <button type='submit' class="glyphicon glyphicon-search"></button>
  </span>
</form>
