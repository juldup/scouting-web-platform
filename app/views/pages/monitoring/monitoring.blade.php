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
  Supervision du site
@stop

@section('content')
  
  @include('subviews.contextualHelp', array('help' => 'monitoring'))
  
  <div class="row">
    <div class="col-lg-12">
      <h1>Supervision du site</h1>
      @include('subviews.flashMessages')
      
      <h2>1. Tâches Cron</h2>
      <table class="table-striped table-bordered table">
        <thead>
          <tr>
            <th>Tâche</th>
            <th>Dernière exécution</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Envoi automatique des e-mails</td>
            <td>{{ $emailLastExecution ? date('j/n/Y à G:i', $emailLastExecution) : "Jamais" }}</td>
            <td>
              @if ($emailTimedOut)
                <span class="danger">
                  Attention&nbsp;!
                  Cette tâche n'a pas été exécutée depuis trop longtemps.
                  Préviens le <a href="{{ URL::route('contacts') }}#webmaster">webmaster</a>.
                </span>
              @else
                <span class="safe">OK</span>
              @endif
            </td>
          </tr>
          <tr>
            <td>Vérification des expirations des fiches santé</td>
            <td>{{ $healthCardsLastExecution ? date('j/n/Y à G:i', $healthCardsLastExecution) : "Jamais" }}</td>
            <td>
              @if ($healthCardsTimedOut)
                <span class="danger">
                  Attention&nbsp;!
                  Cette tâche n'a pas été exécutée depuis trop longtemps.
                  Préviens le <a href="{{ URL::route('contacts') }}#webmaster">webmaster</a>.
                </span>
              @else
                <span class="safe">OK</span>
              @endif
            </td>
          </tr>
          <tr>
            <td>Augmentation automatique de l'année dans la section</td>
            <td>{{ $incrementYearInSectionLastExecution ? date('j/n/Y à G:i', $incrementYearInSectionLastExecution) : "Jamais" }}</td>
            <td>
              @if ($incrementYearInSectionTimedOut)
                <span class="danger">
                  Attention&nbsp;!
                  Cette tâche n'a pas été exécutée depuis trop longtemps.
                  Préviens le <a href="{{ URL::route('contacts') }}#webmaster">webmaster</a>.
                </span>
              @else
                <span class="safe">OK</span>
              @endif
            </td>
          </tr>
          <tr>
            <td>Suppression automatique des comptes non vérifiés</td>
            <td>{{ $cleanUpUnverifiedAccountsLastExecution ? date('j/n/Y à G:i', $cleanUpUnverifiedAccountsLastExecution) : "Jamais" }}</td>
            <td>
              @if ($cleanUpUnverifiedAccountsLastExecution < time() - 3600 * 24 * 7)
                <span class="safe">
                  Cette tâche n'a pas été exécutée depuis longtemps, mais elle n'est pas indispensable.
                </span>
              @else
                <span class="safe">OK</span>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  
@stop