@extends('base')

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_documents.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var documents = new Array();
    @foreach ($documents as $doc)
      documents[{{ $doc->id }}] = {
        'title': "{{ Helper::sanitizeForJavascript($doc->title) }}",
        'description': "{{ Helper::sanitizeForJavascript($doc->description) }}",
        'public': {{ $doc->public ? "true" : "false" }},
        'delete_url': "{{ URL::route('manage_documents_delete', array('document_id' => $doc->id)) }}"
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
      <h1>Documents {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-lg-12'>
      
      <div id="document_form"
           @if (!Session::has('error_message')) style="display: none;" @endif
           >
        {{ Form::open(array('files' => true, 'url' => URL::route('manage_documents_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('doc_id', 0) }}
          <p>
            Titre :
            {{ Form::text('doc_title', '', array('size' => '35', 'placeholder' => "Titre de la nouvelle")) }}
          <p>
            Description :
            {{ Form::textarea('description', '', array('cols' => '35', 'rows' => 3, 'placeholder' => "Contenu de la nouvelle")) }}
          </p>
          <p>
            Public :
            {{ Form::checkbox('public') }}
            CONSEIL : Ne coche pas cette case, sauf si tu es sûr que ce document peut être visible publiquement.
          </p>
          <p>
            Document :
            {{ Form::file('document') }}
          </p>
          <p>
            {{ Form::submit('Enregistrer') }}
            <a id='delete_link' href="">Supprimer</a>
            <a href="javascript:dismissDocumentForm()">Fermer</a>
          </p>

        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-lg-12">
      <h2>Ajouter</h2>
      <a href="javascript:addDocument()">ajouter un nouveau document</a></p>
    </div>
  </div>
  
  @foreach ($documents as $doc)
    <div class="row">
      <div class="col-lg-12">
        <h2>{{ $doc->title }}</h2>
        <p><a href="javascript:editDocument({{ $doc->id }})">Modifier</a></p>
        <div>
          {{ Helper::rawToHTML($doc->description) }}
        </div>
      </div>
    </div>
  @endforeach
  
@stop