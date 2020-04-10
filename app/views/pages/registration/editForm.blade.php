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
  Modifier le formulaire d'inscription
@stop

@section('additional_javascript')
  <script src="{{ asset('js/registration-form.js') }}"></script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('registration_form') }}'>
      Retour au formulaire
    </a>
  </p>
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'edit-registration-form'))
  
  {{ Form::open(array('url' => URL::route('edit_registration_form_submit'), 'class' => 'form-horizontal')) }}
  <div class="row">
    <div class="col-md-12">
      <h1>Formulaire d'inscription</h1>
      @if (Session::has('success_message'))
        <p class='alert alert-success'>{{ Session::get('success_message'); }}</p>
      @endif
      {{ Form::textarea('introduction', $data['introduction'], array('rows' => 5, 'class' => 'form-control edit-form-field')) }}
      <h2>Remplissez le formulaire</h2>
      @if (Session::has('error_message'))
        <p class='alert alert-danger'>{{ Session::get('error_message'); }}</p>
      @endif
      {{ Form::textarea('fill-in-form', $data['fill-in-form'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
      <p></p>      
      
      <div class="well">
        <div id="registration_form">
          
          <legend>Identité du scout</legend>
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('identity', $data['identity'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('first_name', "Prénom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('first_name', $data['first_name'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('last_name', "Nom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('last_name', $data['last_name'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('birth_date', "Date de naissance", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'small form-control', 'disabled' => 'disabled')) }} /
              {{ Form::text('', '', array('class' => 'small form-control', 'disabled' => 'disabled')) }} /
              {{ Form::text('', '', array('class' => 'small form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('birth_date', $data['birth_date'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('gender', "Sexe", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('', array('M' => '', 'F' => ''), 'M', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('gender', $data['gender'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('nationality', "Nationalité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'small form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('nationality', $data['nationality'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <legend>Adresse</legend>
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('address', $data['address'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('address', "Rue et numéro", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('address_street', $data['address_street'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('postcode', "Code postal", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'small form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('postcode', $data['postcode'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('city', "Localité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('city', $data['city'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <legend>Contact</legend>
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('contact', $data['contact'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-9">
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              {{ Form::label('phone', "Téléphone/GSM des parents", array('class' => 'control-label', 'style' => 'display: block;')) }}
              {{ Form::textarea('phone', $data['phone'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
            <div class="col-md-9">
              {{ Form::text('', '', array('placeholder' => "Numéro principal", 'class' => "form-control medium", 'disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('', '', array('placeholder' => "Ex: maison", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <br />
              {{ Form::text('', '', array('placeholder' => "Autre numéro", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('', '', array('placeholder' => "Ex: gsm maman", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <br />
              {{ Form::text('', '', array('placeholder' => "Autre numéro", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('', '', array('placeholder' => "Ex: gsm papa", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <br />&nbsp;
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('phone_member', "GSM du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('', '', array('placeholder' => "GSM du scout", 'class' => "medium form-control", 'disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::label('', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              <span class='horiz-divider'></span>
              {{ Form::text('phone_member', $data['phone_member'], array('class' => 'form-control very-large edit-form-field')) }}
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('email', "Adresses e-mail des parents", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
              <br />
            </div>
            <div class="col-md-5">
              {{ Form::textarea('email', $data['email'], array('rows' => 4, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('email_member', "Adresse e-mail du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('placeholder' => "", 'class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('email_member', $data['email_member'], array('class' => 'form-control edit-form-field')) }}
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
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('section_header', $data['section_header'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('section', "Section", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('', array(), $user->currentSection->id, array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('section', $data['section'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('totem', "Totem", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('totem', $data['totem'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('quali', "Quali", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('', '', array('class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-5">
              {{ Form::text('quali', $data['quali'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('is_leader', "Inscription d'un animateur", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              <div class="checkbox">
                {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              </div>
            </div>
            <div class="col-md-5">
              {{ Form::text('leader', $data['leader'], array('class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <legend>Remarques particulières</legend>
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('remarks', $data['remarks'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('has_handicap', "Handicap", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              <div class="checkbox">
                {{ Form::checkbox('', 1, '', array('disabled' => 'disabled')) }}
              </div>
              {{ Form::textarea('', '', array('placeholder' => "Détails du handicap", 'rows' => 3, 'class' => 'form-control', 'disabled' => 'disabled')) }}
              <br />
            </div>
            <div class="col-md-4">
              {{ Form::textarea('handicap', $data['handicap'], array('class' => 'form-control edit-form-field', 'rows' => 5)) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('comments', "Commentaires", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::textarea('', '', array('placeholder' => "Toute information utile à partager aux animateurs (sauf les informations médicales que vous serez invité à indiquer dans une fiche santé).", 'rows' => 3, 'class' => 'form-control', 'disabled' => 'disabled')) }}
            </div>
            <div class="col-md-4">
              {{ Form::textarea('comments', $data['comments'], array('class' => 'form-control edit-form-field', 'rows' => 3)) }}
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('family_in_other_units', "Famille dans d'autres unités", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-5">
              {{ Form::select('', array(), '', array('class' => 'form-control medium', 'disabled' => 'disabled')) }}
              <br />
              {{ Form::textarea('', '',
                        array('placeholder' => "Si le scout a des frères et sœurs dans une autre unité, " .
                                               "cela peut entrainer une réduction de la cotisation. Indiquer " .
                                               "ici qui et dans quelle(s) unité(s).", 'rows' => 3,
                                               'class' => 'form-control', 'disabled' => 'disbled')) }}
            </div>
            <div class="col-md-4">
              {{ Form::textarea('family', $data['family'], array('class' => 'form-control edit-form-field', 'rows' => 5)) }}
            </div>
          </div>
          
          <legend>Terminer l'inscription</legend>
          <div class='form-group'>
            <div class="col-md-12">
              {{ Form::textarea('finish', $data['finish'], array('rows' => 3, 'class' => 'form-control edit-form-field')) }}
            </div>
          </div>
          
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
              {{ Form::submit('Enregistrer', array('class' => 'btn btn-primary')) }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{ Form::close() }}
@stop