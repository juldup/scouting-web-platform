<ul>
  @foreach ($menu_items as $category_name=>$category_items)
    <li>{{ $category_name }}
      <ul>
        @foreach ($category_items as $item=>$url)
          <li><a href="{{ $url }}">{{ $item }}</a></li>
        @endforeach
      </ul>
    </li>
  @endforeach
</ul>

@include('menu.user_box')