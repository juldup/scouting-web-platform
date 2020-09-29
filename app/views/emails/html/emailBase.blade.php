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
<!doctype html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
</head>
<body>
  <table style="background: lightgray; padding-left: 5px; padding-right: 5px; border-radius: 5px; width: 100%;">
    <tr>
      <td>
        <table style="width: 100%;">
          <tr>
            <td>
              @yield('header')
            </td>
          </tr>
          <tr>
            <td style="padding: 20px; background: white; width: 100%;">
              @yield('body')
            </td>
          </tr>
          <tr>
            <td>
              <center>
                <span style="font-size: .8em; color: #333333;">
                  @if ($email_is_in_listing)
                    Cet e-mail a été envoyé depuis le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }} dont vous êtes membre.<br />
                    Si vous souhaitez ne plus recevoir d'e-mails envoyés depuis le site, veuillez contacter {{{ Parameter::adaptAnUDenomination("l'animateur d'unité") }}}&nbsp;: <a href='{{ URL::route('contacts') }}'>{{ URL::route('contacts') }}</a>
                  @else
                    Cet e-mail a été envoyé depuis le site de l'unité {{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}.
                    Si vous souhaitez ne plus recevoir d'e-mails envoyés depuis notre site, veuillez cliquer sur le lien suivant.<br />
                    This e-mail was sent because you are a member of our scout group. To unsubscribe, please click on the following link.<br />
                    <a href='{{ URL::route('ban_email', array("code" => $ban_email_code)) }}'>{{ URL::route('ban_email', array("code" => $ban_email_code)) }}</a>
                  @endif
                </span>
              </center>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
