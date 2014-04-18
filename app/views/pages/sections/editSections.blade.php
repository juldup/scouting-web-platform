@extends('base')

@section('title')
  Paramètres des sections
@stop

@section('head')
  <meta name="robots" content="noindex">
  <link media="all" type="text/css" rel='stylesheet' href="{{ URL::to('/') }}/css/bootstrap-colorpicker.min.css"></link>
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/libs/bootstrap-colorpicker.min.js"></script>
  <script src="{{ URL::to('/') }}/js/edit-sections.js"></script>
  @if ($user->can(Privilege::$MANAGE_SECTIONS, 1))
    <script src="{{ URL::to('/') }}/js/reorder-list.js"></script>
    <script>
      var saveSectionOrderURL = "{{ URL::route('ajax_change_section_order') }}";
    </script>
  @endif
  <script>
    var sections = new Array();
    @foreach ($sections as $section)
      sections[{{ $section->id }}] = {
        'name': "{{ Helper::sanitizeForJavascript($section->name) }}",
        'email': "{{ Helper::sanitizeForJavascript($section->email) }}",
        'type': "{{ Helper::sanitizeForJavascript($section->section_type) }}",
        'type_number': "{{ Helper::sanitizeForJavascript($section->section_type_number) }}",
        'color': "{{ Helper::sanitizeForJavascript($section->color) }}",
        'la_section': "{{ Helper::sanitizeForJavascript($section->la_section) }}",
        'de_la_section': "{{ Helper::sanitizeForJavascript($section->de_la_section) }}",
        'subgroup_name': "{{ Helper::sanitizeForJavascript($section->subgroup_name) }}",
        'delete_url': "{{ URL::route('edit_section_delete', array('section_id' => $section->id)) }}"
      };
    @endforeach
  </script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'sections'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Paramètres des sections</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-md-12'>
      
      <div id="section_form" class="form-horizontal well"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('edit_section_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('section_id', 0) }}
          <legend>Modifier la section</legend>
          <div class="form-group">
            {{ Form::label('section_name', 'Nom', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_name', '', array('class' => 'form-control', 'placeholder' => 'ex.: Waingunga')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_email', 'Adresse e-mail', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_email', '', array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_type', 'Sigle', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('section_type', '', array('class' => 'form-control small', 'placeholder' => 'ex.: B')) }}
              {{ Form::text('section_type_number', '', array('class' => 'form-control small', 'placeholder' => 'ex.: 1')) }}
              <span class="horiz-divider"></span>
              <span class="form-side-note">
                Le sigle fédaration de la section&nbsp;: symbole (B, L, E, P...) + numéro (1, 2, 3...)
              </span>
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_color', 'Couleur', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::hidden('section_color', '') }}
              <p class="form-side-note">
                <a class="color-sample"></a>
              </p>
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_la_section', '"la section"', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_la_section', '', array('class' => 'form-control', 'placeholder' => "Utilisé pour compléter certaines phrases sur le site (ex.: la troupe)")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_de_la_section', '"de la section"', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_de_la_section', '', array('class' => 'form-control', 'placeholder' => "Utilisé pour compléter certaines phrases sur le site (ex.: du poste)")) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_subgroup_name', 'Nom des sous-groupes', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_subgroup_name', '', array('class' => 'form-control', 'placeholder' => 'Patrouille, Sizaine, Hutte...')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5 col-md-offset-3">
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
              <a class='btn btn-danger' id='delete_button' href="">Supprimer</a>
              <a class='btn btn-default dismiss-form'>Fermer</a>
            </div>
          </div>
        {{ Form::close() }}
      </div>
      
      <div id="section-form-limited" class="form-horizontal well"
           @if (!Session::has('_old_input')) style="display: none;" @endif
           >
        {{ Form::open(array('url' => URL::route('edit_section_submit', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('section_id', 0) }}
          <legend>Modifier la section</legend>
          <div class="form-group">
            {{ Form::label('section_email', 'Adresse e-mail', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_email', '', array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('section_subgroup_name', 'Nom des sous-groupes', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-7">
              {{ Form::text('section_subgroup_name', '', array('class' => 'form-control', 'placeholder' => 'Patrouille, Sizaine, Hutte...')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-5 col-md-offset-3">
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
              <a class='btn btn-default dismiss-form'>Fermer</a>
            </div>
          </div>
        {{ Form::close() }}
      </div>
      
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover draggable-table">
        <thead>
          <tr>
            <th colspan="5">
              <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-3">Section</div>
                <div class="col-md-3">Adresse e-mail</div>
                <div class="col-md-1">Sigle</div>
                <div class="col-md-1">Couleur</div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sections as $section)
            <tr data-section-id="{{ $section->id}}" data-draggable-id="{{ $section->id }}" class="draggable-row">
              <td>
                <div class="row">
                  <div class="col-md-3">
                    @if ($user->can(Privilege::$MANAGE_SECTIONS, $section))
                      <a class="btn-sm btn-primary edit-button" href="">
                        Modifier
                      </a>
                    @elseif ($user->can(Privilege::$EDIT_SECTION_EMAIL_AND_SUBGROUP, $section))
                      <a class="btn-sm btn-primary edit-limited-button" href="">
                        Modifier
                      </a>
                    @else
                      <a class="btn-sm btn-primary invisible">
                        Modifier
                      </a>
                    @endif
                    <a class="btn-sm btn-default details-button" href="">
                      Détails
                    </a>
                  </div>
                  <div class="col-md-3">{{{ $section->name }}}</div>
                  <div class="col-md-3">{{{ $section->email }}}</div>
                  <div class="col-md-1">{{{ $section->section_type }}}{{{ $section->section_type_number }}}</div>
                  <div class="col-md-1"><span style="background-color: {{ $section->color }}" class="color-sample"></span></div>
                </div>
                <div class="details_section" data-section-id="{{ $section->id}}" style="display: none;">
                  <div class="row">
                    <div class="col-md-3 member-detail-label">
                      "De la section" :
                    </div>
                    <div class="col-md-9">
                       "Voici les e-mails <strong>{{{ $section->de_la_section }}}</strong>."
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3 member-detail-label">
                      "La section" :
                    </div>
                    <div class="col-md-9">
                      "Inscriptions pour <strong>{{{ $section->la_section }}}</strong>."
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3 member-detail-label">
                      Nom des sous-groupes :
                    </div>
                    <div class="col-md-9">
                      {{{ $section->subgroup_name }}}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
          @if ($user->can(Privilege::$MANAGE_SECTIONS, 1))
            <tr>
              <td>
                <a class="btn-sm btn-primary add-button" href="">
                  Ajouter une nouvelle section
                </a>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
  
@stop