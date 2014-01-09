@extends('base')

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_links.js"></script>
  <script>
    var links = new Array();
    @foreach ($links as $link)
      links[{{ $link->id }}] = {
        'title': "{{ Helper::sanitizeForJavascript($link->title) }}",
        'url': "{{ Helper::sanitizeForJavascript($link->url) }}",
        'description': "{{ Helper::sanitizeForJavascript($link->description) }}",
        'delete_url': "{{ URL::route('edit_links_delete', array('link_id' => $link->id)) }}"
      };
    @endforeach
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('links') }}'>
      Retour à la page de liens utiles
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Liens utiles</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      <div id="link_form"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('edit_links_submit'))) }}
          {{ Form::hidden('link_id', 0) }}
          <p>
            Nom du lien :
            {{ Form::text('link_title') }}
          <p>
            URL de la page :
            {{ Form::text('link_url') }}
          </p>
          <p>
            Description :
            {{ Form::textarea('link_description') }}
          </p>
          <p>
            {{ Form::submit('Enregistrer') }}
            <a id='delete_link' style="display: none;" href="">Supprimer</a>
            <a href="javascript:dismissLinkForm()">Fermer</a>
          </p>
        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a href="javascript:addLink()">Ajouter un nouveau lien</a></p>
    </div>
  </div>
  
  @foreach ($links as $link)
    <div class="row">
      <div class="col-lg-12">
        <h2>{{ $link->title }}</a></h2>
        <p><a href="javascript:editLink({{ $link->id }})">Modifier</a></p>
        <p>URL : {{ $link->url }}</p>
        <div>
          {{ Helper::rawToHTML($link->description) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop