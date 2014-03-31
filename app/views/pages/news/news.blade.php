@extends('base')

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('news', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux nouvelles de cette année
      </a>
    </p>
  @endif
@stop

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
      @if (count($news) == 0)
        <p>Aucune nouvelle.</p>
      @endif
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
          {{ Helper::rawToHTML($newsItem->body) }}
        </div>
      </div>
    </div>
  @endforeach
  
  @if ($has_archives)
    <div class="vertical-divider"></div>
    @if ($showing_archives)
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('news_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les nouvelles plus anciennes</a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('news_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les nouvelles archivées</a>
        </div>
      </div>
    @endif
  @endif
  
@stop