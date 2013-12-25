@extends('base')

@section('content')
  
  {{-- Link to management --}}
  @if ($can_edit)
    <p class='management'>
      <a class='button' href='{{ URL::route('manage_home') }}'>
        Modifier la page d'accueil
      </a>
    </p>
  @endif
  
  <div class="row">
    {{ $page_content }}
  </div>
@stop