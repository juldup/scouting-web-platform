@extends('pages.bootstrapping.bootstrapping-base')
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
  Initialisation du site
@stop


@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Bienvenue sur votre nouveau site</h1>
      <p>
        Bravo&nbsp;! Votre site est installé. Vous allez à présent pouvoir le configurer pour le rendre fonctionnel pour votre unité.
      </p>
      <p>
        Voici les différentes étapes de ce processus&nbsp;:
        <ul>
          <li>
            <strong>Étape 1&nbsp;: Donner l'accès en écriture au système de fichiers</strong>
          </li>
          <li>
            <strong>Étape 2&nbsp;: Configuration de la base de données</strong>
          </li>
          <li>
            <strong>Étape 3&nbsp;: Création des tâches CRON</strong>
          </li>
          <li>
            <strong>Étape 4&nbsp;: Créer un compte d'utilisateur pour le webmaster</strong>
          </li>
          <li>
            <strong>Étape 5&nbsp;: Configuration de l'envoi des e-mails</strong>
          </li>
          <li>
            <strong>Étape 6&nbsp;: Configuration des paramètres de l'unité (nom, sigle, logo, etc.)</strong>
          </li>
          <li>
            <strong>Étape 7&nbsp;: Création des sections</strong>
          </li>
          <li>
            <strong>Étape 8&nbsp;: Paramétrage du prix des cotisations</strong>
          </li>
          <li>
            <strong>Étape 9&nbsp;: Rédaction des pages du site et inscription des membres</strong>
          </li>
        </ul>
      </p>
      <p>
        <a class="btn btn-primary" href="{{ URL::route('bootstrapping-step', array('step' => 1)) }}">
          Commencer
        </a>
      </p>
    </div>
  </div>  
@stop
