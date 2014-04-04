<ul class="nav navbar-nav navbar-right">
  <li class="dropdown active">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="navbar-section-hint">Section :</span>
      {{ $user->currentSection->name }} <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      @foreach ($tabs as $tab)
        @if ($tab['is_selected'])
          <li class="active">
            <a href="{{ $tab['link'] }}">
              {{ $tab['text'] }}
            </a>
          </li>
        @else
          <li>
            <a href="{{ $tab['link'] }}">
              {{ $tab['text'] }}
            </a>
          </li>
        @endif
      @endforeach
    </ul>
  </li>
</ul>
