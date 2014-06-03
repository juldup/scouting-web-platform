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
  Gestion des réinscriptions
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-reregistration.js') }}"></script>
  <script>
    var reregisterMemberURL = "{{ URL::route('ajax_reregister') }}";
    var unreregisterMemberURL = "{{ URL::route('ajax_cancel_reregistration') }}";
    var deleteMemberURL = "{{ URL::route('ajax_delete_member') }}";
  </script>
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
  
  @include('subviews.contextualHelp', array('help' => 'edit-reregistrations'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'reregistration'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Réinscription des membres actifs {{{ $user->currentSection->de_la_section }}}</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <table class="table table-striped table-hover wide-table">
        <tbody>
          @foreach ($active_members as $member)
            <?php $unreregistered = $member->isReregistered() ? " style='display: none;' " : "" ?>
            <?php $reregistered = $member->isReregistered() ? "" : " style='display: none;' " ?>
            <tr class="member-row" data-member-id="{{ $member->id }}">
              <th class="space-on-right">
                <span class="member-name">
                  {{{ $member->first_name }}} {{{ $member->last_name }}}
                </span>
                <span class="reregistered" {{ $reregistered }}>
                  est réinscrit
                </span>
              </th>
              <td>
                <a class='btn-sm btn-primary unreregistered reregister-member-button' href="" {{ $unreregistered }}>
                  Réinscrire
                </a>
              </td>
              <td>
                <a class='btn-sm btn-warning unreregistered delete-member-button' href="" {{ $unreregistered }}>
                  Désinscrire
                </a>
              </td>
              <td>
                <a class='btn-sm btn-default cancel-reregistration-button reregistered' href="" {{ $reregistered }}>
                  Annuler la réinscription
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@stop