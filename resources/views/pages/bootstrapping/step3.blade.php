@extends('pages.bootstrapping.bootstrapping-base')
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
?>

@section('title')
  Initialisation du site - étape 3
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
      <h1>Étape 3 : Créer les tâches CRON</h1>
      <p>
        Pour le bon fonctionnement du site, 3 tâches CRON doivent être exécutées. Faites la configuration des tâches CRON
        via votre hébergeur ou via un outil de tâches CRON extérieur au site. Il y a deux manières de configurer les tâches CRON&nbsp;:
        <ul>
          <li>depuis l'extérieur, via l'appel d'une URL</li>
          <li>depuis le serveur, via un script</li>
        </ul>
        Une fois le site actif, vous pourrez vérifier le bon fonctionnement des tâches CRON depuis le coin des animateurs.
      </p>
      <h2>(Possibilité 1) Exécuter les tâches via l'appel d'une URL</h2>
      <table class="table table-striped table-bordered table-responsive">
        <thead>
          <tr>
            <th>Tâche</th>
            <th>URL à appeler</th>
            <th>Fréquence</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Envoi automatique des e-mails en attente</td>
            <td>{{ URL::route('cron_send_emails_automatically') }}</td>
            <td>Toutes les minutes (ou le plus souvent possible)</td>
          </tr>
          <tr>
            <td>Envoi des rappel avant expiration des fiches santé et suppression des fiches expirées</td>
            <td>{{ URL::route('cron_auto_delete_health_cards') }}</td>
            <td>Une fois par jour</td>
          </tr>
          <tr>
            <td>Augmentation automatique de l'année des scouts</td>
            <td>{{ URL::route('cron_auto_increment_year_in_section') }}</td>
            <td>Tous les jours (doit être exécuté au moins le 1er août de chaque année)</td>
          </tr>
          <tr>
            <td>Suppression automatique des comptes non vérifiés</td>
            <td>{{ URL::route('cron_auto_clean_up_unverified_accounts') }}</td>
            <td>Une fois par jour</td>
          </tr>
        </tbody>
      </table>
      <h2>(Possibilité 2) Exécuter les tâches via un script</h2>
      <table class="table table-striped table-bordered table-responsive">
        <thead>
          <tr>
            <th>Tâche</th>
            <th>Script à appeler</th>
            <th>Fréquence</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Envoi automatique des e-mails en attente</td>
            <td>{...}/cron/send_pending_emails.php</td>
            <td>Toutes les minutes (ou le plus souvent possible)</td>
          </tr>
          <tr>
            <td>Envoi des rappel avant expiration des fiches santé et suppression des fiches expirées</td>
            <td>{...}/cron/health_cards.php</td>
            <td>Une fois par jour</td>
          </tr>
          <tr>
            <td>Augmentation automatique de l'année des scouts</td>
            <td>{...}/cron/year_in_section.php</td>
            <td>Tous les jours (doit être exécuté au moins le 1er août de chaque année)</td>
          </tr>
          <tr>
            <td>Suppression automatique des comptes non vérifiés</td>
            <td>{...}/cron/delete_unverified_accounts.php</td>
            <td>Une fois par jour</td>
          </tr>
        </tbody>
      </table>
      <p>
        <a class="btn btn-primary" href="{{ URL::route('bootstrapping_step', array('step' => 4)) }}">
          Passer à l'étape 4
        </a>
      </p>
    </div>
  </div>  
@stop
