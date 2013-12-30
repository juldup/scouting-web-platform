@extends('base')

@section('content')
  
  @if ($can_edit)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ $edit_url }}'>
          Modifier les nouvelles
        </a>
      </p>
    </div>
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Nouvelles {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
  @foreach ($news as $newsItem)
    <div class="row">
      <div class="col-lg-12">
        <h2>{{ $newsItem->title }}</h2>
        <p>{{ $newsItem->getHumanDate() }}</p>
        <div>
          {{ Helper::rawToHTML($newsItem->content) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop