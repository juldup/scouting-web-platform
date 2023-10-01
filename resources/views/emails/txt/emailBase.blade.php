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
@yield('header')

@yield('body')


------------------------------------------------
@if ($email_is_in_listing)
Cet e-mail a été envoyé depuis le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} dont vous êtes membre.
Si vous souhaitez ne plus recevoir d'e-mails envoyés depuis le site, veuillez contacter {{ Parameter::adaptAnUDenomination("l'animateur d'unité") }} : {{ URL::route('contacts') }}
@else
Cet e-mail a été envoyé depuis le site de l'unité {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}}. Si vous souhaitez ne plus recevoir d'e-mails envoyés depuis notre site, veuillez cliquer sur le lien suivant.
This e-mail was sent because you are a member of our scout group. To unsubscribe, please click on the following link.
{{ URL::route('ban_email', array("code" => $ban_email_code)) }}
@endif
