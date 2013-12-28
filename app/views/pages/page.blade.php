@extends('base')

@section('content')
  
  {{-- Link to management --}}
  @if ($can_edit)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ $edit_url }}'>
          Modifier cette page
        </a>
      </p>
    </div>
  @endif
  
  <div class="row page_content">
    <h1>{{ $page_title }}</h1>
    {{ $page_content }}
  </div>
@stop