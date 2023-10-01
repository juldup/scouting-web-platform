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
<ul class="nav navbar-nav navbar-right user-box">
  @if (!$user->isConnected())
    {{-- The visitor is not connected --}}
    <li>
      <a href="{{ URL::route('login') }}">Me connecter</a>
    </li>
  @else
    {{-- The visitor is connected --}}
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{{ $user->username }}} <b class="caret"></b></a>
      <ul class='dropdown-menu'>
        <li><a href="{{ URL::route('logout') }}">Déconnexion</a></li>
        <li><a href="{{ URL::route('edit_user') }}">Modifier</a></li>
      </ul>
    </li>
  @endif
</ul>
