@extends('base')

@section('content')
  
  @if ($can_edit)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ URL::route('edit_links') }}'>
          Modifier les liens
        </a>
      </p>
    </div>
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Liens utiles</h1>
    </div>
  </div>
  
  @foreach ($links as $link)
    <div class="row">
      <div class="col-lg-12">
        <h2><a href="{{ $link->url }}">{{ $link->title }}</a></h2>
        <div>
          {{ Helper::rawToHTML($link->description) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop