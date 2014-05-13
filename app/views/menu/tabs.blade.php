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
