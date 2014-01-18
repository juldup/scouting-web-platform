<div class="navbar navbar-default navbar-static-top first-nav-bar" role='navigation'>
  <div class='container'>
    <div class="navbar-header">
      <a class="navbar-brand" href="{{ URL::route('home') }}">{{ Parameter::get(Parameter::$UNIT_LONG_NAME) }}</a>
    </div>
    <div class="navbar-collapse collapse">
      @include('menu.menu')
      @include('menu.user_box')
    </div>
  </div>
</div>
<div class="navbar navbar-default navbar-static-top second-nav-bar">
  <div class="container">
    <div class="navbar-collapse collapse">
      @include('menu.tabs')
    </div>
  </div>
</div>
