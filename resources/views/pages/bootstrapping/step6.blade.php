@extends('pages.bootstrapping.bootstrapping-base')
<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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
  Initialisation du site - étape 6
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 6 : Configurer les paramètres de l'unité et du site</h1>
      @if ($error)
        <p class="alert alert-danger">Une erreur s'est produite lors de l'enregistrement des paramètres.</p>
      @endif
      @if ($success)
        <p class="alert alert-success">Les paramètres ont été enregistrés avec succèss.</p>
        <a href="{{ URL::route('bootstrapping_step', array('step' => 6)) }}" class="btn btn-default">
          Changer les paramètres
        </a>
        <a href="{{ URL::route('bootstrapping_step', array('step' => 7)) }}" class="btn btn-primary">
          Passer à l'étape 7
        </a>
      @else
        <div class="well form-horizontal">
          {!! Form::open(array('files' => true)) !!}
            <legend>Nom de l'unité</legend>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('unit_long_name', "Nom de l'unité") !!}
                <p>Nom tel qu'il apparaitra au sommet des pages du site</p>
              </div>
              <div class="col-sm-5">
                {!! Form::text('unit_long_name', Parameter::get(Parameter::$UNIT_LONG_NAME), array("class" => "form-control")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('unit_short_name', "Sigle de l'unité") !!}
                <p>Nom court (p.ex: SV001, SV19, BW37, BC044). Il servira notamment dans les e-mails lorsque le nom complet est trop long</p>
              </div>
              <div class="col-sm-5">
                {!! Form::text('unit_short_name', Parameter::get(Parameter::$UNIT_SHORT_NAME), array("class" => "form-control")) !!}
              </div>
            </div>

            <legend>Logo</legend>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('logo', "Logo du site") !!}
                <p>Il apparait sur toutes les pages à côté du nom de l'unité</p>
              </div>
              <div class="col-sm-8">
                <img src="{{ URL::route('website_logo') }}" class="website-logo-preview" />
                {!! Form::file('logo', array('class' => 'btn btn-default website-logo-file-selector')) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('logo_two_lines', "Logo sur deux lignes") !!}
                <p>
                  Le logo s'étalera sur deux fois plus de hauteur (idéal pour les logos avec beaucoup de détails et les logos verticaux)
                </p>
              </div>
              <div class="col-sm-8">
                {!! Form::checkbox('logo_two_lines', 1, Parameter::get(Parameter::$LOGO_TWO_LINES)) !!}
              </div>
            </div>

            <legend>Prix de la cotisation</legend>
            <p>Les prix de la cotisation apparaitront à titre informatif sur la page d'inscription.</p>
            <div class="form-group">
              <div class="col-sm-3 col-lg-2 col-sm-offset-6 col-md-offset-4"><label class="control-label">Scout</label></div>
              <div class="col-sm-3"><label class="control-label">Animateur</label></div>
            </div>
            <div class="form-group">
              <div class="col-sm-6 col-md-4"><label class="control-label">1 membre dans la famille</label></div>
              <div class="col-sm-3 col-lg-2">{!! Form::text('price_1_child', Parameter::get(Parameter::$PRICE_1_CHILD), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
              <div class="col-sm-3">{!! Form::text('price_1_leader', Parameter::get(Parameter::$PRICE_1_LEADER), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
            </div>
            <div class="form-group">
              <div class="col-sm-6 col-md-4"><label class="control-label">2 membres dans la famille</label></div>
              <div class="col-sm-3 col-lg-2">{!! Form::text('price_2_children', Parameter::get(Parameter::$PRICE_2_CHILDREN), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
              <div class="col-sm-3">{!! Form::text('price_2_leaders', Parameter::get(Parameter::$PRICE_2_LEADERS), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
            </div>
            <div class="form-group">
              <div class="col-sm-6 col-md-4"><label class="control-label">3 membres ou plus dans la famille</label></div>
              <div class="col-sm-3 col-lg-2">{!! Form::text('price_3_children', Parameter::get(Parameter::$PRICE_3_CHILDREN), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
              <div class="col-sm-3">{!! Form::text('price_3_leaders', Parameter::get(Parameter::$PRICE_3_LEADERS), array('class' => 'form-control small')) !!}&nbsp;&euro;</div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('unit_bank_account', "N° de compte en banque de l'unité") !!}
              </div>
              <div class="col-sm-5">
                {!! Form::text('unit_bank_account', Parameter::get(Parameter::$UNIT_BANK_ACCOUNT), array("class" => "form-control")) !!}
              </div>
            </div>

            <legend>Moteurs de recherche</legend>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('website_meta_description', "Description du site") !!}
                <p>Cette description apparaitra dans les résultats des moteurs de recherche</p>
              </div>
              <div class="col-sm-7">
                {!! Form::textarea('website_meta_description', Parameter::get(Parameter::$WEBSITE_META_DESCRIPTION),
                          array("class" => "form-control", "rows" => 3, "placeholder" => "Cette description apparaitra dans les résultats des moteurs de recherche")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-4 control-label">
                {!! Form::label('website_meta_keywords', "Mots-clés de recherche") !!}
                <p>Séparés par des virgules, ils permettent aux moteurs de recherche de favoriser ce site dans les résultats quand ces mots-clés sont recherchés</p>
              </div>
              <div class="col-sm-7">
                {!! Form::textarea('website_meta_keywords', Parameter::get(Parameter::$WEBSITE_META_KEYWORDS),
                  array("class" => "form-control", "rows" => 3, "placeholder" => "Séparés par des virgules, ils permettent aux moteurs de recherche de favoriser ce site dans les résultats quand ces mots-clés sont recherchés")) !!}
              </div>
            </div>

            <legend>Enregistrer</legend>
            <div class='form-group'>
              <div class="col-sm-4 col-md-offset-4">
                <input type="submit" class="btn btn-primary" value="Enregistrer"/>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
        <a href="{{ URL::route('bootstrapping_step', array('step' => 7)) }}" class="btn btn-default">
          Passer directement à l'étape 7 sans enregistrer les paramètres
        </a>
      @endif
    </div>
  </div>  
@stop
