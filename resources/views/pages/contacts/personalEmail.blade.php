@extends('base')
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
use App\Http\Controllers\PersonalEmailController;

?>

@section('title')
  Envoi d'un e-mail
@stop

@section('back_links')
<p>
  <a href="{{ Session::get('personal_email_referrer') }}">
    Retour à la page précédente
  </a>
</p>
@stop

@section('content')
  @if (Session::has('success_message'))
    @include('subviews.flashMessages')
  @else
    <div class="row">
      <div class="col-md-12">
        <div class="form-horizontal well">
          <form method="post" action="" class="obfuscated-form" data-action-url="{{ URL::route('personal_email_submit', array('contact_type' => $contact_type, 'member_id' => $member ? $member->id : ($section ? $section->id : 0))) }}" >
            @csrf
            <legend>
              @if ($contact_type == PersonalEmailController::$CONTACT_TYPE_PARENTS)
                <h2>Envoyer un e-mail aux parents de {{{ $member->first_name }}} {{{ $member->last_name }}}</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_PERSONAL)
                <h2>Envoyer un e-mail à {{{ $member->first_name }}} {{{ $member->last_name }}} ({{{ $member->leader_name }}})</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_ARCHIVED_LEADER)
                <h2>Envoyer un e-mail à {{{ $member->first_name }}} {{{ $member->last_name }}} ({{{ $member->leader_name }}} en {{{ $member->year }}})</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_SECTION)
                <h2>Envoyer un e-mail aux animateurs {{{ $section->de_la_section }}}</h2>
              @elseif ($contact_type == PersonalEmailController::$CONTACT_TYPE_WEBMASTER)
                <h2>Envoyer un e-mail au webmaster</h2>
              @endif
            </legend>
            @include('subviews.flashMessages')
            <div class="form-group">
              <label for="subject" class="control-label col-md-3">Sujet</label>
              <div class="col-md-7">
                <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label for="body" class="control-label col-md-3">Message</label>
              <div class="col-md-7">
                <textarea name="body" class="form-control" rows="10">{{ old('body') }}</textarea>
              </div>
            </div>
            <div class="form-group">
              <label for="sender_name" class="control-label col-md-3">Votre nom</label>
              <div class="col-md-4">
                <input type="text" name="sender_name" value="{{ old('sender_name') }}" class="form-control" />
              </div>
            </div>
            <div class="form-group">
              <label for="sender_email" class="control-label col-md-3">Votre adresse e-mail</label>
              <div class="col-md-4">
                <input type="text" name="sender_email" value="{{ old('sender_email') }}" class="form-control" />
              </div>
              <div class="col-md-5">
                <p class="form-side-note">
                  Votre adresse e-mail sera révélée au destinataire.
                </p>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-7 col-md-offset-3">
                <input type="submit" value="Activez le javascript pour soumettre" class="btn btn-primary"
                       data-text="Envoyer" disabled />
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@stop