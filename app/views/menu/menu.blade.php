
  <ul class="nav navbar-nav">
    @foreach ($menu_items as $category_name => $category_data)
      <li class="dropdown @if ($category_data['active']) active" @endif">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $category_name }} <b class="caret"></b></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          @foreach ($category_data['items'] as $item => $item_data)
            <li @if ($item_data['active']) class="active" @endif><a href="{{ $item_data['url'] }}">{{ $item }}</a></li>
          @endforeach
        </ul>
      </li>
    @endforeach
  </ul>

