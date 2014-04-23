@extends('base')

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
              <div class="col-md-9">
                Prix des cotisations
              </div>
              <div class="col-md-3 text-right">
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
              <div class="col-md-9">
                Inscriptions
              </div>
              <div class="col-md-3 text-right">
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
          </div>
          
          <legend>
            <div class="row">
              <div class="col-md-9">
                Pages du site
              </div>
              <div class="col-md-3 text-right">
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
              <div class="col-md-9">
                Catégories de documents
              </div>
              <div class="col-md-3 text-right">
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
                    <div class="col-sm-10">
                      {{ Form::text('document_categories[]', $category, array("class" => "form-control document-category")) }}
                    </div>
                    <div class="col-sm-2">
                      <p class="form-side-note">
                        <span class="glyphicon glyphicon-remove document-category-remove"></span>
                      </p>
                    </div>
                  </div>
                @endif
              @endforeach
              <div class="row document-category-row document-category-prototype" style="display: none;">
                <div class="col-sm-10">
                  {{ Form::text('document_categories[]', "", array("class" => "form-control document-category")) }}
                </div>
                <div class="col-sm-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-remove document-category-remove"></span>
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-10">
                  {{ Form::text('', 'Divers', array("class" => "form-control", "disabled")) }}
                </div>
                <div class="col-sm-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-plus document-category-add"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-md-9">
                Paramètres de l'unité
              </div>
              <div class="col-md-3 text-right">
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
            <div class="col-md-8">
              <img src="{{ URL::route('website_logo') }}" class="website-logo-preview" />
              {{ Form::file('logo', array('class' => 'btn btn-default website-logo-file-selector')) }}
            </div>
            <div class="col-lg-1"></div>
          </div>
          
          <legend>
            <div class="row">
              <div class="col-md-9">
                Moteurs de recherche
              </div>
              <div class="col-md-3 text-right">
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
              <div class="col-md-9">
                Paramètres avancés du site
              </div>
              <div class="col-md-3 text-right">
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
          
          <legend>
            <div class="row">
              <div class="col-md-9">
                Paramètres avancés des e-mails
              </div>
              <div class="col-md-3 text-right">
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
              {{ Form::label('default_email_from_address', "Adresse e-mail du site") }}
            </div>
            <div class="col-sm-5">
              {{ Form::text('default_email_from_address', Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS), array("class" => "form-control")) }}
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
                  <div class="col-sm-10">
                    {{ Form::text('email_safe_list[]', $safe_email, array("class" => "form-control safe-email")) }}
                  </div>
                  <div class="col-sm-2">
                    <p class="form-side-note">
                      <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                    </p>
                  </div>
                </div>
              @endforeach
              <div class="row safe-email-row safe-email-row-prototype" style="display: none;">
                <div class="col-sm-10">
                  {{ Form::text('email_safe_list[]', "", array("class" => "form-control safe-email")) }}
                </div>
                <div class="col-sm-2">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-remove safe-email-remove"></span>
                  </p>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 col-sm-offset-10">
                  <p class="form-side-note">
                    <span class="glyphicon glyphicon-plus safe-email-add"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12 text-right">
              <input type="submit" class="btn-sm btn-default" value="Enregistrer tous les changements"/>
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@stop