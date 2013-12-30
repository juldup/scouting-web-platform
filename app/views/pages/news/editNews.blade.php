@extends('base')

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_news.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var news = new Array();
    @foreach ($news as $item)
      news[{{ $item->id }}] = {
        'title': "{{ Helper::sanitizeForJavascript($item->title) }}",
        'content': "{{ Helper::sanitizeForJavascript($item->content) }}",
        'section': {{ $item->section_id }},
        'delete_url': "{{ URL::route('manage_news_delete', array('news_id' => $item->id)) }}"
      };
    @endforeach
  </script>
@stop

@section('content')
  
  <div class="row">
    <p class='pull-right management'>
      <a class='button' href='{{ $page_url }}'>
        Retour à la page
      </a>
    </p>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
  
      <h1>Nouvelles {{ $user->currentSection->de_la_section }}</h1>
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message'); }}</p>
      @endif
      @if (Session::has('error_message'))
        <p class='alert alert-danger'>{{ Session::get('error_message'); }}</p>
      @endif
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      <div id="news_form"
           @if (!Session::has('error_message')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('manage_news_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('news_id', 0) }}
          <p>
            Title :
            {{ Form::text('news_title', '', array('size' => '35', 'placeholder' => "Titre de la nouvelle")) }}
          <p>
            Content :
            {{ Form::textarea('news_content', '', array('cols' => '35', 'rows' => 3, 'placeholder' => "Contenu de la nouvelle")) }}
          </p>
          <p>
            Section :
            {{ Form::select('section', $sections, $user->currentSection->id) }}
          </p>
          <p>
            {{ Form::submit('Enregistrer') }}
            <a id='delete_link' href="">Supprimer</a>
            <a href="javascript:dismissNews()">Fermer</a>
          </p>

        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a href="javascript:addNews()">ajouter une nouvelle</a></p>
    </div>
  </div>
  
  @foreach ($news as $newsItem)
    <div class="row">
      <div class="col-lg-12">
        <h2>{{ $newsItem->title }}</h2>
        <p>{{ $newsItem->getHumanDate() }} <a href="javascript:editNews({{ $newsItem->id }})">Modifier</a></p>
        <div>
          {{ Helper::rawToHTML($newsItem->content) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop