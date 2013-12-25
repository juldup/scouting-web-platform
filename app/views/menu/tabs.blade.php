<ul>
  @foreach ($tabs as $tab)
    @if ($tab['is_selected'])
      <li class="selected_tab">
        <a href="{{ $tab['link'] }}">
          {{ $tab['text'] }}
        </a> XXX
      </li>
    @else
      <li class="unselected_tab">
        <a href="{{ $tab['link'] }}">
          {{ $tab['text'] }}
        </a>
      </li>
    @endif
  @endforeach
</ul>