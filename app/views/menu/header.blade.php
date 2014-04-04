<div class="navbar navbar-default navbar-static-top first-nav-bar" role='navigation'>
  <div class='container'>
    <div class="navbar-header" style="margin-left: 15px;">
      <a class="navbar-brand" href="{{ URL::route('home') }}">
        <img class="website-logo" src='{{ URL::route('website_logo') }}'/>
        <span class="horiz-divider"></span>
        <span class="website-title">
          {{ Parameter::get(Parameter::$UNIT_LONG_NAME) }}
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
<div class="navbar navbar-collapse navbar-default navbar-static-top second-nav-bar collapse" id='bs-example-navbar-collapse-1'>
  <div class="container">
    <div>
      @include('menu.menu')
      @include('menu.tabs')
    </div>
  </div>
</div>
