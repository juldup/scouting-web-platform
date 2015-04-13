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

<div ng-controller="LogsController">
  <div class="row">
    <div class="col-sm-12">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>#</th>
            <th>Date</th>
            <th>Utilisateur</th>
            <th>Catégorie</th>
            <th>Action</th>
            <th>Section</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th></th>
            <td></td>
            <td>
              <select ng-change="updateFilter()" ng-model="userFilter" class="form-control">
                <option value="">Tous</option>
                <option ng-repeat="user in users" ng-value="user">{{ user }}</option>
              </select>
            </td>
            <td>
              <select ng-change="updateFilter()" ng-model="categoryFilter" class="form-control">
                <option value="">Toutes</option>
                <option ng-repeat="category in categories" ng-value="category">{{ category }}</option>
              </select>
            </td>
            <td>
              <select ng-change="updateFilter()" ng-model="actionFilter" class="form-control">
                <option value="">Toutes</option>
                <option value="errors">Uniquement les erreurs</option>
                <option value="non-errors">Uniquement les actions réussies</option>
              </select>
            </td>
            <td>
              <select ng-change="updateFilter()" ng-model="sectionFilter" class="form-control">
                <option value="">Toutes</option>
                <option ng-repeat="section in sections" ng-value="section">{{ section }}</option>
              </select>
            </td>
          </tr>
        </tbody>
        <tbody ng-repeat="log in logs" class="log-entry" ng-show="showLog(log)" ng-class="{'log-error': log.isError}">
          <tr ng-click="toggleDetails(log.id)">
            <td>{{ log.id }}</td>
            <td>{{ log.date }}</td>
            <td title="{{ log.userEmail }}">{{ log.user }}</td>
            <td>{{ log.category }}</td>
            <td>{{ log.action }} {{ log.data.multiple ? "(" + log.data.multiple.length + ")" : "" }}</td>
            <td>{{ log.section }}</td>
          </tr>
          <tr ng-show="displayDetails == log.id" ng-click="toggleDetails(log.id)">
            <td></td>
            <td colspan="5">
              <div ng-if="log.data.multiple">
                <div ng-repeat="item in log.data.multiple.slice().reverse()" class="log-multiple-data">
                  <p ng-repeat="info in item">
                    <strong>{{ info.key }}&nbsp;:</strong> <span ng-bind-html="html(info.value)"></span>
                  </p>
                  <hr>
                </div>
              </div>
              <div ng-if="!log.data.multiple">
                <p ng-repeat="info in log.data">
                  <strong>{{ info.key }}&nbsp;:</strong> {{ info.value }}
                </p>
                <p ng-if="!log.data">Pas de détails</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <button class="btn btn-primary" ng-click="loadMoreLogs()" ng-if="!bottomReached && !loading">
        Charger plus de logs
      </button>
      <p ng-if="loading">
        Chargement en cours...
      </p>
    </div>
  </div>
</div>
