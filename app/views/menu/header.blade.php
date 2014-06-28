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
<div class="navbar navbar-default navbar-static-top first-nav-bar" role='navigation'>
  <div class='container'>
    <div class="navbar-header" style="margin-left: 15px;">
      <a class="navbar-brand" href="{{ URL::route('home') }}">
        <img class="website-logo" src='{{ URL::route('website_logo') }}'/>
        <span class="horiz-divider"></span>
        <span class="website-title">
          {{{ Parameter::get(Parameter::$UNIT_LONG_NAME) }}}
        </span>
      </a>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    
      <div class="collapse navbar-collapse">
        @include('menu.user_box')
      </div>
    
  </div>
</div>
<div class="navbar navbar-collapse navbar-default navbar-static-top second-nav-bar collapse" id='bs-example-navbar-collapse-1'
     @if ($section_page)
       style="border-bottom: 4px {{ $user->currentSection->color }} solid;"
     @endif
     >
  <div class="container">
    <div>
      @include('menu.menu')
    </div>
  </div>
</div>
