@extends('base')

@section('additional_javascript')
  <script src="{{ asset('js/edit-news.js') }}"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var news = new Array();
    @foreach ($news as $item)
      news[{{ $item->id }}] = {
        'title': "{{ Helper::sanitizeForJavascript($item->title) }}",
        'body': "{{ Helper::sanitizeForJavascript($item->body) }}",
        'section': {{ $item->section_id }},
        'delete_url': "{{ URL::route('manage_news_delete', array('news_id' => $item->id)) }}"
      };
    @endforeach
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ $page_url }}'>
      Retour aux nouvelles
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-news'))
  
  <div class="row">
    <div class="col-lg-12">
  
      <h1>Nouvelles {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      <div id="news_form" class="form-horizontal well"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('manage_news_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('news_id', 0) }}
          <div class="form-group">
            {{ Form::label('news_title', "Titre", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-5">
              {{ Form::text('news_title', '', array('class' => 'form-control', 'placeholder' => "Titre de la nouvelle")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('news_body', "Contenu", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-8">
              {{ Form::textarea('news_body', '', array('class' => 'form-control', 'rows' => 3, 'placeholder' => "Contenu de la nouvelle")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section', "Section", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-5">
              {{ Form::select('section', $sections, $user->currentSection->id, array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5 col-md-offset-2">
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
              <a class="btn btn-danger" id='delete_link' style="display: none;" href="">Supprimer</a>
              <a class="btn btn-default" href="javascript:dismissNewsForm()">Fermer</a>
            </div>
          </div>
        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a class="btn btn-default" href="javascript:addNews()">Ajouter une nouvelle</a></p>
    </div>
  </div>
  
  @foreach ($news as $newsItem)
    <div class="row well">
      <div class="col-lg-12">
        <legend>
          <div class="row">
            <div class="col-md-10">
              {{ $newsItem->title }}} – {{{ $newsItem->getHumanDate() }}}
            </div>
            <div class="col-md-2 text-right">
              <a class="btn-sm btn-default" href="javascript:editNews({{ $newsItem->id }})">Modifier</a>
            </div>
          </div>
          
        </legend>
        <p>
          
        </p>
        <div>
          {{ Helper::rawToHTML($newsItem->body) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop