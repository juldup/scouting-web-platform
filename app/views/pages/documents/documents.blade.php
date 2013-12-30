@extends('base')

@section('content')
  
  @if ($can_edit)
    <div class="row">
      <p class='pull-right management'>
        <a class='button' href='{{ $edit_url }}'>
          Gérer les documents à télécharger
        </a>
      </p>
    </div>
  @endif
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Documents {{ $user->currentSection->de_la_section }}</h1>
    </div>
  </div>
  
  @foreach ($documents as $doc)
    <div class="row">
      <div class="col-lg-12">
        @if ($user->isMember() || $doc->public)
          <h2>
            <a href="{{ URL::route('download_document', array('document_id' => $doc->id)) }}">
              {{ $doc->title }}
            </a>
          </h2>
          <div>
            {{ Helper::rawToHTML($doc->description) }}
          </div>
        @else
          <h2>
            {{ $doc->title }}
          </h2>
          <div>
            Vous ne pouvez pas accéder à ce document.
          </div>
        @endif
      </div>
    </div>
  @endforeach
  
@stop