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

<div class="row registration-edit-help">
  <div class="col-sm-12">
    Dans cette page, les chaines de caractères suivantes seront automatiquement remplacées :
    <table class='registration-edit-help-table'>
      <tbody
        <tr>
          <td><strong>(PRIX UN ENFANT)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_1_CHILD)}}</strong></td>
          <td>(prix pour l'inscription d'un enfant tel que spécifié dans les paramètres)</td>
        </tr>
        <tr>
          <td><strong>(PRIX DEUX ENFANTS)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_2_CHILDREN)}}</strong></td>
          <td>(prix par enfant s'il y a deux membres de la même famille dans l'unité)</td>
        </tr>
        <tr>
          <td><strong>(PRIX TROIS ENFANTS)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_3_CHILDREN)}}</strong></td>
          <td>(prix par enfant s'il y a trois membres de la même famille dans l'unité)</td>
        </tr>
        <tr>
          <td><strong>(PRIX UN ANIMATEUR)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_1_LEADER)}}</strong></td>
          <td>(prix pour l'inscription d'un animateur)</td>
        </tr>
        <tr>
          <td><strong>(PRIX DEUX ANIMATEURS)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_2_LEADERS)}}</strong></td>
          <td>(prix par animateur s'il y a deux membres de la même famille dans l'unité)</td>
        </tr>
        <tr>
          <td><strong>(PRIX TROIS ANIMATEURS)</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$PRICE_3_LEADERS)}}</strong></td>
          <td>(prix par animateur s'il y a trois membres de la même famille dans l'unité)</td>
        </tr>
        <tr>
          <td><strong>BEXX-XXXX-XXXX-XXXX</strong></td>
          <td>=&gt;</td>
          <td><strong>{{Parameter::get(Parameter::$UNIT_BANK_ACCOUNT)}}</strong></td>
          <td>(numéro de compte tel que spécifié dans les paramètres)</td>
        </tr>
        <tr>
          <td><strong>(ACCES CHARTE)</strong></td>
          <td>=&gt;</td>
          <td><strong><a target='_blank' href="{{URL::route('unit_policy')}}">charte d&apos;unité</a></strong></td>
          <td>(lien vers la page de la charte d'unité)</td>
        </tr>
        <tr>
          <td><strong>(ACCES CONTACT)</strong></td>
          <td>=&gt;</td>
          <td><strong><a target='_blank' href="{{URL::route('contacts')}}">contact</a></strong></td>
          <td>(lien vers la page de contact)</td>
        </tr>
        <tr>
          <td><strong>(ACCES FORMULAIRE)</strong></td>
          <td>=&gt;</td>
          <td><strong><a target='_blank' href="{{URL::route('registration_form')}}">formulaire d&apos;inscription</a></strong></td>
          <td>(lien vers le formulaire d'inscription)</td>
        </tr>
      </tbody>
    </table>
    </div>
</div>