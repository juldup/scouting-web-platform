@extends('base')

@section('forward_links')
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Modifier les nouvelles
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Nouvelles {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
  @foreach ($news as $newsItem)
    <div class="row well">
      <div class="col-lg-12">
        <legend>
          @if ($user->currentSection->id == 1)
            {{ Section::find($newsItem->section_id)->name }} :
          @endif
          {{ $newsItem->title }} – {{ $newsItem->getHumanDate() }}
        </legend>
        <div>
          {{ Helper::rawToHTML($newsItem->content) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop