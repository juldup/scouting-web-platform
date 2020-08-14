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
  Paramètres du site
@stop

@section('additional_javascript')
  <script src="{{ asset('js/edit-parameters.js') }}"></script>
@stop

@section('content')
  @include('subviews.contextualHelp', array('help' => 'parameters'))
  
  <div class="row">
    <div class="col-md-12">
      <h1>Paramètres du site</h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <div class='col-md-12'>
      <div id="website-parameters-form" class="form-horizontal well">
        {{ Form::open(array('files' => true, 'url' => URL::route('edit_parameters_submit', array('section_slug' => $user->currentSection->slug)))) }}
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Prix des cotisations
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-3 col-lg-2 col-sm-offset-6 col-md-offset-4"><label class="control-label">Scout</label></div>
            <div class="col-sm-3"><label class="control-label">Animateur</label></div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">1 membre dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_1_child', $prices['1 child'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_1_leader', $prices['1 leader'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">2 membres dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_2_children', $prices['2 children'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_2_leaders', $prices['2 leaders'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          <div class="form-group">
            <div class="col-sm-6 col-md-4"><label class="control-label">3 membres ou plus dans la famille</label></div>
            <div class="col-sm-3 col-lg-2">{{ Form::text('price_3_children', $prices['3 children'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
            <div class="col-sm-3">{{ Form::text('price_3_leaders', $prices['3 leaders'], array('class' => 'form-control small')) }}&nbsp;&euro;</div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Inscriptions
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-lg-5 col-md-6 col-sm-9 control-label">
              {{ Form::label("registration_active", "Activer les inscriptions") }}
              <span class="horiz-divider"></span>
              {{ Form::checkbox("registration_active", 1, $registration_active) }}
            </div>
            <div class="col-lg-5 col-md-6 col-sm-9 control-label">
              {{ Form::label("reregistration_active", "Activer les réinscriptions") }}
              <span class="horiz-divider"></span>
              {{ Form::checkbox("reregistration_active", 1, $reregistration_active) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Pages du site
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            @foreach ($pages as $page=>$pageData)
              <div class="col-lg-5 col-md-6 col-sm-9 control-label">
                {{ Form::label($page, $pageData['description']) }}
                <span class="horiz-divider"></span>
                {{ Form::checkbox($page, 1, $pageData['active']) }}
              </div>
              <div class="col-lg-1"></div>
            @endforeach
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Menu
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-lg-5 col-md-6 col-sm-9 control-label">
              {{ Form::label("registration_active", "Regrouper les menus de section") }}
              <span class="horiz-divider"></span>
              {{ Form::checkbox("grouped_section_menu", 1, $grouped_section_menu) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Catégories de documents
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('document_categories', "Catégories") }}
              <p>
                NOTE : La catégorie ayant pour nom "Pour&nbsp;les&nbsp;scouts" sera déclinée en "Pour&nbsp;les&nbsp;baladins",
                "Pour&nbsp;les&nbsp;louveteaux", "Pour&nbsp;les&nbsp;éclaireurs", etc. suivant la section.
              </p>
            </div>
            <div class="col-sm-5">
              @foreach ($document_categories as $category)
                @if ($category)
                  <div class="row document-category-row">
                    <div class="col-xs-10">
                      {{ Form::text('document_categories[]', $category, array("class" => "form-control document-category")) }}
                    </div>
                    <div class="col-xs-2">
                      <p class="form-side-note">
                        <span class="glyphicon glyphicon-remove document-category-remove"></span>
                      </p>
                    </div>
                  </div>
                @endif
              @endforeach
              <div class="row document-category-row document-category-prototype" style="display: none;">
                <div class="col-xs-10">
                  {{ Form::text('document_categories[]', "", array("class" => "form-control document-category")) }}
                </div>
                <div class="col-xs-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-remove document-category-remove"></span>
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-10">
                  {{ Form::text('', 'Divers', array("class" => "form-control", "disabled")) }}
                </div>
                <div class="col-xs-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-plus document-category-add"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Paramètres de l'unité
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('unit_long_name', "Nom de l'unité") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('unit_long_name', Parameter::get(Parameter::$UNIT_LONG_NAME), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('unit_short_name', "Sigle de l'unité") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('unit_short_name', Parameter::get(Parameter::$UNIT_SHORT_NAME), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('unit_bank_account', "N° de compte en banque de l'unité") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('unit_bank_account', Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('logo', "Logo du site") }}
            </div>
            <div class="col-sm-8">
              <img src="{{ URL::route('website_logo') }}" class="website-logo-preview" />
              {{ Form::file('logo', array('class' => 'btn btn-default website-logo-file-selector')) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('logo_two_lines', "Logo sur deux lignes") }}
            </div>
            <div class="col-sm-8">
              {{ Form::checkbox('logo_two_lines', 1, $logo_two_lines) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('icon', "Icône du site") }}
            </div>
            <div class="col-sm-8">
              <img src="{{ URL::route('website_icon') }}" class="website-icon-preview" />
              {{ Form::file('icon', array('class' => 'btn btn-default website-logo-file-selector')) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Moteurs de recherche
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('website_meta_description', "Description du site") }}
            </div>
            <div class="col-sm-7">
              {{ Form::textarea('website_meta_description', Parameter::get(Parameter::$WEBSITE_META_DESCRIPTION), array("class" => "form-control", "rows" => 3, "placeholder" => "Cette description apparaitra dans les résultats des moteurs de recherche")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('website_meta_keywords', "Mots-clés de recherche") }}
            </div>
            <div class="col-sm-7">
              {{ Form::textarea('website_meta_keywords', Parameter::get(Parameter::$WEBSITE_META_KEYWORDS), array("class" => "form-control", "rows" => 3, "placeholder" => "Séparés par des virgules, ils permettent aux moteurs de recherche de favoriser ce site dans les résultats quand ces mots-clés sont recherchés")) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Contenu des e-mails automatiques
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <p>
              Certaines parties des e-mails automatiques peuvent être modifiées : elles sont ici représentées
              par des zones de texte à saisir.
            </p>
            <p>
              Clique sur un e-mail pour le modifier : 
              <span class='btn btn-primary' onclick="$('.email-content-pane').hide(); $('#email1').show()">
                Formulaire d'inscription complété
              </span>
              <span class='btn btn-primary' onclick="$('.email-content-pane').hide(); $('#email2').show()">
                Inscription validée
              </span>
            </p>
            <div id="email1" class="email-content-pane col-md-10 col-md-offset-1" style="display: none;">
              <p class="email-description">Cet e-mail automatique est envoyé lorsqu'un visiteur complète le formulaire d'inscription</p>
              <p><strong>Objet : Demande d'inscription de (prénom + nom)</strong></p>
              <p>
                Madame, Monsieur,
              </p>
              <p>Vous venez de faire une demande d'inscription sur le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}.<br />
                Voici les détails de la demande d'inscription&nbsp;:</p>
              <p><em>(Ici apparait la liste des champs complétés lors de la demande d'inscription : nom, prénom, etc.)</em></p>
              {{ Form::textarea('registration_form_filled', Parameter::get(Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_FORM_FILLED), array("class" => "form-control", "rows" => 5)) }}
            </div>
            <div id="email2" class="email-content-pane col-md-10 col-md-offset-1" style="display: none;">
              <p class="email-description">
                Cet e-mail automatique est envoyé lorsqu'une demande d'inscription est validée.
                <br />
                La chaine de caractères <strong>((NOM))</strong> sera remplacée
                par le prénom et le nom du membre inscrit.
                <br />
                Note : si ce champ est vide, aucun e-mail ne sera envoyé.
              </p>
              <p><strong>Objet : Confirmation de l'inscription de ((NOM))</strong></p>
              {{ Form::textarea('registration_validated', Parameter::get(Parameter::$AUTOMATIC_EMAIL_CONTENT_REGISTRATION_VALIDATED), array("class" => "form-control", "rows" => 10)) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Réseaux sociaux
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('facebook_app_id', "Facebook App ID") }}
              <p>Pour activer les fonctionnalités Facebook, <a href="https://developers.facebook.com/apps" target="_blank">créez une application Facebook</a> et entrez ici son ID.</p>
            </div>
            <div class="col-sm-5">
              {{ Form::text('facebook_app_id', Parameter::get(Parameter::$FACEBOOK_APP_ID ), array("class" => "form-control")) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Paramètres avancés du site
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('additional_head_html', "Contenu additionnel du champ &lt;head&gt;") }}
              <p>Ce champ permet par exemple d'insérer les tags de google analytics dans toutes les pages du site.</p>
            </div>
            <div class="col-sm-7">
              {{ Form::textarea('additional_head_html', Parameter::get(Parameter::$ADDITIONAL_HEAD_HTML), array("class" => "form-control", "rows" => 3)) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('photos_public', "Photos publiques") }}
              <p>
                <span class="glyphicon glyphicon-warning-sign danger"></span>
                Si oui, toutes les photos de tous les albums seront visibles par tous les internautes. <br />
                <a target="_blank" href="http://www.lesscouts.be/organiser/les-scouts-20/droit-a-limage/">Plus d'infos sur le droit à l'image.</a>
              </p>
            </div>
            <div class="col-sm-7">
              {{ Form::checkbox('photos_public', 1, Parameter::get(Parameter::$PHOTOS_PUBLIC), array("class" => "photos-public-checkbox")) }}
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-sm-8">
                Paramètres avancés des e-mails
              </div>
              <div class="col-sm-4 text-right">
                <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
              </div>
            </div>
          </legend>
          <div class="form-group">
            <div class="col-sm-12">
              <div class="alert alert-danger">
                <p>
                  Attention&nbsp;! Si les paramètres suivants sont mal configurés, les e-mails ne partiront plus du site. Ne change ces valeurs que si tu es sûr de ce que tu fais.
                </p>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('webmaster_email', "Adresse e-mail du webmaster") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('webmaster_email', Parameter::get(Parameter::$WEBMASTER_EMAIL), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('default_email_from_address', "Adresse e-mail du site") }} <br />(pour l'envoi des e-mails personnels)
            </div>
            <div class="col-sm-5">
              {{ Form::text('default_email_from_address', Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('unit_email_address', "Adresse e-mail de l'unité") }} <br />(pour l'envoi des e-mails d'unité)
            </div>
            <div class="col-sm-5">
              {{ Form::text('unit_email_address', Section::find(1)->email, array("class" => "form-control")) }}
              <br />
              Envoyer les demandes d'inscription à cette adresse :
              <span class="horiz-divider"></span>
              {{ Form::checkbox("send_registrations_to_unit_email_address", 1, Parameter::get(Parameter::$SEND_REGISTRATIONS_TO_UNIT_EMAIL_ADDRESS)) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('smtp_host', "Hôte SMTP pour l'envoi des e-mails") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('smtp_host', Parameter::get(Parameter::$SMTP_HOST), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('smtp_port', "Port SMTP pour l'envoi des e-mails") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('smtp_port', Parameter::get(Parameter::$SMTP_PORT), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('smtp_username', "Login SMTP pour l'envoi des e-mails") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('smtp_username', Parameter::get(Parameter::$SMTP_USERNAME), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('smtp_password', "Mot de passe SMTP pour l'envoi des e-mails") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('smtp_password', Parameter::get(Parameter::$SMTP_PASSWORD), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('smtp_security', "Sécurité SMTP pour l'envoi des e-mails") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('smtp_security', Parameter::get(Parameter::$SMTP_SECURITY), array("class" => "form-control")) }}
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-4 control-label">
              {{ Form::label('email_safe_list[]', "Liste des adresses e-mail vérifiées") }}
            </div>
            <div class="col-sm-5">
              @foreach ($safe_emails as $safe_email)
                <div class="row safe-email-row">
                  <div class="col-xs-10">
                    {{ Form::text('email_safe_list[]', $safe_email, array("class" => "form-control safe-email")) }}
                  </div>
                  <div class="col-xs-2">
                    <p class="form-side-note">
                      <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                    </p>
                  </div>
                </div>
              @endforeach
              <div class="row safe-email-row safe-email-row-prototype" style="display: none;">
                <div class="col-xs-10">
                  {{ Form::text('email_safe_list[]', "", array("class" => "form-control safe-email")) }}
                </div>
                <div class="col-xs-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-10"></div>
                <div class="col-xs-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-plus safe-email-add"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-sm-12 text-right">
              <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop