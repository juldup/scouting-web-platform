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

@section('back_links')
  <p>
    <a href='{{ $page_url }}'>
      Retour aux nouvelles
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
  
      <h1>Nouvelles {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      <div id="news_form"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('manage_news_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('news_id', 0) }}
          <p>
            Titre :
            {{ Form::text('news_title', '', array('size' => '35', 'placeholder' => "Titre de la nouvelle")) }}
          <p>
            Contenu :
            {{ Form::textarea('news_content', '', array('cols' => '35', 'rows' => 3, 'placeholder' => "Contenu de la nouvelle")) }}
          </p>
          <p>
            Section :
            {{ Form::select('section', $sections, $user->currentSection->id) }}
          </p>
          <p>
            {{ Form::submit('Enregistrer') }}
            <a id='delete_link' style="display: none;" href="">Supprimer</a>
            <a href="javascript:dismissNewsForm()">Fermer</a>
          </p>

        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a href="javascript:addNews()">Ajouter une nouvelle</a></p>
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