<div class="navbar navbar-default navbar-static-top" role='navigation'>
  <div class='container'>
    <div class="navbar-header">
      <a class="navbar-brand" href="{{ URL::route('home') }}">{{ Parameter::get(Parameter::$UNIT_LONG_NAME) }}</a>
    </div>
    <div class="navbar-collapse collapse">
      @include('menu.menu')
      @include('menu.user_box')
      @include('menu.tabs')
    </div>
  </div>
</div>
