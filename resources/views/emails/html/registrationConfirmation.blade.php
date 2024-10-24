@extends('emails.html.emailBase')
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

use App\Models\Parameter;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Session;
use App\Helpers\Form;
use App\Models\Privilege;
use App\Models\MemberHistory;

?>

@section('body')
  <p>
    Madame, Monsieur,
  </p>
  @if ($to_leaders)
    <p>
      Une nouvelle demande d'inscription a été introduite sur le site de l'unité.
    </p>
  @else
    <p>
      Vous venez de faire une demande d'inscription sur le site de l'unité {{{ Parameter::get(Parameter::$UNIT_SHORT_NAME) }}}.<br />
      Voici les détails de la demande d'inscription&nbsp;:
    </p>
  @endif
  <p>
    <table>
      <tr>
        <td><strong>Nom</strong>&nbsp;:</td>
        <td>{{{ $member->getFullName() }}}</td>
      </tr>
      <tr>
        <td><strong>Date de naissance</strong>&nbsp;:</td>
        <td>{{{ Helper::dateToHuman($member->birth_date) }}}</td>
      </tr>
      <tr>
        <td><strong>Sexe</strong>&nbsp;:</td>
        <td>{{{ $member->gender == "M" ? "Garçon" : "Fille" }}}</td>
      </tr>
      <tr>
        <td><strong>Nationalité</strong>&nbsp;:</td>
        <td>{{{ $member->nationality }}}</td>
      </tr>
      <tr>
        <td><strong>Adresse</strong>&nbsp;:</td>
        <td>{{{ $member->address }}} ; {{{ $member->postcode }}} {{{ $member->city }}}</td>
      </tr>
      @if ($member->phone1 || $member->phone2 || $member->phone3)
        <tr>
          <td><strong>Téléphone parents</strong>&nbsp;:</td>
          <td>
            {{{ $member->phone1 ? $member->phone1 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}}
            {{{ $member->phone2 ? $member->phone2 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}}
            {{{ $member->phone3 ? $member->phone3 . ($member->phone1_private ? " (confidentiel)" : "") : "" }}}
          </td>
        </tr>
      @endif
      @if ($member->phone_member)
        <tr>
          <td><strong>Téléphone du scout</strong>&nbsp;:</td>
          <td>{{{ $member->phone_member }}} {{{ $member->phone_member_private ? " (confidentiel)" : "" }}}</td>
        </tr>
      @endif
      @if ($member->email1 || $member->email2 || $member->email3)
        <tr>
          <td><strong>E-mail des parents </strong>&nbsp;:</td>
          <td>{{{ $member->email1 }}}{{{ $member->email1 && $member->email2 ? ", " : "" }}}{{{ $member->email2 }}}{{{ $member->email3 && ($member->email1 || $member->email2) ? ", " : "" }}}{{{ $member->email3 }}}</td>
        </tr>
      @endif
      @if ($member->email_member)
        <tr>
          <td><strong>E-mail du scout</strong>&nbsp;:</td>
          <td>{{{ $member->email_member }}}</td>
        </tr>
      @endif
      <tr>
        <td><strong>Section</strong>&nbsp;:</td>
        <td>{{{ ($member->registration_section_category ? Section::getCategoryName($member->registration_section_category) : $member->getSection()->name) }}}</td>
      </tr>
      @if ($member->totem)
        <tr>
          <td><strong>Totem et quali</strong>&nbsp;:</td>
          <td>{{{ $member->totem }}} {{{ $member->quali }}}</td>
        </tr>
      @endif
      @if ($member->has_handicap)
        <tr>
          <td><strong>Handicap</strong>&nbsp;:</td>
          <td>{{{ $member->handicap_details }}}</td>
        </tr>
      @endif
      @if ($member->comments)
        <tr>
          <td><strong>Commentaires</strong>&nbsp;:</td>
          <td>{{{ $member->comments }}}</td>
        </tr>
      @endif
      @if ($member->is_leader)
        <tr>
          <td><strong>Spécial&nbsp;:</strong></td>
          <td>Inscription en tant qu'animateur</td>
        </tr>
      @endif
    </table>
  </p>
  @if ($to_leaders)
    <p>
      À vous maintenant de valider ou annuler cette demande d'inscription&nbsp;:
      <a href="{{ URL::route('manage_registration', array('section_slug' => $member->getSection()->slug)) }}">{{ URL::route('manage_registration', array('section_slug' => $member->getSection()->slug)) }}</a>
    </p>
    <p>
      Cordialement,<br />Le gestionnaire du site
    </p>
  @else
    {{ Helper::rawToHTML($custom_content) }}
  @endif
@stop
