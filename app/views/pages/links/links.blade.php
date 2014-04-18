@extends('base')

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
    <div class="row well clickable clickable-no-default">
      <div class="col-lg-12">
        <legend>
          <a href="{{{ $link->url }}}" target="_blank">{{{ $link->title }}}</a>
        </legend>
        <div>
          {{ Helper::rawToHTML($link->description) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop