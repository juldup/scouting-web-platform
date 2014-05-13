@extends('base')

@section('title')
  Liens utiles
@stop

@section('forward_links')
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_links') }}'>
        Modifier les liens
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Liens utiles</h1>
    </div>
  </div>
  
  @foreach ($links as $link)
    <div class="row">
      <div class="col-md-12">
        <div class="well clickable clickable-no-default">
          <legend>
            <a href="{{{ $link->url }}}" target="_blank">{{{ $link->title }}}</a>
          </legend>
          <div>
            {{ Helper::rawToHTML($link->description) }}
          </div>
        </div>
      </div>
    </div>
  @endforeach
  
@stop