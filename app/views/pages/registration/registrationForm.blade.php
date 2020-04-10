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
  Formulaire d'inscription
@stop

@section('additional_javascript')
  <script src="{{ asset('js/registration-form.js') }}"></script>
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_manage)
    <p>
      <a class='button' href='{{ URL::route('manage_registration') }}'>
        Gérer les inscriptions
      </a>
    </p>
  @endif
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_registration_form') }}'>
        Modifier le formulaire
      </a>
    </p>
  @endif
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('registration') }}'>
      Retour à la page d'inscription
    </a>
  </p>
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>Formulaire d'inscription</h1>
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message'); }}</p>
      @endif
      <p class='registration-form-introduction-text'>
        {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_INTRODUCTION)) }}
      </p>
      <h2>Remplissez le formulaire</h2>
      @if (Session::has('error_message'))
        <p class='alert alert-danger'>{{ Session::get('error_message'); }}</p>
      @endif
      <p class='registration-form-introduction-text'>
        {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FILL_IN_FORM)) }}
      </p>
    </div>
  </div>
      
      
      
  <div class="row">
    <div class="col-md-12">
      <div class="well">
        <div id="registration_form">
          {{ Form::open(array('url' => URL::route('registration_form_submit'), 'class' => 'form-horizontal')) }}
          
          <legend>Identité du scout</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_IDENTITY)) }}
          </p>
          
          <div class='form-group'>
            {{ Form::label('first_name', "Prénom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('first_name', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FIRST_NAME)) }}
              </p>
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('last_name', "Nom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('last_name', $default['last_name'], array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_LAST_NAME)) }}
              </p>
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('birth_date', "Date de naissance", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('birth_date_day', '', array('class' => 'small form-control', 'placeholder' => 'Jour')) }} /
              {{ Form::text('birth_date_month', '', array('class' => 'small form-control', 'placeholder' => 'Mois')) }} /
              {{ Form::text('birth_date_year', '', array('class' => 'small form-control', 'placeholder' => 'Année')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_BIRTH_DATE)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('gender', "Sexe", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('gender', array('M' => 'Garçon', 'F' => 'Fille'), 'M', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_GENDER)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('nationality', "Nationalité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('nationality', $default['nationality'], array('class' => 'small form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_NATIONALITY)) }}
              </p>
            </div>
          </div>
          
          <legend>Adresse</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_ADDRESS)) }}
          </p>
          
          <div class='form-group'>
            {{ Form::label('address', "Rue et numéro", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('address', $default['address'], array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_ADDRESS_STREET)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('postcode', "Code postal", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('postcode', $default['postcode'], array('class' => 'small form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_POSTCODE)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('city', "Localité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('city', $default['city'], array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_CITY)) }}
              </p>
            </div>
          </div>

          <legend>Contact</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_CONTACT)) }}
          </p>
          
          <div class="row">
            <div class="col-md-3">
              <p>{{ Form::label('phone1', "Téléphone/GSM des parents", array('class' => 'control-label', 'style' => 'display: block;')) }}</p>
              <p class="registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_PHONE)) }}
              </p>
            </div>
            <div class="col-md-9">
              {{ Form::text('phone1', $default['phone1'], array('placeholder' => "Numéro principal", 'class' => "form-control medium")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone1_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone1_private', 1, $default['phone1_private']) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone1_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone1_owner', $default['phone1_owner'], array('placeholder' => "Ex: maison", 'class' => "medium form-control")) }}
              <br />
              {{ Form::text('phone2', $default['phone2'], array('placeholder' => "Autre numéro", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone2_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone2_private', 1, $default['phone2_private']) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone2_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone2_owner', $default['phone2_owner'], array('placeholder' => "Ex: gsm maman", 'class' => "medium form-control")) }}
              <br />
              {{ Form::text('phone3', $default['phone3'], array('placeholder' => "Autre numéro", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone3_private', 1, $default['phone3_private']) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone3_owner', $default['phone3_owner'], array('placeholder' => "Ex: gsm papa", 'class' => "medium form-control")) }}
              <br />&nbsp;
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('phone_member', "GSM du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('phone_member', '', array('placeholder' => "GSM du scout", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone_member_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone_member_private') }}
              <span class='horiz-divider'></span>
              <span class="registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_PHONE_MEMBER)) }}
              </span>
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('email1', "Adresses e-mail des parents", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('email1', $default['email1'], array('class' => 'form-control')) }}
              {{ Form::text('email2', $default['email2'], array('class' => 'form-control')) }}
              {{ Form::text('email3', $default['email3'], array('class' => 'form-control')) }} <br />
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_EMAIL)) }}
              </p>
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('email_member', "Adresse e-mail du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('email_member', '', array('placeholder' => "", 'class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_EMAIL_MEMBER)) }}
              </p>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-9 col-md-offset-3">
              <p>
                (*) Confidentiel signifie que seuls les animateurs auront accès à l'information.
              </p>
            </div>
          </div>
          
          <legend>Choix de la section</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_SECTION_HEADER)) }}
          </p>
          
          <div class='form-group'>
            {{ Form::label('section', "Section", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('section', Section::getSectionsForSelect(), $user->currentSection->id, array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_SECTION)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('totem', "Totem", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('totem', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_TOTEM)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('quali', "Quali", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('quali', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_QUALI)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('is_leader', "Inscription d'un animateur", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              <div class="checkbox">
                {{ Form::checkbox('is_leader') }}
              </div>
            </div>
            <div class="col-md-5">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_LEADER)) }}
              </p>
            </div>
          </div>
          
          <div class="leader_specific" style="display:none;">
            
            <div class='form-group'>
              {{ Form::label('leader_name', "Nom d'animateur", array('class' => 'col-md-3 control-label')) }}
              <div class="col-md-4">
                {{ Form::text('leader_name', '', array('placeholder' => "Nom utilisé dans sa section", 'class' => 'form-control')) }}
              </div>
            </div>
            
            <div class='form-group'>
              {{ Form::label('leader_in_charge', "Animateur responsable", array('class' => 'col-md-3 control-label')) }}
              <div class="col-md-8">
                <div class="checkbox">
                  {{ Form::checkbox('leader_in_charge') }}
                </div>
              </div>
            </div>
            
            <div class='form-group'>
              {{ Form::label('leader_description', "Description de l'animateur", array('class' => 'col-md-3 control-label')) }}
              <div class="col-md-6">
                {{ Form::textarea('leader_description', '', array('placeholder' => "Petite description qui apparaitra sur la page des animateurs", 'rows' => 3, 'class' => 'form-control')) }}
              </div>
            </div>
            
            <div class='form-group'>
              {{ Form::label('leader_role', "Rôle de l'animateur", array('class' => 'col-md-3 control-label')) }}
              <div class="col-md-6">
                {{ Form::text('leader_role', '', array('placeholder' => "Rôle particulier dans le staff", 'class' => 'form-control')) }}
              </div>
            </div>
            
          </div>
          
          <legend>Remarques particulières</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_REMARKS)) }}
          </p>
          
          <div class='row'>
            {{ Form::label('has_handicap', "Handicap", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              <div class="checkbox">
                {{ Form::checkbox('has_handicap') }}
              </div>
              {{ Form::textarea('handicap_details', '', array('placeholder' => "Détails du handicap", 'rows' => 3, 'class' => 'form-control')) }}
              <br />
            </div>
            <div class="col-md-4">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_HANDICAP)) }}
              </p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('comments', "Commentaires", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::textarea('comments', '', array('placeholder' => "Toute information utile à partager aux animateurs (sauf les informations médicales que vous serez invité à indiquer dans une fiche santé).", 'rows' => 3, 'class' => 'form-control')) }}
            </div>
            <div class="col-md-4">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_COMMENTS)) }}
              </p>
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('family_in_other_units', "Famille dans d'autres unités", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::select('family_in_other_units', Member::getFamilyOtherUnitsForSelect(), $default['family_in_other_units'], array('class' => 'form-control medium')) }}
              {{ Form::textarea('family_in_other_units_details', $default['family_in_other_units_details'],
                        array('placeholder' => "Si le scout a des frères et sœurs dans une autre unité, " .
                                               "cela peut entrainer une réduction de la cotisation. Indiquer " .
                                               "ici qui et dans quelle(s) unité(s).", 'rows' => 3, 'class' => 'form-control')) }}
            </div>
            <div class="col-md-4">
              <p class="form-side-note registration-form-side-information">
                {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FAMILY)) }}
              </p>
            </div>
          </div>
          
      <legend>Terminer l'inscription</legend>
          <p class='registration-form-subsection-information'>
            {{ Helper::rawToHTML(Parameter::get(Parameter::$REGISTRATION_FORM_HELP_FINISH)) }}
          </p>
      
      @if (Parameter::get(Parameter::$SHOW_UNIT_POLICY))
        <div class='form-group'>
          {{ Form::label('policy_agreement', "Engagement", array('class' => 'col-md-3 control-label')) }}
          <div class="col-md-8">
            <p class="form-side-note">
              J'ai pris connaissance de la <a target="_blank" href="{{ URL::route('unit_policy') }}">charte d'unité</a>
              et y adhère entièrement : 
              {{ Form::checkbox('policy_agreement') }}
            </p>
          </div>
        </div>
      @endif
     
      <div class="form-group">
        <div class="col-md-9 col-md-offset-3">
          {{ Form::submit('Inscrire maintenant', array('class' => 'btn btn-primary')) }}
        </div>
      </div>
    {{ Form::close() }}
  </div>
@stop