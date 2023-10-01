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

@section('header')
<center>
  <small>Cet e-mail a été envoyé automatiquement</small>
</center>
@stop

@section('body')
  <p>
    Bonjour,
  </p>
  <p>
    Vous avez créé une fiche santé pour <strong>{{ $member->first_name }} {{ $member->last_name }}</strong> sur
    le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} il y a près d'un an.
  </p>
  <p>
    Comme nous ne pouvons pas conserver ces données plus d'un an, cette fiche sera automatiquement détruite
    dans 7 jours, sauf si vous la signez à nouveau sur <a href='{{ URL::route('health_card_edit', array('member_id' => $member->id)) }}'>{{ URL::route('health_card_edit', array('member_id' => $member->id)) }}</a>.
  </p>
  <p>
    Si vous ne resignez pas la fiche à temps, vous devrez la remplir à nouveau.
  </p>
  <p>
    Cordialement,<br />Le gestionnaire du site
  </p>
@stop
