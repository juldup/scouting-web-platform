{{--
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
--}}

<?php
use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;
use App\Models\MemberHistory;
?>

<ul class="nav navbar-nav navbar-left">
  <!-- Main menu -->
  <li class="dropdown @if ($mainMenu['active'] && $groupedSectionMenu) active" @endif">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Unité <b class="caret"></b></a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
      @foreach ($mainMenu['items'] as $item => $item_data)
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
  @if ($groupedSectionMenu)
    <!-- Section/page menu -->
    <li class="dropdown @if (isset($sectionPage)) active @endif">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        @if (isset($sectionPage))
          <span class="glyphicon glyphicon-certificate" style="color: {{ $user->currentSection->color }};"></span> 
          <!--{{{ $user->currentSection->name }}}-->
          Section @if ($user->currentSection->id != 1) : {{{ $user->currentSection->name }}} @endif
        @else
          Section
        @endif
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <!-- List of sections -->
        @foreach ($sectionList as $tab)
          <li class="{{ $tab['is_selected'] ? "active" : "" }}">
            <a href="{{ $tab['link'] }}">
              <span class="glyphicon glyphicon-certificate" style="color: {{ $tab['color'] }};"></span> {{{ $tab['text'] }}}
            </a>
          </li>
        @endforeach
        <li class="divider"></li>
        <!-- Section menu items -->
        @foreach ($sectionMenuItems as $item => $item_data)
          @if ($item_data['url'])
            <li @if ($item_data['active']) class="active" @endif><a href="{{ $item_data['url'] }}">{{{ $item }}}</a></li>
          @else
            <li class="disabled"><a>{{{ $item }}}</a></li>
          @endif
        @endforeach
      </ul>
    </li>
  @else
    <!-- Section menu -->
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        @if ($section_page)
          <span class="glyphicon glyphicon-certificate" style="color: {{ $user->currentSection->color }};"></span> 
          <!--{{{ $user->currentSection->name }}}-->
          Section @if ($user->currentSection->id != 1) : {{{ $user->currentSection->name }}} @endif
        @else
          Section
        @endif
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        @foreach ($sectionList as $tab)
          <li class="{{ $tab['is_selected'] ? "active" : "" }}">
            <a href="{{ $tab['link'] }}">
              <span class="glyphicon glyphicon-certificate" style="color: {{ $tab['color'] }};"></span> {{{ $tab['text'] }}}
            </a>
          </li>
        @endforeach
      </ul>
    </li>
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        Page
        @if ($section_page)
          @foreach ($sectionMenuItems as $item => $item_data)
            @if ($item_data['active'])
              : {{{ $item }}}
            @endif
          @endforeach
        @endif
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <!-- Section menu items -->
        @foreach ($sectionMenuItems as $item => $item_data)
          @if ($item_data['url'])
            <li @if ($item_data['active']) class="active" @endif><a href="{{ $item_data['url'] }}">{{{ $item }}}</a></li>
          @else
            <li class="disabled"><a>{{{ $item }}}</a></li>
          @endif
        @endforeach
      </ul>
    </li>
  @endif
  <!-- News shortcut -->
  @if (Parameter::get(Parameter::$SHOW_NEWS))
    <li class="{{ $globalNewsSelected ? "active" : "" }}">
      <a href="{{ URL::route('global_news') }}">
        Actualités
      </a>
    </li>
  @endif
  <!-- Daily photo -->
  @if (Parameter::get(Parameter::$SHOW_DAILY_PHOTOS))
    <li class="{{ $dailyPhotosSelected ? "active" : "" }}">
      <a href="{{ URL::route('daily_photos') }}">
        Photos du jour
      </a>
    </li>
  @endif
</ul>

<!-- Search and leader menu -->
  @if ($leaderMenu || Parameter::get(Parameter::$SHOW_SEARCH))
    <ul class="nav navbar-nav navbar-right section-selector">
      @if (Parameter::get(Parameter::$SHOW_SEARCH))
        <li class="nav navbar-nav search-box-wrapper">
          @include('pages.search.search_box')
        </li>
      @endif
      @if ($leaderMenu)
        <li class="dropdown @if ($leaderMenu['active']) active" @endif">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">Coin des animateurs <b class="caret"></b></a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            @foreach ($leaderMenu['items'] as $item => $item_data)
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
      @endif
    </ul>
  @endif
