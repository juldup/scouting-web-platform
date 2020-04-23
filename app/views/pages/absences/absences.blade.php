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
  Absence
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/absences.js') }}"></script>
@stop

@section('forward_links')
  @if ($can_manage)
  <p>
    <a href="{{ URL::route('manage_absences') }}">
      Gérer les absences
    </a>
  </p>
  @endif
@stop

@section('content')

  <div class="row">
    <div class="col-md-12">
      <h1>Absences</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @if (count($members))
    <div class="row">
      <div class="col-md-12">
        <p>
          Cliquez sur le nom du scout concerné pour afficher le formulaire et signaler une absence à un événement.
        </p>
      </div>
    </div>
    @foreach ($members as $member)
      <button class="btn-primary form-control absence-member-button" data-member-id='{{ $member['id'] }}'>
        {{{ $member['full_name'] }}}
      </button>
    @endforeach
    @foreach ($members as $member)
      <div class="form-horizontal well absence-form" id='form-{{ $member['id'] }}' style='display: none;'>
        {{ Form::open(array('url' => URL::route('submit_absence', array('section_slug' => $user->currentSection->slug)))) }}
          {{ Form::hidden('member_id', $member['id']) }}
          <div class='form-group'>
            {{ Form::label('', "Nom du scout", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-8 form-side-note">
              {{ $member['full_name'] }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('event_id' . $member['id'], "Activité", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-8">
              {{ Form::select('event_id' . $member['id'], ['' => 'Sélectionnez une activité'] + $member['events'], '', array('class' => 'form-control select-event')) }}
              {{ Form::text('other_event' . $member['id'], '', array('class' => 'form-control input-other-event', 'placeholder' => "Nom et date de l'activité", 'style' => 'display: none;')) }}
            </div>
          </div>
          <div class="form-group">
            {{ Form::label('explanation' . $member['id'], "Justification", array("class" => "col-md-2 control-label")) }}
            <div class="col-md-8">
              {{ Form::textarea('explanation' . $member['id'], '', array('class' => 'form-control', 'rows' => 3)) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-8 col-md-offset-2">
              {{ Form::submit('Envoyer', array('class' => 'btn-primary form-control medium enabled-submit', 'style' => 'display: none;')) }}
              <button class='btn-disabled form-control medium disabled-submit' disabled="disabled">Envoyer</button>
            </div>
          </div>
        {{ Form::close() }}
      </div>
    @endforeach
  @else
    {{-- No members --}}
    @include('subviews.limitedAccess')
  @endif
  
@stop
