@extends('base')

@section('title')
  Télécharger
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('documents', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux documents de cette année
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if ($can_edit)
    <p>
      <a href='{{ $edit_url }}'>
        Gérer les documents à télécharger
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Documents {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @foreach ($documents as $category=>$docs)
    <div class="row">
      <div class="col-lg-12">
        <h3>{{ $category }}</h3>
      </div>
    </div>
    @foreach ($docs as $doc)
      <div class="row well clickable-no-default clickable">
        <div class="col-lg-12">
          @if ($user->isMember() || $doc->public)
            <legend>
              <div class="row">
                <div class="col-md-10">
                  @if ($showing_archives)
                    {{ Helper::dateToHuman($doc->doc_date) }} :
                  @endif
                  <a href="{{ URL::route('download_document', array('document_id' => $doc->id)) }}">
                    {{ $doc->title }}
                  </a>
                </div>
                <div class="col-md-2 text-right">
                  <a class="btn-sm btn-default">Télécharger</a>
                </div>
              </div>
            </legend>
            <p>
              {{ Helper::rawToHTML($doc->description) }}
            </p>
          @else
            <legend>
              <div class="row">
                <div class="col-md-10">
                  {{ $doc->title }}
                </div>
              </div>
            </legend>
            <p>
              Document privé.
            </p>
          @endif
        </div>
      </div>
    @endforeach
  @endforeach
  
  @if (count($documents) == 0)
    <div class="row">
      <div class="col-lg-12">
        <p>Il n'y a aucun document {{ $user->currentSection->de_la_section }} à télécharger.</p>
      </div>
    </div>
  @elseif (!$user->isMember())
    @include('subviews.limitedAccess')
    <div class="row">
      <div class="col-lg-12">
        <p>
          Vous pouvez également recevoir un document directement par e-mail :
        </p>
        {{ Form::open(array('route' => 'send_document_by_email')) }}
          M'envoyer le document
          {{ Form::select('document_id', $documentSelectList, null, array('class' => 'form-control large')) }}
          à l'adresse
          {{ Form::text('email', '', array('size' => 35, 'class' => 'form-control large')) }}
          <span class="horiz-divider"></span>
          {{ Form::submit('Envoyer', array('class' => 'btn btn-primary')) }}
        {{ Form::close() }}
      </div>
    </div>
  @endif
  
  @if ($has_archives)
    <div class="vertical-divider"></div>
    @if ($showing_archives)
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('document_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les documents plus anciens</a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('document_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les documents archivés</a>
        </div>
      </div>
    @endif
  @endif
  
@stop