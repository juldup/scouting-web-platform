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
<ul class="nav navbar-nav navbar-right section-selector">
  <li class="dropdown active">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="navbar-section-hint">Section :</span>
      <span class="navbar-section-hint-small-screen">
        Section :
      </span>
      {{{ $user->currentSection->name }}} <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      @foreach ($tabs as $tab)
        @if ($tab['is_selected'])
          <li class="active">
            <a href="{{ $tab['link'] }}">
              {{{ $tab['text'] }}}
            </a>
          </li>
        @else
          <li>
            <a href="{{ $tab['link'] }}">
              {{{ $tab['text'] }}}
            </a>
          </li>
        @endif
      @endforeach
    </ul>
  </li>
  @if ($section_page)
    <div class="section-selector-hint-wrapper">
      <div class="section-selector-hint">
        <div class="section-selector-hint-arrow">
          <span class="glyphicon glyphicon-arrow-up"></span>
        </div>
        <div>
          Adaptez cette page à une section
        </div>
      </div>
    </div>
  @endif
</ul>
