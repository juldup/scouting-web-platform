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
  Gestion des animateurs
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-leaders.js') }}"></script>
  <script>
    var currentSection = {{ $user->currentSection->id }};
    var leaders = new Array();
    var ownedLeaders = new Array();
    @foreach ($leaders as $leader)
      leaders[{{ $leader->id }}] = @include('subviews.memberToJavascript', array('member' => $leader));
      @if ($user->isOwnerOfMember($leader->id))
        ownedLeaders.push("{{ $leader->id }}");
      @endif
    @endforeach
  </script>
@stop

@section('forward_links')
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('edit_leaders', array('section_slug' => $user->currentSection->slug)) }}'>
      Retour aux animateurs actuels
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-archived-leaders'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Animateurs {{{ $user->currentSection->de_la_section }}} en {{{ $archive }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  @include('subviews.editArchivedLeaderForm', array('form_legend' => "Modifier un animateur", 'submit_url' => URL::route('edit_archived_leaders_submit', array('section_slug' => $user->currentSection->slug, 'archive' => $archive))))
  
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th></th>
            <th>Nom d'animateur</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Photo</th>
            <th>Téléphone</th>
            <th>E-mail</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($leaders as $leader)
            <tr>
              <td>
                <a class="btn-sm btn-primary" href="javascript:editLeader({{ $leader->id }})">
                  Modifier
                </a>
                <a class="btn-sm btn-danger warning-delete"
                   href="{{ URL::route('edit_archived_leaders_delete', array('member_id' => $leader->id, 'section_slug' => $user->currentSection->slug, 'archive' => $archive)) }}">
                  Supprimer
                </a>
              </td>
              <td>{{{ $leader->leader_name }}} @if ($leader->leader_in_charge) (responsable) @endif</td>
              <td>{{{ $leader->last_name }}}</td>
              <td>{{{ $leader->first_name }}}</td>
              <td>
                @if ($leader->has_picture)
                  <img class="leader_picture_mini" alt="Photo de {{{ $leader->leader_name }}}" src="{{ $leader->getPictureURL() }}" />
                @else
                  Pas de photo
                @endif
              </td>
              <td>{{{ $leader->phone_member }}}</td>
              <td>{{{ $leader->email_member }}}</td>
            </tr>
          @endforeach
          <tr>
            <td>
              <a class="btn-sm btn-default" href="javascript:addLeader({{ $user->currentSection->id }})">Ajouter un animateur pour l'année {{{ $archive }}}</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  
  <h2>Animateurs d'autres années</h2>
  @foreach ($archives as $otherArchive)
    <div class="row">
      <div class="col-md-12">
        <p>
          @if ($otherArchive != $archive)
            <a href="{{ URL::route('edit_archived_leaders', array('section_slug' => $user->currentSection->slug, 'archive' => $otherArchive)) }}" class="btn-sm btn-default">
              Année {{{ $otherArchive }}}
            </a>
          @else
            <strong class='btn-sm'>Année {{{ $otherArchive }}}</strong>
          @endif
        </p>
      </div>
    </div>
  @endforeach
  
@stop
