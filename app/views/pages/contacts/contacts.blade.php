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
  Contacts {{{ Parameter::get(Parameter::$SHOW_LINKS) ? "et liens" : "" }}}
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('forward_links')
  {{-- Link to management --}}
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_address_page') }}'>
        Modifier l'adresse
      </a>
    </p>
  @endif
  @if ($user->isLeader())
    <p>
      <a href='{{ URL::route('edit_leaders') }}'>
        Modifier les animateurs
      </a>
    </p>
  @endif
  @if ($can_edit)
    <p>
      <a href='{{ URL::route('edit_links') }}'>
        Modifier les liens
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <h1>Contacts {{{ Parameter::get(Parameter::$SHOW_LINKS) ? "et liens" : "" }}}</h1>
  
  @if (Parameter::get(Parameter::$SHOW_ADDRESSES))
    <h2>Adresse</h2>
    
    <div class="row page_body">
      <div class="col-md-12">
        {{ $page_body }}
      </div>
    </div>
  @endif
  
  @if (Parameter::get(Parameter::$SHOW_ADDRESSES) || Parameter::get(Parameter::$SHOW_LINKS))
    <h2>Contacts</h2>
  @endif
  
  <div class="well">
    <legend>Contacter les animateurs d'unité</legend>
    @foreach ($unitLeaders as $leader)
      <div class='row contact-row'>
        <div class="col-md-3">
          <p>
            <strong>{{{ $leader->leader_name }}}</strong>
            @if ($leader->leader_in_charge)
              @if ($leader->gender == "F") (animatrice d'unité) @else (animateur d'unité) @endif
            @else
              @if ($leader->gender == "F") (assistante d'unité) @else (assistant d'unité) @endif
            @endif
          </p>
        </div>
        <div class="col-md-3">
          <p>{{{ $leader->first_name }}} {{{ $leader->last_name }}}</p>
        </div>
        <div class="col-md-2">
          <p>
            @if ($leader->phone_member && !$leader->phone_member_private) {{{ $leader->phone_member }}} @endif
          </p>
        </div>
        <div class="col-md-4">
          <a class='btn-sm btn-default' href='{{ URL::route('personal_email', array("contact_type" => PersonalEmailController::$CONTACT_TYPE_PERSONAL, "member_id" => $leader->id)) }}'>
            Contacter {{{ $leader->leader_name }}} par e-mail
          </a>
        </div>
      </div>
    @endforeach
  </div>
  
  <div class='well'>
    <legend>Contacter les responsables des sections</legend>
    @foreach ($sectionLeaders as $leader)
      <div class='row contact-row'>
        <div class="col-md-3">
          <p><strong>{{{ $leader->getSection()->name }}}</strong></p>
        </div>
        <div class="col-md-3">
          <p>{{{ $leader->first_name }}} {{{ $leader->last_name }}} ({{{ $leader->leader_name }}})</p>
        </div>
        <div class="col-md-2">
          <p>
            @if ($leader->phone_member && !$leader->phone_member_private) {{{ $leader->phone_member }}} @endif
          </p>
        </div>
        <div class="col-md-3">
          <a class='btn-sm btn-default' href='{{ URL::route('personal_email', array('contact_type' => PersonalEmailController::$CONTACT_TYPE_PERSONAL, 'member_id' => $leader->id)) }}'>
            Contacter {{{ $leader->leader_name }}} par e-mail
          </a>
        </div>
      </div>
    @endforeach
  </div>
  
  <div class='well'>
    <a name='webmaster'></a>
    <legend>Contacter le webmaster (Julien Dupuis)</legend>
    <div class='row'>
      <div class="col-md-3">
        <p><strong>Webmaster</strong></p>
      </div>
      <div class="col-md-3">
        <p>{{{ $webmaster['name'] }}}</p>
      </div>
      <div class="col-md-2">
        <p>{{{ $webmaster['phone'] }}}</p>
      </div>
      <div class="col-md-4">
        <a class='btn-sm btn-default' href='{{ URL::route('personal_email', array('contact_type' => PersonalEmailController::$CONTACT_TYPE_WEBMASTER, 'member_id' => 0)) }}'>Contacter {{ $webmaster['name'] }} par e-mail</a>
      </div>
    </div>
  </div>
  
  @if (Parameter::get(Parameter::$SHOW_LINKS))
    <div class="row">
      <div class="col-lg-12">
        <h2>Liens</h2>
      </div>
    </div>

    @foreach ($links as $link)
      <div class="row">
        <div class="col-md-12">
          <div class="well clickable clickable-no-default">
            <legend>
              <a href="{{{ $link->url }}}" target="_blank">{{{ $link->title }}}</a>
            </legend>
            <div>
              {{ Helper::rawToHTML($link->description) }}
            </div>
          </div>
        </div>
      </div>
    @endforeach
  @endif
  
@stop
