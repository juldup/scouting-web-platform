@extends('base')

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier cette page
      </a>
    </p>
  @endif
@stop

@section('content')
  <div class="row page_content">
    <h1>{{ $page_title }}</h1>
    {{ $page_content }}
  </div>
@stop