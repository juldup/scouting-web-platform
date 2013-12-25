<ul class="nav navbar-nav navbar-right">
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
