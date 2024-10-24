@extends('base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;
use App\Models\Section;

?>

@section('title')
  Paramètres des sections
@stop

@section('head')
  <meta name="robots" content="noindex">
  @vite(['resources/css/bootstrap-colorpicker.min.css'])
@stop

@section('additional_javascript')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.3/js/bootstrap-colorpicker.min.js"></script>
  @vite(['resources/js/edit-sections.js'])
  @if ($user->can(Privilege::$MANAGE_SECTIONS, 1))
    @vite(['resources/js/reorder-list.js'])
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
        'category': "{{ Helper::sanitizeForJavascript($section->section_category) }}",
        'type': "{{ Helper::sanitizeForJavascript($section->section_type) }}",
        'type_number': "{{ Helper::sanitizeForJavascript($section->section_type_number) }}",
        'color': "{{ Helper::sanitizeForJavascript($section->color) }}",
        'la_section': "{{ Helper::sanitizeForJavascript($section->la_section) }}",
        'de_la_section': "{{ Helper::sanitizeForJavascript($section->de_la_section) }}",
        'subgroup_name': "{{ Helper::sanitizeForJavascript($section->subgroup_name) }}",
        'delete_url': "{{ URL::route('edit_section_delete', array('section_id' => $section->id)) }}",
        'calendar_shortname': "{{ Helper::sanitizeForJavascript($section->calendar_shortname) }}",
        'start_age': "{{ $section->start_age }}",
        'google_calendar_link': "{{ Helper::sanitizeForJavascript($section->google_calendar_link) }}",
        'export_calendar_url': "{{ URL::route('export_calendar', ['section_id' => $section->id]) }}"
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
      
      @if ($user->can(Privilege::$MANAGE_SECTIONS, $section))
        <div id="section_form" class="form-horizontal well"
             @if (!Session::has('_old_input')) style="display: none;" @endif
             >
          {!! Form::open(array('url' => URL::route('edit_section_submit', array('section_slug' => $user->currentSection->slug)))) !!}
            {!! Form::hidden('section_id', 0) !!}
            <legend>Modifier la section</legend>
            <div class="form-group">
              {!! Form::label('section_name', 'Nom', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-7">
                {!! Form::text('section_name', '', array('class' => 'form-control', 'placeholder' => 'ex.: Waingunga')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('section_email', 'Adresse e-mail', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-7">
                {!! Form::text('section_email', '', array('class' => 'form-control')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('section_category', 'Type de section', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-7">
                {!! Form::select('section_category', Section::categoriesForSelect(), '', array('class' => 'form-control')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('section_type', 'Sigle', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-9">
                {!! Form::text('section_type', '', array('class' => 'form-control small', 'placeholder' => 'ex.: B')) !!}
                {!! Form::text('section_type_number', '', array('class' => 'form-control small', 'placeholder' => 'ex.: 1')) !!}
                <span class="horiz-divider"></span>
                <span class="form-side-note">
                  Le sigle fédération de la section&nbsp;: symbole (B, L, E, P...) + numéro (1, 2, 3...)
                </span>
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('section_color', 'Couleur', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-5">
                {!! Form::hidden('section_color', '') !!}
                <p class="form-side-note">
                  <a class="color-sample"></a>
                </p>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-3 control-label">
                {!! Form::label('section_calendar_shortname', 'Préfixe pour le calendrier') !!}
                <p>Précédera le nom de l'activité dans le calendrier d'unité</p>
              </div>
              <div class="col-md-7">
                {!! Form::text('section_calendar_shortname', '', array('class' => 'form-control large', 'placeholder' => "ex.: LOU")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-3 control-label">
                {!! Form::label('section_la_section', '"la section"') !!}
                <p>Utilisé pour compléter certaines phrase du site</p>
              </div>
              <div class="col-md-7">
                {!! Form::text('section_la_section', '', array('class' => 'form-control large', 'placeholder' => "ex.: la meute")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-3 control-label">
                {!! Form::label('section_de_la_section', '"de la section"') !!}
                <p>Utilisé pour compléter certaines phrase du site</p>
              </div>
              <div class="col-md-7">
                {!! Form::text('section_de_la_section', '', array('class' => 'form-control large', 'placeholder' => "ex: de la meute")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class='col-md-3 control-label'>
                {!! Form::label('section_subgroup_name', 'Nom des sous-groupes') !!}
                <br />
                (au singulier)
              </div>
              <div class="col-md-7">
                {!! Form::text('section_subgroup_name', '', array('class' => 'form-control', 'placeholder' => 'ex.: Sizaine')) !!}
              </div>
            </div>
            <div class="form-group">
              <div class='col-md-3 control-label'>
                {!! Form::label('section_start_age', 'Âge minimum') !!}
                <br>
                (nombre entier)
              </div>
              <div class="col-md-7">
                {!! Form::text('section_start_age', '', array('class' => 'form-control', 'placeholder' => 'ex.: 12')) !!}
              </div>
            </div>
            <div class="form-group">
              <div class='col-md-3 control-label'>
                {!! Form::label('google_calendar_link', 'Lien Google Agenda') !!}
              </div>
              <div class="col-md-9">
                {!! Form::text('google_calendar_link', '', array('class' => 'form-control', 'placeholder' => 'ex.: https://calendar.google.com/calendar/u/0/r?cid=1paa6v92kavl29mp97gn20a8h5mrt7e0@import.calendar.google.com')) !!}
                URL du calendrier au format icalendar : <span id='icalendar_link'></span>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-5 col-md-offset-3">
                {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
                <a class='btn btn-danger' id='delete_button' href="">Supprimer</a>
                <a class='btn btn-default dismiss-form'>Fermer</a>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      @else
        <div id="section-form-limited" class="form-horizontal well"
             @if (!Session::has('_old_input')) style="display: none;" @endif
             >
          {!! Form::open(array('url' => URL::route('edit_section_submit', array('section_slug' => $user->currentSection->slug)))) !!}
            {!! Form::hidden('section_id', 0) !!}
            <legend>Modifier la section</legend>
            <div class="form-group">
              {!! Form::label('section_email', 'Adresse e-mail', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-7">
                {!! Form::text('section_email', '', array('class' => 'form-control')) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('section_subgroup_name', 'Nom des sous-groupes', array('class' => 'col-md-3 control-label')) !!}
              <div class="col-md-7">
                {!! Form::text('section_subgroup_name', '', array('class' => 'form-control', 'placeholder' => 'Patrouille, Sizaine, Hutte...')) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-5 col-md-offset-3">
                {!! Form::submit('Enregistrer', array('class' => 'btn btn-primary')) !!}
                <a class='btn btn-default dismiss-form'>Fermer</a>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      @endif
      
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th colspan="5">
              <div class="row">
                <div class="col-xs-3"></div>
                <div class="col-xs-3">Section</div>
                <div class="col-xs-3">Adresse e-mail</div>
                <div class="col-xs-1">Sigle</div>
                <div class="col-xs-1">Couleur</div>
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="draggable-tbody">
          @foreach ($sections as $section)
            <tr data-section-id="{{ $section->id}}" data-draggable-id="{{ $section->id }}" class="draggable-row">
              <td>
                <div class="row">
                  <div class="col-xs-3">
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
                  <div class="col-xs-3">{{{ $section->name }}}</div>
                  <div class="col-xs-3">{{{ $section->email }}}</div>
                  <div class="col-xs-1">{{{ $section->section_type }}}{{{ $section->section_type_number }}}</div>
                  <div class="col-xs-1"><span style="background-color: {{ $section->color }}" class="color-sample"></span></div>
                </div>
                <div class="details_section" data-section-id="{{ $section->id}}" style="display: none;">
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      Préfixe pour le calendrier :
                    </div>
                    <div class="col-xs-9">
                      {{{ $section->calendar_shortname }}}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      "De la section" :
                    </div>
                    <div class="col-xs-9">
                       "Voici les actualités <strong>{{{ $section->de_la_section }}}</strong>."
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      "La section" :
                    </div>
                    <div class="col-xs-9">
                      "Inscriptions pour <strong>{{{ $section->la_section }}}</strong>."
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      Nom des sous-groupes :
                    </div>
                    <div class="col-xs-9">
                      {{{ $section->subgroup_name }}}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      Âge minimum
                    </div>
                    <div class="col-xs-9">
                      {{{ $section->start_age }}} {{ $section->start_age ? "ans" : "-" }}
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xs-3 member-detail-label">
                      Lien Google Agenda
                    </div>
                    <div class="col-xs-9">
                      {{{ $section->google_calendar_link }}}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
        @if ($user->can(Privilege::$MANAGE_SECTIONS, 1))
          <tbody>
            <tr>
              <td>
                <a class="btn-sm btn-primary add-button" href="">
                  Ajouter une nouvelle section
                </a>
              </td>
            </tr>
          </tbody>
        @endif
      </table>
    </div>
  </div>
@stop
