@extends('emails.txt.emailBase')
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
  (Cet e-mail a été envoyé automatiquement depuis le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }})
@stop

@section('body')
Bonjour,

Vous avez créé une fiche santé pour {{ $member->first_name }} {{ $member->last_name }} sur le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} il y a près d'un an.
Comme nous ne pouvons pas conserver ces données plus d'un an, cette fiche sera automatiquement détruite dans 7 jours, sauf si vous la signez à nouveau sur :
{{ URL::route('health_card_edit', array('member_id' => $member->id)) }}

Si vous ne resignez pas la fiche à temps, vous devrez la remplir à nouveau.

Cordialement,
Le gestionnaire du site
@stop
