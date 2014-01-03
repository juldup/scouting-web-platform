@extends('base')

@section('title')
  Inscriptions
@stop

@section('content')
  
  {{-- Link to management --}}
  @if ($can_edit || $can_manage)
    <div class="row">
      <div class='pull-right management'>
        @if ($can_edit)
          <p>
            <a class='button' href='{{ URL::route('edit_registration_page') }}'>
              Modifier cette page
            </a>
          </p>
        @endif
        @if ($can_manage)
          <p>
            <a class='button' href='{{ URL::route('manage_registration') }}'>
              Gérer les inscriptions
            </a>
          </p>
        @endif
      </div>
    </div>
  @endif
  
  <div class="row page_content">
    <h1>{{ $page_title }}</h1>
    {{ $page_content }}
  </div>
@stop