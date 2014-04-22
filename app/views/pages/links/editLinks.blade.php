@extends('base')

@section('additional_javascript')
  <script src="{{ asset('js/edit-links.js') }}"></script>
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
      
      <div id="link_form" class="form-horizontal well"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('edit_links_submit'))) }}
          {{ Form::hidden('link_id', 0) }}
          <div class="form-group">
            {{ Form::label('link_title', "Nom du lien", array('class' => "col-md-2 control-label")) }}
            <div class="col-md-5">
              {{ Form::text('link_title', null, array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('link_url', "URL de la page", array('class' => "col-md-2 control-label")) }}
            <div class="col-md-5">
              {{ Form::text('link_url', null, array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('link_description', "Description", array('class' => "col-md-2 control-label")) }}
            <div class="col-md-5">
              {{ Form::textarea('link_description', null, array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5 col-md-offset-2">
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
              <a class="btn btn-danger" id='delete_link' style="display: none;" href="">Supprimer</a>
              <a class="btn btn-default" href="javascript:dismissLinkForm()">Fermer</a>
            </div>
          </div>
        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a class="btn btn-default" href="javascript:addLink()">Ajouter un nouveau lien</a></p>
    </div>
  </div>
  
  @foreach ($links as $link)
    <div class="row well">
      <div class="col-lg-12">
        <legend>
          <div class="row">
            <div class="col-md-10">
              {{{ $link->title }}} : {{{ $link->url }}}
            </div>
            <div class="col-md-2 text-right">
              <a class="btn-sm btn-default" href="javascript:editLink({{ $link->id }})">Modifier</a></p>
            </div>
          </div>
        </legend>
        <div>
          {{ trim($link->description) ? Helper::rawToHTML($link->description) : "(Pas de description)" }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop