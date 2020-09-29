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
  Animateurs {{{ $user->currentSection->de_la_section }}}
@stop

@section('head')
  <meta name="robots" content="noindex">
@stop

@section('back_links')
  @if ($archive_year)
    <p>
      <a href='{{ URL::route('leaders', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux animateurs de cette année
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if ($is_leader)
    <p>
      <a href='{{ URL::route('edit_leaders', array('section_slug' => $user->currentSection->slug)) }}'>
        Modifier les animateurs
      </a>
    </p>
    <p>
      <a href='{{ URL::route('edit_privileges', array('section_slug' => $user->currentSection->slug)) }}'>
        Modifier les privilèges des animateurs
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>
        Animateurs {{{ $user->currentSection->de_la_section }}}
        @if ($archive_year)
          en {{{ $archive_year }}}
        @endif
      </h1>
      @include('subviews.flashMessages')
    </div>
  </div>
  
  <div class="row">
    <?php $currentLeaderInCharge = false; ?>
    @foreach ($leaders as $leader)
      @if ($currentLeaderInCharge != $leader->leader_in_charge)
        </div>
        <div class="row">
          <div class="col-md-12">
            <legend>
              @if ($leader->leader_in_charge)
                <?php $currentLeaderInCharge = true; ?>
                @if ($user->currentSection->id == 1)
                  @if ($count_in_charge > 1 && $men_in_charge) {{{ Parameter::adaptAnUDenomination("Animateurs d'unité") }}}
                  @elseif ($count_in_charge > 1 && !$men_in_charge) {{{ Parameter::adaptAnUDenomination("Animatrices d'unité") }}}
                  @elseif ($men_in_charge) {{{ Parameter::adaptAnUDenomination("Animateur d'unité") }}}
                  @else {{{ Parameter::adaptAnUDenomination("Animatrice d'unité") }}}
                  @endif
                @else
                  @if ($count_in_charge > 1 && $men_in_charge) Animateurs responsables
                  @elseif ($count_in_charge > 1 && !$men_in_charge) Animatrices responsables
                  @elseif ($men_in_charge) Animateur responsable
                  @else Animatrice responsable
                  @endif
                @endif
              @else
                <?php $currentLeaderInCharge = false; ?>
                @if ($user->currentSection->id == 1)
                  @if ($count_others > 1 && $men_in_others) {{{ Parameter::adaptAsUDenomination("Équipiers d'unité") }}}
                  @elseif ($count_others > 1 && !$men_in_others) {{{ Parameter::adaptAsUDenomination("Équipières d'unité") }}}
                  @elseif ($men_in_others) {{{ Parameter::adaptAsUDenomination("Équipier d'unité") }}}
                  @else {{{ Parameter::adaptAsUDenomination("Équipière d'unité") }}}
                  @endif
                @else
                  @if ($count_others > 1 && $men_in_others) Équipiers
                  @elseif ($count_others > 1 && !$men_in_others) Équipières
                  @elseif ($men_in_others) Équipier
                  @else Équipière
                  @endif
                @endif
              @endif
            </legend>
          </div>
        </div>
        <div class="row">
      @endif
      <div class="col-md-6 leader-card">
        <div class="well">
          <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-6">
              @if ($leader->has_picture)
                <img class="leader_picture" src="{{ $leader->getPictureURL() }}" />
              @else
                <img class="leader_picture" src="" alt=" Pas de photo " />
              @endif
            </div>
            <div class="col-xs-6 col-sm-8 col-md-6">
              <p class="leader-name">{{{ $leader->leader_name }}}</p>
              <p class="leader-real-name">{{{ $leader->first_name }}} {{{ $leader->last_name }}}</p>
              <p><em>{{ Helper::rawToHTML($leader->leader_description) }}</em></p>
              @if ($leader->leader_role)
                <p><strong>Rôle :</strong> {{ Helper::rawToHTML($leader->leader_role) }}</p>
              @endif
              @if (!$leader->phone_member_private && $leader->phone_member)
                <p><strong>GSM :</strong> {{{ $leader->phone_member }}}</p>
              @endif
              @if ($leader->email_member)
                <p>
                  <strong>E-mail :</strong>
                  @if ($archive_year)
                    <a class="btn-sm btn-primary" href="{{ URL::route('personal_email', array('contact_type' => PersonalEmailController::$CONTACT_TYPE_ARCHIVED_LEADER, 'member_id' => $leader->id)) }}">
                      Envoyer un e-mail
                    </a>
                  @else
                    <a class="btn-sm btn-primary" href="{{ URL::route('personal_email', array('contact_type' => PersonalEmailController::$CONTACT_TYPE_PERSONAL, 'member_id' => $leader->id)) }}">
                      Envoyer un e-mail
                    </a>
                  @endif
                </p>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  
  @if (count($archives) || $archive_year)
    <div class="row">
      <div class="col-md-12">
        <h2>Animateurs des années précédentes</h2>
      </div>
    </div>
    @foreach ($archives as $index => $archive)
      <div class="row">
        <div class="col-md-12">
          @if ($index != 0 && substr($archive,5,4) != substr($archives[$index - 1],0,4))
            <div class="vertical-divider-small"></div>
          @endif
          <p>
            <a href="{{ URL::route('archived_leaders', array('section_slug' => $user->currentSection->slug, 'year' => $archive)) }}" class="btn-sm btn-default">
              Voir les animateurs de l'année {{{ $archive }}}
            </a>
          </p>
        </div>
      </div>
    @endforeach
    @if ($archive_year)
      <div class="row">
        <div class="col-md-12">
          <p></p>
          <p>
            <a class="btn-sm btn-default" href='{{ URL::route('leaders', array('section_slug' => $user->currentSection->slug)) }}'>
                Retour aux animateurs de cette année
            </a>
          </p>
        </div>
      </div>
    @endif
  @endif
@stop