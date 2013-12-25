@extends('base')

@section('content')
  
  {{-- Link to management --}}
  @if ($can_edit)
    <p class='management'>
      <a class='button' href='{{ $edit_url }}'>
        Modifier cette page
      </a>
    </p>
  @endif
  
  <div class="row">
    <h1>{{ $page_title }}</h1>
    {{ $page_content }}
  </div>
@stop