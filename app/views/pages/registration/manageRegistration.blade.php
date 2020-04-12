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
  Gestion des inscriptions
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-registration.js') }}"></script>
  <script>
    var registrations = new Array();
    @foreach ($registrations as $member)
      registrations[{{ $member->id }}] = {
        'first_name': "{{ Helper::sanitizeForJavascript($member->first_name) }}",
        'last_name': "{{ Helper::sanitizeForJavascript($member->last_name) }}",
        'birth_date_day': "{{ Helper::getDateDay($member->birth_date) }}",
        'birth_date_month': "{{ Helper::getDateMonth($member->birth_date) }}",
        'birth_date_year': "{{ Helper::getDateYear($member->birth_date) }}",
        'gender': "{{{ $member->gender }}}",
        'nationality': "{{{ $member->nationality }}}",
        'address': "{{ Helper::sanitizeForJavascript($member->address) }}",
        'postcode': "{{ Helper::sanitizeForJavascript($member->postcode) }}",
        'city': "{{ Helper::sanitizeForJavascript($member->city) }}",
        'has_handicap': {{ $member->has_handicap ? "true" : "false" }},
        'handicap_details': "{{ Helper::sanitizeForJavascript($member->handicap_details) }}",
        'comments': "{{ Helper::sanitizeForJavascript($member->comments) }}",
        'is_leader': {{ $member->is_leader ? "true" : "false" }},
        'leader_name': "{{ Helper::sanitizeForJavascript($member->leader_name) }}",
        'leader_in_charge': {{ $member->leader_in_charge ? "true" : "false" }},
        'leader_description': "{{ Helper::sanitizeForJavascript($member->leader_description) }}",
        'leader_role': "{{ Helper::sanitizeForJavascript($member->leader_role) }}",
        'section_id': {{ $member->section_id }},
        'phone1': "{{ Helper::sanitizeForJavascript($member->phone1) }}",
        'phone1_owner': "{{ Helper::sanitizeForJavascript($member->phone1_owner) }}",
        'phone1_private': {{ $member->phone1_private ? "true" : "false" }},
        'phone2': "{{ Helper::sanitizeForJavascript($member->phone2) }}",
        'phone2_owner': "{{ Helper::sanitizeForJavascript($member->phone2_owner) }}",
        'phone2_private': {{ $member->phone2_private ? "true" : "false" }},
        'phone3': "{{ Helper::sanitizeForJavascript($member->phone3) }}",
        'phone3_owner': "{{ Helper::sanitizeForJavascript($member->phone3_owner) }}",
        'phone3_private': {{ $member->phone3_private ? "true" : "false" }},
        'phone_member': "{{ Helper::sanitizeForJavascript($member->phone_member) }}",
        'phone_member_private': {{ $member->phone_member_private ? "true" : "false" }},
        'email1': "{{ Helper::sanitizeForJavascript($member->email1) }}",
        'email2': "{{ Helper::sanitizeForJavascript($member->email2) }}",
        'email3': "{{ Helper::sanitizeForJavascript($member->email3) }}",
        'email_member': "{{ Helper::sanitizeForJavascript($member->email_member) }}",
        'totem': "{{ Helper::sanitizeForJavascript($member->totem) }}",
        'quali': "{{ Helper::sanitizeForJavascript($member->quali) }}",
        'family_in_other_units': {{{ $member->family_in_other_units }}},
        'family_in_other_units_details' : "{{ Helper::sanitizeForJavascript($member->family_in_other_units_details) }}",
      };
    @endforeach
    var reregisterMemberURL = "{{ URL::route('ajax_reregister') }}";
    var unreregisterMemberURL = "{{ URL::route('ajax_cancel_reregistration') }}";
    var deleteMemberURL = "{{ URL::route('ajax_delete_member') }}";
    var toggleWaitingListURL = "{{ URL::route('ajax_toggle_waiting_list') }}";
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
  
  @include('subviews.contextualHelp', array('help' => 'edit-new-registrations'))
  
  @include('pages.registration.manageRegistrationMenu', array('selected' => 'registration'))
  
  @include('subviews.flashMessages')
  
  <?php echo $__env->make('subviews.editMemberForm', array(
      'form_legend' => "Inscription d'un membre",
      'submit_url' => URL::route('manage_registration_submit', array('section_slug' => $user->currentSection->slug)),
      'edit_identity' => true,
      'edit_section' => true,
      'edit_totem' => true,
      'edit_leader' => true), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  
  <div class="row">
    <div class="col-md-12">
      <h1>Nouvelles inscriptions en attente pour {{{ $user->currentSection->la_section }}}</h1>
      @if (count($registrations))
        <table class="table table-striped table-hover wide-table">
          <thead>
            <tr>
              <th></th>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Animateur</th>
              <th>Liste d'attente</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($registrations as $member)
              <tr class="member-row" data-member-id="{{ $member->id }}">
                <td class="space-on-right">
                  <a class="btn-sm btn-primary" href="javascript:editRegistration({{ $member->id }})">
                    Inscrire
                  </a>
                  &nbsp;
                  <a class="btn-sm btn-danger delete-registration-button" href="{{ URL::route('edit_delete_registration', array('member_id' => $member->id)) }})">
                    Supprimer
                  </a>
                </td>
                <td class="space-on-right">{{{ $member->last_name }}}</td>
                <td class="space-on-right">{{{ $member->first_name }}}</td>
                <td>{{{ $member->is_leader ? "Oui" : "Non" }}}</td>
                <td>
                  <span class="is-in-waiting-list" @if (!$member->in_waiting_list) style='display: none;' @endif>
                    Oui <span class='horiz-divider'></span>
                    <a class="btn-sm btn-default toggle-waiting-list-button" data-in-waiting-list="0">
                      Retirer de la liste d'attente
                    </a>
                  </span>
                  <span class="is-not-in-waiting-list" @if ($member->in_waiting_list) style='display: none;' @endif>
                    Non <span class='horiz-divider'></span>
                    <a class="btn-sm btn-default toggle-waiting-list-button" data-in-waiting-list="1">
                      Ajouter à la liste d'attente
                    </a>
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @else
        @if (count($other_sections))
          <p>Il n'y a pas de demande d'inscription pour {{{ $user->currentSection->la_section }}}.</p>
        @else
          <p>Il n'y a aucune demande d'inscription en attente.</p>
        @endif  
      @endif
      
      @if (count($other_sections))
        <p>Il y a des demandes d'inscription en attente dans d'autres sections :
          <?php $first = true; ?>
          @foreach ($other_sections as $other_section)
            @if ($first) <?php $first = false; ?>
            @else –
            @endif
            <a href='{{ URL::route('manage_registration', array('section_slug' => $other_section->slug)) }}'>
              {{{ $other_section->name}}}
            </a>
          @endforeach
        </p>
      @endif
    </div>
  </div>
@stop