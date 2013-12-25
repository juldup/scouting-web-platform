
  <ul class="nav navbar-nav">
    @foreach ($menu_items as $category_name=>$category_items)
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $category_name }} <b class="caret"></b></a>
        <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
          @foreach ($category_items as $item=>$url)
            <li><a href="{{ $url }}">{{ $item }}</a></li>
          @endforeach
        </ul>
      </li>
    @endforeach
  </ul>

