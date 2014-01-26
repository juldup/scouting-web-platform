@extends('base')

@section('title')
  Gestion des documents
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/edit_documents.js"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var documents = new Array();
    @foreach ($documents as $doc)
      documents[{{ $doc->id }}] = {
        'title': "{{ Helper::sanitizeForJavascript($doc->title) }}",
        'description': "{{ Helper::sanitizeForJavascript($doc->description) }}",
        'category': "{{ Helper::sanitizeForJavascript($doc->category) }}",
        'public': {{ $doc->public ? "true" : "false" }},
        'filename': "{{ Helper::sanitizeForJavascript($doc->filename) }}",
        'delete_url': "{{ URL::route('manage_documents_delete', array('document_id' => $doc->id)) }}"
      };
    @endforeach
  </script>
@stop

@section('back_links')
  <p>
    <a href='{{ $page_url }}'>
      Retour à la page de téléchargements
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-documents'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Documents {{ $user->currentSection->de_la_section }}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-md-12'>
      
      <div id="document_form" class="form-horizontal well"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('files' => true, 'url' => URL::route('manage_documents_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('doc_id', 0) }}
          <legend>Nouveau document</legend>
          <div class="form-group">
            {{ Form::label('doc_title', 'Titre', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::text('doc_title', '', array('class' => 'form-control', 'placeholder' => "Nom du document")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('description', 'Description', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::textarea('description', '', array('class' => 'form-control', 'rows' => 3, 'placeholder' => "Description du document")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('document', 'Document', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::file('document', array('class' => 'btn btn-default')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('filename', 'Nom du fichier', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::text('filename', '', array('class' => 'form-control', 'placeholder' => 'Laisse ce champ vide pour garder le nom original')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('category', 'Catégorie', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::select('category', $categories, null, array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('public', 'Public', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-1">
              {{ Form::checkbox('public') }}
            </div>
            <div class="col-md-8">
              <p class="form-side-note">
                CONSEIL : "Non", sauf si tu es sûr que ce document peut être visible <strong>publiquement</strong>.
              </p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5 col-md-offset-3">
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
              <a class='btn btn-danger' id='delete_link' style="display: none;" href="">Supprimer</a>
              <a class='btn btn-default' href="javascript:dismissDocumentForm()">Fermer</a>
            </div>
          </div>
          <p>
            
          </p>

        {{ Form::close() }}
      </div>
      
    </div>
  </div>
  
  <div class="row">
    <div class="col-md-4">
      <p>
        <a class='btn btn-primary' href="javascript:addDocument()">
          Ajouter un nouveau document
        </a>
      </p>
    </div>
  </div>
  
  @foreach ($documents_in_categories as $category=>$docs)
    <h3>{{ $category }}</h3>
    @foreach ($docs as $doc)
      <div class="row">
        <div class="col-md-6">
          <div class="well">
            <legend>{{ $doc->title }}</legend>
            <p>
              {{ Helper::rawToHTML($doc->description) }}
            </p>
          </div>
        </div>
        <div class="col-md-6">
          <p>
            <a class="btn btn-primary" href="javascript:editDocument({{ $doc->id }})">
              Modifier
            </a>
            @if ($doc->public)
              <p>
                <span class="label label-warning">
                  Ce document est public ! Il peut être consulté par tous les internautes.
                </span>
              </p>
            @else
              <p>
                Ce document est privé. Il est réservé aux membres de l'unité.
              </p>
            @endif
            <p>
              <strong>Fichier :</strong> {{ $doc->filename }}
            </p>
          </p>
        </div>
      </div>
    @endforeach
  @endforeach
  
@stop