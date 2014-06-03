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
  Suggestions
@stop

@section('additional_javascript')
  @if ($managing)
    <script src="{{ asset('js/edit-suggestions.js') }}"></script>
  @endif
@stop

@section('back_links')
  @if ($managing)
    <p>
      <a href="{{ URL::route('suggestions') }}">
        Retour aux suggestions
      </a>
    </p>
  @endif
@stop

@section('forward_links')
  @if (!$managing && $can_manage)
    <p>
      <a href="{{ URL::route('edit_suggestions') }}">
        Gérer les suggestions
      </a>
    </p>
  @endif
@stop

@section('content')
  
  @if ($managing)
    @include('subviews.contextualHelp', array('help' => 'suggestions'))
  @endif
  
  <div class="row">
    <div class="col-md-12">
      <h1>Boite à suggestions</h1>
      <p>
        Votre avis est important !
      </p>
      <p>
        Cette page vous permet de soumettre des suggestions, de donner votre
        avis sur le site et la vie dans l'unité. Vos suggestions peuvent être
        de n'importe quel ordre.
      </p>
      @include('subviews.flashMessages')
    </div>
  </div>
  @if (!$managing)
    <div class="row">
      <div class="col-md-12">
        <div class="form-horizontal well">
          {{ Form::open(array('url' => URL::route('suggestions_submit'))) }}
            <legend>
              Nouvelle suggestion
            </legend>
            <div class="form-group">
              <div class="col-md-10 col-md-offset-1">
                {{ Form::textarea('body', null, array('class' => 'form-control', 'rows' => 6, 'placeholder' => "Entrez ici votre suggestion")) }}
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-10 col-md-offset-1">
                <p>
                  Attention ! Votre suggestion sera visible publiquement. Pour envoyer un message privé, visitez
                  la <a href="{{ URL::route('contacts') }}">page de contacts</a>.
                </p>
                {{ Form::submit('Soumettre', array('class' => "btn btn-primary")) }}
              </div>
            </div>
          {{ Form::close() }}
        </div>
      </div>
    </div>
  @endif
  <div class="row">
    <div class="col-md-12">
      <h2>Suggestions</h2>
    </div>
    @if (count($suggestions))
      @foreach ($suggestions as $suggestion)
        <div class="col-md-12">
          <div class="well">
            <legend>
              <div class="row">
                <div class="col-md-10">
                  {{{ Helper::dateToHuman($suggestion->created_at) }}}
                </div>
                @if ($managing)
                  <div class="col-md-2 text-right">
                    <a class="btn-sm btn-danger" href="{{ URL::route('edit_suggestions_delete', array('suggestion_id' => $suggestion->id)) }}">
                      Supprimer
                    </a>
                  </div>
                @endif
              </div>
            </legend>
            {{ Helper::rawToHTML($suggestion->body) }}
            <div class="suggestion-response">
              @if ($suggestion->response)
                <div>
                  <strong>Réponse : </strong>
                  {{ Helper::rawToHTML($suggestion->response) }}
                </div>
              @endif
              @if ($managing)
                <p>
                  <strong>Auteur : </strong>
                  @if ($suggestion->user_id)
                    {{{ User::find($suggestion->user_id)->email }}}
                  @else
                    anonyme
                  @endif
                </p>
                @if ($suggestion->response)
                  <div class="text-right">
                    <a class="btn-sm btn-default suggestion-edit-response-button" href="">Changer la réponse</a>
                  </div>
                @else
                  <div class="text-right">
                    <a class="btn-sm btn-primary suggestion-edit-response-button" href="">Répondre</a>
                  </div>
                @endif
                <div class="form-horizontal suggestion-edit-response" style="display:none;">
                  {{ Form::open(array('url' => URL::route('edit_suggestions_submit_response', array('suggestion_id' => $suggestion->id)))) }}
                    <div class="form-group">
                      <div class="col-md-12">
                        {{ Form::textarea('response_' . $suggestion->id, $suggestion->response, array('class' => 'form-control', 'rows' => 5, 'placeholder' => "Réponse à la suggestion")) }}
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="col-md-12">
                        {{ Form::submit('Enregistrer', array('class' => "btn btn-primary")) }}
                      </div>
                    </div>
                  {{ Form::close() }}
                </div>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    @else
      <div class="col-md-12">
        <p>Il n'y a pas encore de suggestions.</p>
      </div>
    @endif
  </div>
@stop