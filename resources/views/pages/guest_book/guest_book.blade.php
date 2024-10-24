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

?>

@section('title')
  Livre d'or
@stop

@section('additional_javascript')
  @vite(['resources/js/guest-book.js'])
@stop

@section('back_links')
  @if ($managing)
    <p>
      <a href="{{ URL::route('guest_book') }}">
        Retour au livre d'or
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if (!$managing && $can_manage)
    <p>
      <a href="{{ URL::route('edit_guest_book') }}">
        Gérer le livre d'or
      </a>
    </p>
  @endif
@stop

@section('content')
  
  @if ($managing)
    @include('subviews.contextualHelp', array('help' => 'guest-book'))
  @endif
  
  <div class="row">
    <div class="col-md-12">
      <h1>Livre d'or</h1>
      <p>
        Laissez ici une trace de votre passage sur le site ou dans l'unité.
      </p>
      @include('subviews.flashMessages')
    </div>
  </div>
  @if (!$managing)
    <div class="row">
      <div class="col-md-12">
        <a class='btn btn-default guest-book-button'
           @if (Session::has('_old_input')) style="display: none;" @endif
           >
          Ajouter un message au livre d'or
        </a>
        <div class="form-horizontal well guest-book-form"
             @if (!Session::has('_old_input')) style="display: none;" @endif
             >
          {!! Form::open(array('url' => "", 'class' => 'obfuscated-form', 'data-action-url' => URL::route('guest_book_submit'))) !!}
            <legend>
              Nouveau message pour le livre d'or
            </legend>
            <div class="form-group">
              {!! Form::label('author', 'Qui êtes-vous ?', array('class' => 'control-label col-md-2')) !!}
              <div class="col-md-8">
                {!! Form::text('author', null, array('class' => 'form-control', 'placeholder' => "Votre nom, qui vous êtes par rapport à l'unité")) !!}
              </div>
            </div>
            <div class="form-group">
              {!! Form::label('body', 'Message', array('class' => 'control-label col-md-2')) !!}
              <div class="col-md-8">
                {!! Form::textarea('body', null, array('class' => 'form-control', 'rows' => 6, 'placeholder' => "Entrez ici votre message")) !!}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-8 col-md-offset-2">
                {!! Form::submit('Activez le javascript pour soumettre', array('class' => "btn btn-primary", 'data-text' => 'Soumettre', 'disabled')) !!}
                <a href="" class="btn btn-default guest-book-cancel">
                  Annuler
                </a>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  @endif
  <div class="vertical-divider"></div>
  <div class="row">
    @if (count($guest_book_entries))
      @foreach ($guest_book_entries as $entry)
        <div class="col-md-12">
          <div class="well">
            <legend>
              <div class="row">
                <div class="col-md-10">
                  {{ Helper::dateToHuman($entry->created_at) }} – Message de {{{ $entry->author }}}
                </div>
                @if ($managing)
                  <div class="col-md-2 text-right">
                    <a class="btn-sm btn-danger" href="{{ URL::route('edit_guest_book_delete', array('entry_id' => $entry->id)) }}">
                      Supprimer
                    </a>
                  </div>
                @endif
              </div>
            </legend>
            {{ Helper::rawToHTML($entry->body) }}
          </div>
        </div>
      @endforeach
    @else
      <div class="col-md-12">
        <p>Le livre d'or est vide :-(</p>
        @if (!$managing)
          <p>Soyez le premier à y laisser un petit mot.</p>
        @endif
      </div>
    @endif
  </div>
@stop