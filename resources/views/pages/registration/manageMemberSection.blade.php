@extends('base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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
?>

@section('title')
  Gestion des passages de sections
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-change-section.js') }}"></script>
@stop

@section('back_links')
  @if (Parameter::get(Parameter::$SHOW_REGISTRATION))
    <p>
      <a href='{{ URL::route('registration', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour à la page d'inscription
      </a>
    </p>
  @endif
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-member-section'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'change_section'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Faire passer des scouts {{{ $user->currentSection->de_la_section }}} vers d'autres sections</h1>
    </div>
  </div>
  
  @if (count($active_members))
    <div class="row">
      <div class="col-md-12">
        <p class="label-warning label">
          Pense à <a href="{{ URL::route('manage_year_in_section') }}">changer l'année</a> de la section de destination AVANT d'opérer les transferts de sections, car l'année des scouts transférés est mise à 1.
        </p>
      </div>
    </div>
    &nbsp;
    <div class="row">
      <div class="col-md-12">
      @include('subviews.flashMessages')
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-horizontal">
          {{ Form::open(array('url' => URL::route('manage_member_section_submit', array('section_slug' => $user->currentSection->slug)))) }}
            <div class="form-group">
              <div class="col-sm-2 col-sm-offset-7 text-center">
                {{ Form::submit('Enregistrer les transferts', array('class' => 'btn btn-primary submit-button', 'disabled' => 'disabled')) }}
              </div>
            </div>
            <div class="form-group">
              <div class="col-xs-4 col-sm-5 text-right">
                <span class="form-control large text-left">{{{ $user->currentSection->name }}}</span>
              </div>
              <div class="col-xs-4 col-sm-2 text-center">
                <label class="form-side-note">Vers</label>
              </div>
              <div class="col-xs-4 col-sm-5">
                {{ Form::select('destination', Section::getSectionsForSelect(array(1, $user->currentSection)), null, array('class' => 'form-control large section-selector')) }}
              </div>
            </div>
            @foreach ($active_members as $member)
              <div class="form-group member-row">
                <div class="hidden">
                  {{ Form::checkbox('members[' . $member->id . "]", 1, false, array('class' => 'transfered-checkbox')) }}
                </div>
                <div class="col-xs-4 col-sm-5 text-right">
                  <span class="untransfered">
                    <label>{{{ $member->first_name }}} {{{ $member->last_name }}}</label>
                    <span class="horiz-divider"></span>
                  </span>
                  {{{ $member->year_in_section }}}e année
                </div>
                <div class="col-xs-4 col-sm-2 text-center">
                  <a class='btn-sm btn-primary untransfered transfer-button' href="">
                    Faire passer
                  </a>
                  <a class='btn-sm btn-default untransfer-button transfered' href="" style="display: none;">
                    Annuler
                  </a>
                </div>
                <div class="col-xs-4 col-sm-5">
                  <div class="transfered" style="display: none;">
                    <label>{{{ $member->first_name }}} {{{ $member->last_name }}}</label>
                    &rightarrow;
                    <span class="destination-section"></span>
                  </div>
                </div>
              </div>
            @endforeach
          {{ Form::close() }}
        </div>
      </div>
    </div>
  @else
    <p>Il n'y a aucun membre dans cette section.</p>
  @endif
  
@stop