@extends('emails.html.emailBase')
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

@section('body')
  <p>
    Bonjour,
  </p>
  <p>
    Vous avez changé l'adresse e-mail associée à votre compte d'utilisateur sur le site de l'unité {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}}.
  </p>
  <p>
    Vous devez réactiver votre compte en cliquant sur ce lien :
    <a href="{{ URL::route('verify_user', array("code" => $verification_code)) }}">{{ URL::route('verify_user', array("code" => $verification_code)) }}</a>
  </p>
  <p>
    Cordialement,<br />Le gestionnaire du site
  </p>
@stop
