@extends('base')

@section('title')
  Formulaire d'inscription
@stop

@section('additional_javascript')
  <script src="{{ URL::to('/') }}/js/registration-form.js"></script>
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
      <p>
        Ce formulaire ne fait pas office d'inscription. Avant de le remplir, il est
        indispensable de prendre contact avec l'animateur d'unité. L'inscription de
        votre enfant dans l'unité ne sera effective qu'après une confirmation de la
        part de l'animateur d'unité, et après le paiement de la cotisation.
      </p>
      <p>
        Il est inutile de remplir ce formulaire pour un membre étant déjà inscrit.
      </p>
      <h2>Remplissez le formulaire</h2>
      @if (Session::has('error_message'))
        <p class='alert alert-danger'>{{ Session::get('error_message'); }}</p>
      @endif
      <p>Dans ce formulaire, "<em>le scout</em>" signifie "<em>le jeune que vous êtes en train d'inscrire</em>".</p>
    </div>
  </div>
      
      
      
  <div class="row">
    <div class="col-md-12">
      <div class="well">
        <div id="registration_form">
          {{ Form::open(array('url' => URL::route('registration_form_submit'), 'class' => 'form-horizontal')) }}
          
          <legend>Identité du scout</legend>
          
          <div class='form-group'>
            {{ Form::label('first_name', "Prénom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('first_name', '', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('last_name', "Nom", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('last_name', '', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('birth_date', "Date de naissance", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('birth_date_day', '', array('class' => 'small form-control', 'placeholder' => 'Jour')) }} /
              {{ Form::text('birth_date_month', '', array('class' => 'small form-control', 'placeholder' => 'Mois')) }} /
              {{ Form::text('birth_date_year', '', array('class' => 'small form-control', 'placeholder' => 'Année')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('gender', "Sexe", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('gender', array('M' => 'Garçon', 'F' => 'Fille'), 'M', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('nationality', "Nationalité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-8">
              {{ Form::text('nationality', 'BE', array('class' => 'small form-control')) }}
            </div>
          </div>
          
          <legend>Adresse</legend>
          
          <div class='form-group'>
            {{ Form::label('address', "Rue et numéro", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('address', '', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('postcode', "Code postal", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('postcode', '', array('class' => 'small form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('city', "Localité", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('city', '', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <legend>Contact</legend>
          
          <div class="row">
            {{ Form::label('phone1', "Téléphone/GSM des parents", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('phone1', '', array('placeholder' => "Numéro principal", 'class' => "form-control medium")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone1_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone1_private') }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone1_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone1_owner', '', array('placeholder' => "Ex: maison", 'class' => "medium form-control")) }}
            </div>
          </div>
          <div class="row">
            <div class="col-md-9 col-md-offset-3">
              {{ Form::text('phone2', '', array('placeholder' => "Autre numéro", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone2_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone2_private') }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone2_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone2_owner', '', array('placeholder' => "Ex: gsm maman", 'class' => "medium form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-9 col-md-offset-3">
              {{ Form::text('phone3', '', array('placeholder' => "Autre numéro", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone3_private') }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone3_owner', 'Téléphone de', array('class' => 'control-label')) }}
              {{ Form::text('phone3_owner', '', array('placeholder' => "Ex: gsm papa", 'class' => "medium form-control")) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('phone_member', "GSM du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              {{ Form::text('phone_member', '', array('placeholder' => "GSM du scout", 'class' => "medium form-control")) }}
              <span class='horiz-divider'></span>
              {{ Form::label('phone_member_private', "Confidentiel (*) :", array('class' => 'control-label')) }}
              {{ Form::checkbox('phone_member_private') }}
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('email_member', "Adresses e-mail des parents", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('email1', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note">Il est recommandé de donner une adresse e-mail.</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 col-md-offset-3">
              {{ Form::text('email2', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note">Les adresses e-mail resteront toujours confidentielles (*).</p>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-4 col-md-offset-3">
              {{ Form::text('email3', '', array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class="form-group">
            {{ Form::label('email_member', "Adresse e-mail du scout", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('email_member', '', array('placeholder' => "", 'class' => 'form-control')) }}
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
            {{ Form::label('section', "Section", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::select('section', Section::getSectionsForSelect(), $user->currentSection->id, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('totem', "Totem", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('totem', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note">Si le scout a déjà été totémisé précédemment.</p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('quali', "Quali", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('quali', '', array('class' => 'form-control')) }}
            </div>
            <div class="col-md-5">
              <p class="form-side-note">Si le scout a déjà été qualifié précédemment.</p>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('is_leader', "Inscription d'un animateur", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-4">
              <div class="checkbox">
                {{ Form::checkbox('is_leader') }}
              </div>
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
          
          <div class='row'>
            {{ Form::label('has_handicap', "Handicap", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-8">
              <div class="checkbox">
                {{ Form::checkbox('has_handicap') }}
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
              {{ Form::textarea('handicap_details', '', array('placeholder' => "Détails du handicap", 'rows' => 3, 'class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('comments', "Commentaires", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-6">
              {{ Form::textarea('comments', '', array('placeholder' => "Toute information utile à partager aux animateurs (sauf les informations médicales que vous serez invité à indiquer dans une fiche santé).", 'rows' => 3, 'class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='row'>
            {{ Form::label('family_in_other_units', "Famille dans d'autres unités", array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-2">
              {{ Form::select('family_in_other_units', Member::getFamilyOtherUnitsForSelect(), '', array('class' => 'form-control')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6 col-md-offset-3">
              {{ Form::textarea('family_in_other_units_details', '',
                        array('placeholder' => "Si le scout a des frères et sœurs dans une autre unité, " .
                                               "cela peut entrainer une réduction de la cotisation. Indiquer " .
                                               "ici qui et dans quelle(s) unité(s).", 'rows' => 3, 'class' => 'form-control')) }}
            </div>
          </div>
          
      <legend>Terminer l'inscription</legend>
      
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