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

?>

<div id="wait-message">Chargement en cours...</div>

<div id="payment-wrapper" ng-controller="PaymentController" class="payment" style="display: none;">
  <div class="row">
    <div class="col-sm-12">
      <form id="new-event-form" ng-submit="addEvent()">
        <label>Ajouter une activité à la liste des paiements&nbsp;:</label>
        <input id="new-event-input" class="form-control medium" placeholder="Nom de l'activité">
        <input type="submit" class="btn btn-primary" value="Ajouter"></button>
      </form>
    </div>
  </div>
  <div class="vertical-divider"></div>
  <div class="row">
    <table class="table table-striped table-hover payment-table" ng-if="events.length">
      <tr>
        <th class="name-column"></th>
        <th class="count-column"></th>
        <th ng-click="shiftLeft()" class="navigation-column">
          <span class="pointer-cursor" ng-if="minId > events[0].id">&lt;&lt;&lt;</span>
        </th>
        <th ng-repeat="event in events" class="payment-column" ng-if="event.id <= maxId && event.id >= minId">
          <span>{{ event.name }}
            <a class="remove-event" ng-click="remove(event)"><span class="glyphicon glyphicon-remove"></span></a>
          </span>
        </th>
        <th ng-click="shiftRight()" class="navigation-column">
          <span class="pointer-cursor" ng-if="maxId < events[events.length - 1].id">&gt;&gt;&gt;</span>
        </th>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td ng-repeat="event in events" ng-if="event.id <= maxId && event.id >= minId">
          <span class="glyphicon glyphicon-check pointer-cursor payment-paid" ng-click="setAll(event, true)" title="Tous payés"></span>
          –
          <span class="glyphicon glyphicon-unchecked pointer-cursor payment-unpaid" ng-click="setAll(event, false)" title="Tous non payés"></span>
        </td>
        <td></td>
      </tr>
      <tr ng-repeat="member in members">
        <td>
          {{ member.name }}
        </td>
        <td>
          <span class="payment-paid">{{ countMemberStatus(member, true) }}</span>
          –
          <span class="payment-unpaid">{{ countMemberStatus(member, false) }}</span>
        </td>
        <td></td>
        <td ng-repeat="event in events" ng-click="toggle(member, event)" class="pointer-cursor" ng-if="event.id <= maxId && event.id >= minId">
          <span ng-if="member.status['event_' + event.id]" class="payment-paid">Payé</span>
          <span ng-if="!member.status['event_' + event.id]" class="payment-unpaid">Non payé</span>
        </td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td ng-repeat="event in events" ng-if="event.id <= maxId && event.id >= minId">
        <td></td>
      </tr>
      <tr>
        <th>TOTAL</th>
        <td></td>
        <td></td>
        <td ng-repeat="event in events" ng-if="event.id <= maxId && event.id >= minId">
          <span class="payment-paid">{{ countWithStatus(event, true) }}</span>
          –
          <span class="payment-unpaid">{{ countWithStatus(event, false) }}</span>
        </td>
        <td></td>
      </tr>
    </table>
  </div>
</div>
