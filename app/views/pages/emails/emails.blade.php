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
  E-mails {{{ $user->currentSection->de_la_section }}}
@stop

@section('back_links')
  @if ($showing_archives)
    <p>
      <a href='{{ URL::route('emails', array('section_slug' => $user->currentSection->slug)) }}'>
        Retour aux e-mails de cette année
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if ($can_send_emails)
    <p>
      <a href="{{ URL::route('send_section_email') }}">
        Envoyer un e-mail aux parents
      </a>
    </p>
  @endif
  @if ($user->isLeader())
    <p>
      <a href="{{ URL::route('send_leader_email') }}">
        Envoyer un e-mail aux animateurs
      </a>
    </p>
  @endif
  @if ($can_send_emails)
    <p>
      <a href="{{ URL::route('manage_emails') }}" >
        Gérer les e-mails
      </a>
    </p>
  @endif
@stop

@section('content')
  
  <div class="row">
    <div class="col-md-12">
      <h1>E-mails {{{ $user->currentSection->de_la_section }}} @if ($showing_archives) (archives) @endif</h1>
    </div>
  </div>
  
  @include('subviews.flashMessages')
  
  @if (count($emails) == 0)
    <div class="row">
      <div class="col-lg-12">
        <p>Il n'y a aucun e-mail.</p>
      </div>
    </div>
  @endif
  
  @if ($user->isMember())
    
    @foreach($emails as $email)
      <div class="row">
        <div class="col-md-12">
          <div class="well @if ($email->target == 'leaders') email-only-leaders @endif">
            <legend>
              @if ($email->target == 'leaders')
                <p class="email-only-leaders">Cet e-mail n'est visible que par les animateurs</p>
              @endif
              {{{ $email->subject }}} – {{ Helper::dateToHuman($email->date) }} à {{ Helper::timeToHuman($email->time) }}
            </legend>
            <p>
              {{ $email->body_html }}
            </p>
            @if ($email->hasAttachments())
              <p class="email-attachment-list">
                @if (count($email->getAttachments()) == 1)
                  <strong>Pièce jointe :</strong>
                @else
                  <strong>Pièces jointes :</strong>
                @endif
                @foreach ($email->getAttachments() as $attachment)
                  <a href="{{ URL::route('download_attachment', array('attachment_id' => $attachment->id)) }}">{{{ $attachment->filename }}}</a>
                  <span class="horiz-divider"></span>
                @endforeach
              </p>
            @endif
          </div>
        </div>
      </div>
    @endforeach
  @else
    @include('subviews.limitedAccess')
  @endif
  
  @if ($has_archives && $user->isMember())
    <div class="vertical-divider"></div>
    @if ($showing_archives)
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('email_archives', array('section_slug' => $user->currentSection->slug, 'page' => $next_page)) }}">Voir les e-mails plus anciens</a>
        </div>
      </div>
    @else
      <div class="row">
        <div class="col-md-12">
          <a class="btn-sm btn-default" href="{{ URL::route('email_archives', array('section_slug' => $user->currentSection->slug)) }}">Voir les e-mails archivés</a>
        </div>
      </div>
    @endif
  @endif
  
@stop