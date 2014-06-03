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
<ul class="nav navbar-nav navbar-left">
  @foreach ($menu_items as $category_name => $category_data)
    <li class="dropdown @if ($category_data['active']) active" @endif">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{{ $category_name }}} <b class="caret"></b></a>
      <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
        @foreach ($category_data['items'] as $item => $item_data)
          @if ($item_data['is_divider'])
            <li class="divider"></li>
          @elseif ($item_data['is_title'])
            <li class="divider"></li>
            <li class="dropdown-header">{{{ $item }}}</li>
          @else
            @if ($item_data['url'])
              <li @if ($item_data['active']) class="active" @endif><a href="{{ $item_data['url'] }}">{{{ $item }}}</a></li>
            @else
            <li class="disabled"><a>{{{ $item }}}</a></li>
            @endif
          @endif
        @endforeach
      </ul>
    </li>
  @endforeach
</ul>
