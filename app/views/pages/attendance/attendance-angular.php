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

<div id="wait-message">Chargement en cours...</div>

<div id="attendance-wrapper" ng-controller="AttendanceController" class="attendance" style="display: none;">
  <div ng-if="!unmonitoredEvents.length && !monitoredEvents.length" class="alert alert-warning">
    <p>Aucune activité : ajoute des activités dans le calendrier pour pouvoir en gérer les présences.</p>
  </div>
  <div class="row" ng-if="unmonitoredEvents.length">
    <div class="col-sm-12">
      <label>Ajouter une activité à la liste des présences :</label>
      <select ng-change="addUnmonitoredEvent(selectedUnmonitoredEvent)" ng-model="selectedUnmonitoredEvent" class="form-control very-large">
        <option value="">Choisis une activité du calendrier…</option>
        <option ng-repeat="event in unmonitoredEvents" value="{{ event.id }}">{{ formatDate(event.date) }} : {{ event.title }}</option>
      </select>
    </div>
  </div>
  <div class="vertical-divider"></div>
  <div class="row">
    <table class="table table-striped table-hover attendance-table" ng-if="monitoredEvents.length">
      <tr>
        <th class="name-column"></th>
        <th class="count-column"></th>
        <th ng-click="shiftLeft()" class="navigation-column">
          <span class="pointer-cursor" ng-if="minDate > monitoredEvents[0].date">&lt;&lt;&lt;</span>
        </th>
        <th ng-repeat="event in monitoredEvents" class="attendance-column" ng-if="event.date <= maxDate && event.date >= minDate">
          <span title="{{ event.title }}">{{ formatDate(event.date) }}
            <a href="#" class="remove-event" ng-click="remove(event)" ng-if="canEdit"><span class="glyphicon glyphicon-remove"></span></a>
          </span>
        </th>
        <th ng-click="shiftRight()" class="navigation-column">
          <span class="pointer-cursor" ng-if="maxDate < monitoredEvents[monitoredEvents.length - 1].date">&gt;&gt;&gt;</span>
        </th>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td ng-repeat="event in monitoredEvents" ng-if="event.date <= maxDate && event.date >= minDate">
          <span class="glyphicon glyphicon-check pointer-cursor attendance-present" ng-click="setAll(event, true)" title="Tous présents"></span>
          –
          <span class="glyphicon glyphicon-unchecked pointer-cursor attendance-absent" ng-click="setAll(event, false)" title="Tous absents"></span>
        </td>
        <td></td>
      </tr>
      <tr ng-repeat="member in members">
        <td>
          {{ member.name }}
        </td>
        <td>
          <span class="attendance-present">{{ countMemberStatus(member, true) }}</span>
          –
          <span class="attendance-absent">{{ countMemberStatus(member, false) }}</span>
        </td>
        <td></td>
        <td ng-repeat="event in monitoredEvents" ng-click="toggle(member, event)" class="pointer-cursor" ng-if="event.date <= maxDate && event.date >= minDate">
          <span ng-if="member.status['event_' + event.id]" class="attendance-present">Présent<span ng-if="member.isFemale">e</span></span>
          <span ng-if="!member.status['event_' + event.id]" class="attendance-absent">Absent<span ng-if="member.isFemale">e</span></span>
        </td>
        <td></td>
      </tr>
      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td ng-repeat="event in monitoredEvents" ng-if="event.date <= maxDate && event.date >= minDate">
        <td></td>
      </tr>
      <tr>
        <th>TOTAL</th>
        <td></td>
        <td></td>
        <td ng-repeat="event in monitoredEvents" ng-if="event.date <= maxDate && event.date >= minDate">
          <span class="attendance-present">{{ countWithStatus(event, true) }}</span>
          –
          <span class="attendance-absent">{{ countWithStatus(event, false) }}</span>
        </td>
        <td></td>
      </tr>
    </table>
  </div>
</div>
