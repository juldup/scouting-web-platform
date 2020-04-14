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

@section('body')
Madame, Monsieur,

@if ($to_leaders)
Une nouvelle demande d'inscription a été introduite sur le site de l'unité.
@else
Vous venez de faire une demande d'inscription sur le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}.

Voici les détails de la demande d'inscription :
@endif

Nom : {{ $member->getFullName() }}

Date de naissance : {{ Helper::dateToHuman($member->birth_date) }}

Sexe : {{ $member->gender == "M" ? "Garçon" : "Fille" }}

Nationalité : {{ $member->nationality }}

Adresse : {{ $member->address }} ; {{ $member->postcode }} {{ $member->city }}

@if ($member->phone1 || $member->phone2 || $member->phone3)
Téléphone parents : {{ $member->phone1 ? $member->phone1 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}   {{ $member->phone2 ? $member->phone2 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}   {{ $member->phone3 ? $member->phone3 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}

@endif
@if ($member->phone_member)
Téléphone du scout : {{ $member->phone_member }} {{ $member->phone_member_private ? "(confidentiel)" : "" }}

@endif
@if ($member->email1 || $member->email2 || $member->email3)
E-mail des parents : {{ $member->email1 }}{{ $member->email1 && $member->email2 ? ", " : "" }}{{ $member->email2 }}{{ $member->email3 && ($member->email1 || $member->email2) ? ", " : "" }}{{ $member->email3 }}

@endif
@if ($member->email_member)
E-mail du scout : {{ $member->email_member }}

@endif
Section : {{ $member->getSection()->name }}

@if ($member->totem)
Totem et quali : {{ $member->totem }} {{ $member->quali }}

@endif
@if ($member->has_handicap)
Handicap : {{ $member->handicap_details }}

@endif
@if ($member->comments)
Commentaires : {{ $member->comments }}</td>

@endif
@if ($member->is_leader)
Inscription en tant qu'animateur
@endif

@if ($to_leaders)
À vous maintenant de valider ou annuler cette demande inscription.

Cordialement,
Le gestionnaire du site
@else
{{ $custom_content }}
@endif
@stop
