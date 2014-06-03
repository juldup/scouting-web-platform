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
  Utilisateurs du site
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-users.js') }}"></script>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'user-list'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Liste des utilisateurs du site</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Utilisateur</th>
            <th>Adresse e-mail</th>
            <th>Dernière visite</th>
            @if ($can_delete)
              <th></th>
            @endif
          </tr>
        </thead>
        <tbody>
          @foreach ($users as $userInstance)
            <tr>
              <td>{{{ $userInstance->username }}}</td>
              <td>{{{ $userInstance->email }}}</td>
              <td>{{ date('d/m/Y', $userInstance->last_visit) }}</td>
              @if ($can_delete)
                <td>
                  <a class="btn-sm btn-danger warning-delete" href="{{ URL::route('delete_user', array('user_id' => $userInstance->id)) }}">Supprimer</a>
                </td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@stop
