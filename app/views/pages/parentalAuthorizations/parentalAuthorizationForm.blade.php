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
  Formulaire fiche santé
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('additional_javascript')
  <script src="{{ asset('js/health-card.js') }}"></script>
@stop

@section('back_links')
  <p>
    <a href='{{ URL::route('health_card') }}'>
      Retour à la liste
    </a>
  </p>
@stop

@section('content')
  
  @if ($health_card)
    <?php
      $member = $health_card->getMember();
      $il = ($member->gender == 'M' ? "il" : "elle");
    ?>
    <div class="row">
      <div class="col-md-12">
        <h1>Fiche santé de {{{ $member->first_name }}} {{{ $member->last_name }}}</h1>
        
        @include('subviews.flashMessages')
        
        <div class="well form-horizontal">
          {{ Form::model($health_card, array('url' => URL::route('health_card_submit'))) }}
          {{ Form::hidden('member_id') }}
          <legend>Identité du scout</legend>
          
          <div class='col-md-6'>
            
            <div class='row'>
              {{ Form::label('', 'Nom', array('class' => 'col-md-4 control-label')) }}
              <div class="col-md-4">
                <p class='form-side-note'>
                  {{{ $member->first_name }}} {{{ $member->last_name }}}
                </p>
              </div>
            </div>
            
            <div class='form-group'>
              {{ Form::label('', 'Adresse', array('class' => 'col-md-4 control-label')) }}
              <div class="col-md-4">
                <p class='form-side-note'>
                  {{{ $member->address }}} <br />
                  {{{ $member->postode }}} {{{ $member->city }}}
                </p>
              </div>
            </div>
            
          </div>
          <div class='col-md-6'>
            
            <div class='row'>
              {{ Form::label('', 'Né le', array('class' => 'col-md-4 control-label')) }}
              <div class="col-md-4">
                <p class='form-side-note'>
                  {{ $member->getHumanBirthDate() }}
                </p>
              </div>
            </div>
            
            <div class='form-group'>
              {{ Form::label('', 'Téléphone', array('class' => 'col-md-4 control-label')) }}
              <div class="col-md-4">
                <p class='form-side-note'>
                  {{{ $member->getPersonalPhone() }}}
                </p>
              </div>
            </div>
            
          </div>
          
          <legend>Personnes de contact et médecin traitant</legend>
          
          <div class='form-group'>
            <div class="col-md-6">
              <label>Personne 1 à contacter en cas d'urgence</label>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('contact1_name', 'Nom', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('contact1_name', null, array('class' => 'form-control')) }}
            </div>
            {{ Form::label('contact1_relationship', 'Lien de parenté', array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-3">
              {{ Form::text('contact1_relationship', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('contact1_address', 'Adresse', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::textarea('contact1_address', null, array('class' => 'form-control', 'rows' => 2)) }}
            </div>
            {{ Form::label('contact1_phone', 'Téléphone', array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-3">
              {{ Form::text('contact1_phone', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <hr>
          
          <div class='form-group'>
            <div class="col-md-6">
              <label>Personne 2 à contacter en cas d'urgence</label>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('contact2_name', 'Nom', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('contact2_name', null, array('class' => 'form-control')) }}
            </div>
            {{ Form::label('contact2_relationship', 'Lien de parenté', array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-3">
              {{ Form::text('contact2_relationship', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('contact2_address', 'Adresse', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::textarea('contact2_address', null, array('class' => 'form-control', 'rows' => 2)) }}
            </div>
            {{ Form::label('contact2_phone', 'Téléphone', array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-3">
              {{ Form::text('contact2_phone', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <hr>
          
          <div class='form-group'>
            <div class="col-md-6">
              <label>Médecin traitant</label>
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('doctor_name', 'Nom', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::text('doctor_name', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <div class='form-group'>
            {{ Form::label('doctor_address', 'Adresse', array('class' => 'col-md-1 col-md-offset-1 control-label')) }}
            <div class="col-md-4">
              {{ Form::textarea('doctor_address', null, array('class' => 'form-control', 'rows' => 2)) }}
            </div>
            {{ Form::label('doctor_phone', 'Téléphone', array('class' => 'col-md-2 control-label')) }}
            <div class="col-md-3">
              {{ Form::text('doctor_phone', null, array('class' => 'form-control')) }}
            </div>
          </div>
          
          <legend>Informations confidentielles concernant la santé du participant</legend>
          
          <div class="form-group">
            <div class="col-md-12">
              <p>
                <label>
                  1. Peut-{{ $il }} prendre part à toutes les activités proposées ? (sport, excursions, jeux, natation, ...)
                </label>
                <span class='horiz-divider'></span>{{ Form::checkbox('has_no_constrained_activities') }}
              </p>
            </div>
            <div class="col-md-12">
              <p>
                Si non, raisons et détails d'une éventuelle non-participation : 
              </p>
              {{ Form::textarea('constrained_activities_details', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                2. Y a-t-il des données médicales spécifiques importantes à connaitre pour le bon déroulement des
                activités ? (ex.&nbsp;: problèmes cardiaques, épilepsie, asthme, diabète, mal des transports,
                rhumatisme, somnambulisme, affections cutanées, handicap moteur ou mental...)  <br />
                Indiquez la fréquence, la gravité et les actions à mettre en &#339;uvre pour les éviter et/ou y réagir.
              </label>
            </div>
            <div class="col-md-12">
              {{ Form::textarea('medical_data', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                3. Quelles sont les maladies ou les interventions médicales qu'{{ $il }} a dû subir (+ années respectives) ? (rougeole, appendicite...)
              </label>
            </div>
            <div class="col-md-12">
              {{ Form::textarea('medical_history', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <p>
                <label>
                  4. Est-{{ $il }} en ordre de vaccination contre le tétanos ?
                </label>
                <span class='horiz-divider'></span>{{ Form::checkbox('has_tetanus_vaccine') }}
              </p>
            </div>
            <div class="col-md-12">
              <p>
                Date du dernier rappel :
              </p>
              {{ Form::text('tetanus_vaccine_details', null, array('class' => 'form-control', 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <p>
                <label>
                  5. Est-{{ $il }} allergique à certaines substances, aliments ou médicaments ?
                </label>
                <span class='horiz-divider'></span>{{ Form::checkbox('has_allergy') }}
              </p>
            </div>
            <div class="col-md-12">
              <p>
                Si oui, lesquels ?
              </p>
              {{ Form::textarea('allergy_details', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
            <div class="col-md-12">
              <p class="form-side-note">
                Quelles en sont les conséquences ?
              </p>
              {{ Form::textarea('allergy_consequences', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <p>
                <label>
                  6. A-t-{{ $il }} un régime alimentaire particulier ?
                </label>
                <span class='horiz-divider'></span>{{ Form::checkbox('has_special_diet') }}
              </p>
            </div>
            <div class="col-md-12">
              <p>
                Si oui, lequel ?
              </p>
              {{ Form::textarea('special_diet_details', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                7. Y a-t-il d'autres renseignements concernant le scout que vous jugez importants ?
                (problèmes de sommeil, incontinence nocturne, problèmes psychiques ou physiques, 
                port de lunettes ou appareil auditif...)
              </label>
            </div>
            <div class="col-md-12">
              {{ Form::textarea('other_important_information', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <hr>
          
          <div class="form-group">
            <div class="col-md-12">
              <p>
                <label>
                  8. Doit-{{ $il }} prendre des médicaments pendant les week-ends et/ou le camp ?
                </label>
                <span class='horiz-divider'></span>{{ Form::checkbox('has_drugs') }}
              </p>
            </div>
            <div class="col-md-12">
              <p>
                Si oui, lesquels, quand et en quelle quantité ?
              </p>
              {{ Form::textarea('drugs_details', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
            <div class="col-md-12">
              <p class="form-side-note">
                Est-{{ $il }} autonome dans la prise de ces médicaments ? (Nous rappelons que les médicaments
                ne peuvent pas être partagés entre les participants)
                <span class='horiz-divider'></span>{{ Form::checkbox('drugs_autonomy') }}
              </p>
              
            </div>
          </div>
          
          <legend>Autres informations et commentaires</legend>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                Commentaires
              </label>
            </div>
            <div class="col-md-12">
              {{ Form::textarea('comments', null, array('class' => 'form-control', 'rows' => 2, 'placeholder' => "Néant")) }}
            </div>
          </div>
          
          <legend>Remarque</legend>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                <p>
                  Les animateurs disposent d'une boite de premiers soins.  Dans le cas de situations
                  ponctuelles ou dans l'attente de l'arrivée du médecin, ils peuvent administrer les
                  médicaments suivants et ce à bon escient&nbsp;:
                </p>
                <p>
                  <em>
                    Paracétamol, lopéramide (plus de 6 ans), crème à l'arnica, crème Euceta&reg; ou
                    Calendeel&reg;, désinfectant (Cédium&reg; ou Isobétadine&reg;), Flamigel&reg;.
                  </em>
                </p>
              </label>
            </div>
          </div>
          
          <legend>Validation</legend>
          
          <div class="form-group">
            <div class="col-md-12">
              <label>
                <em>
                  &laquo;&nbsp;Je marque mon accord pour que la prise en charge ou les traitements
                  estimés nécessaires soient entrepris durant le séjour de mon enfant par le responsable
                  de centre de vacances ou par le service médical qui y est associé.  J'autorise le
                  médecin local à prendre les décisions qu'il juge urgentes et indispensables pour assurer
                  l'état de santé de l'enfant, même s'il s'agit d'une intervention chirurgicale à défaut
                  de pouvoir être contacté personnellement.&nbsp;&raquo;</i> <br />
                </em>
              </label>
            </div>
            {{ Form::label('', 'Dernière mise à jour', array('class' => 'col-md-3 control-label')) }}
            <div class="col-md-9">
              <p class="form-side-note">
                {{ Helper::dateToHuman($health_card->signature_date) ? Helper::dateToHuman($health_card->signature_date) : "-" }}
              </p>
            </div>
            
            {{ Form::label('', 'Signature', array('class' => 'col-md-3 control-label')) }}
            
            <div class="col-md-9">
              {{ Form::submit('Enregistrer et signer la fiche santé', array('class' => 'btn btn-primary')) }}
              <span class="horiz-divider"></span>Cliquer sur ce bouton a la valeur d'une signature.
            </div>
          </div>
          
          
        </div>
      </div>
    </div>
  @endif
  
@stop