@extends('base')

@section('title')
  Inscriptions
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_registration_page') }}'>
        Modifier cette page
      </a>
    </p>
  @endif
  @if ($can_manage)
    <p>
      <a href='{{ URL::route('manage_registration') }}'>
        Gérer les inscriptions
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