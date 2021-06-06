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
    @foreach ($registrations as $registrationList)
      @foreach ($registrationList as $member)
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
          'leader_role_in_contact_page': {{ $member->leader_role_in_contact_page ? "true" : "false" }},
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

@section('forward_links')
  @if ($user->can(Privilege::$EDIT_LISTING_ALL, 1))
    <p>
      <a href="{{ URL::route('create_temporary_registration_link', array('section_slug' => $user->currentSection->slug)) }}">
        Créer un lien d'inscription temporaire
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
      'edit_photo' => true,
      'edit_leader' => true), array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
  
  <div class="row">
    <div class="col-md-12">
      <h1>Nouvelles inscriptions en attente</h1>
      <div class="text-right">
        <a href="{{ URL::route('recompute_years_in_section') }}" class="btn btn-default">Recalculer les années dans les sections</a>
        <a href="{{ URL::route('download_registration_list') }}" class="btn btn-default">Télécharger la liste au format CSV</a>
      </div>
      @foreach ($registrations as $category => $registrationList)
        <h2>{{{ $category }}}</h2>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th></th>
              <th title="Préinscrit"><span class="glyphicon glyphicon-ok"></span></th>
              <th>Nom</th>
              <th>Prénom</th>
              <th title="Numéro d'ordre par sexe">Ordre</th>
              <th title="Inscription en tant qu'animateur">Animateur</th>
              <th title="A des frères et sœurs déjà inscrits dans l'unité">Fratrie</th>
              <th title="Habite à {{{ Parameter::get(Parameter::$REGISTRATION_PRIORITY_CITY) }}}">{{{ Parameter::get(Parameter::$REGISTRATION_PRIORITY_CITY) }}}</th>
              <th title="Enfant d'ancien animateur">Ancien</th>
              <th title="Date d'inscription">Date</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($registrationList as $member)
              <tr class="member-row" data-member-id="{{ $member->id }}">
                <td class="space-on-right">
                  <a class="btn-sm btn-primary" href="javascript:editRegistration({{ $member->id }})">
                    Inscrire
                  </a>
                  &nbsp;
                  <a class="btn-sm btn-danger delete-registration-button"
                     href="{{ URL::route('edit_delete_registration', array('member_id' => $member->id)) }})"
                     onclick="confirm('Supprimer cette demande d\'inscription ?')">
                    Supprimer
                  </a>
                  &nbsp;
                  <a class="btn-sm btn-default" href="javascript:editRegistrationPriority({{ $member->id }})">
                    <span class='glyphicon glyphicon-pencil'></span>
                  </a>
                </td>
                <td>
                  {{ $member->in_waiting_list ? "<strong>Oui</strong>" : "Non" }}
                </td>
                <td class="space-on-right">{{{ $member->last_name }}}</td>
                <td class="space-on-right">{{{ $member->first_name }}}</td>
                <td>{{{ $member->gender == "M" ? "M-" : "F-" }}}{{{ $member->gender_order }}}</td>
                <td>{{{ $member->is_leader ? "Oui" : "Non" }}}</td>
                <td>
                  @if ($member->registration_siblings)
                    Oui <span class="glyphicon glyphicon-info-sign" title="{{{ $member->registration_siblings }}}"></span>
                  @else
                    Non
                  @endif
                </td>
                <td>
                  @if (strpos(Helper::slugify($member->city),
                       Helper::slugify(Parameter::get(Parameter::$REGISTRATION_PRIORITY_CITY))) !== false)
                    Oui <span class="glyphicon glyphicon-info-sign" title="{{{ $member->city }}}"></span>
                  @else
                    Non <span class="glyphicon glyphicon-info-sign" title="{{{ $member->city }}}"></span>
                  @endif
                </td>
                <td>
                  @if ($member->registration_former_leader_child)
                    Oui <span class="glyphicon glyphicon-info-sign" title="{{{ $member->registration_former_leader_child }}}"></span>
                  @else
                    Non
                  @endif
                </td>
                <td>
                  @if ($member->registration_priority)
                    <span class="glyphicon glyphicon-star danger"></span>
                  @endif
                  {{{ Helper::dateToHuman($member->registration_date) }}}
                  <span class="glyphicon glyphicon-info-sign" title="{{{ substr($member->registration_date,11) }}}"></span>
                </td>
              </tr>
              <div style="display: none;" class="advanced-registration-edit" id="advanced-registration-edit-{{ $member->id }}">
                <div class="advanced-registration-edit-panel form-horizontal">
                  <div class="close-button">
                    <span class="glyphicon glyphicon-remove"></span>
                  </div>
                  {{ Form::open(array('url' => URL::route('submit_registration_priority', array('section_slug' => $user->currentSection->slug)))) }}
                    {{ Form::hidden('member_id', $member['id']) }}
                    <div class="form-group">
                      {{ Form::label('', "Nom", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8 form-side-note">
                        {{{ $member->getFullName() }}}
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-md-4 control-label">
                        {{ Form::label('in_waiting_list', "Préinscription", array("class" => "")) }}
                      </div>
                      <div class="col-md-8">
                        {{ Form::checkbox('in_waiting_list', 1, $member->in_waiting_list ? 1 : 0, ['class' => "no-bootstrap-switch"]) }}
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('section_category', "Section", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-2">
                        @if (Parameter::get(Parameter::$ADVANCED_REGISTRATIONS) && Parameter::get(Parameter::$REGISTRATION_GENERIC_SECTIONS))
                          {{ Form::select('section_category', Section::getExistingCategoriesForSelect(), $member->registration_section_category, array('class' => 'form-control')) }}
                        @else
                          {{ Form::select('section', Section::getSectionsForSelect(), $member->section_id, array('class' => 'form-control')) }}
                        @endif
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('registration_is_leader', "Animateur", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8">
                        {{ Form::checkbox('registration_is_leader', 1, $member->is_leader ? 1 : 0, ['class' => "no-bootstrap-switch"]) }}
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('year_in_section', "Année dans la section", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-2">
                        {{ Form::text('year_in_section', $member->year_in_section, array('class' => 'form-control')) }}
                      </div>
                      <div class="col-md-3 form-side-note">
                        (Date de naissance : {{ Helper::dateToHuman($member->birth_date) }})
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('registration_siblings', "Frères et sœurs dans l'unité", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8">
                        {{ Form::text('registration_siblings', $member->registration_siblings, array('class' => 'form-control')) }}
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('registration_city', "Localité", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8">
                        {{ Form::text('registration_city', $member->city, array('class' => 'form-control')) }}
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('registration_former_leader_child', "Enfant d'ancien animateur", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8">
                        {{ Form::text('registration_former_leader_child', $member->registration_former_leader_child, array('class' => 'form-control')) }}
                      </div>
                    </div>
                    <div class="form-group">
                      {{ Form::label('registration_date', "Date de la demande d'inscription", array("class" => "col-md-4 control-label")) }}
                      <div class="col-md-8">
                        {{ Form::text('registration_date', $member->registration_date, array('class' => 'form-control')) }}
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-md-4 control-label">
                        {{ Form::label('registration_priority', "Inscription prioritaire", array("class" => "")) }}
                        <span class="glyphicon glyphicon-star danger"></span>
                      </div>
                      <div class="col-md-8">
                        {{ Form::checkbox('registration_priority', 1, $member->registration_priority ? 1 : 0, ['class' => "no-bootstrap-switch"]) }}
                      </div>
                    </div>
                    <div class='form-group'>
                      <div class="col-md-2 col-md-offset-4">
                        {{ Form::submit('Appliquer', ["class" => "btn btn-primary"]) }}
                      </div>
                    </div>
                  {{ Form::close() }}
                </div>
              </div>
            @endforeach
          </tbody>
        </table>
      @endforeach
      @if (!count($registrations))
        <p>Il n'y a aucune demande d'inscription en attente.</p>
      @endif
      
    </div>
  </div>
@stop