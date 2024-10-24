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


<div class="container" *ngIf="logs.length > 0">
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
        <td></td>
        <td></td>
        <td>
          <select [(ngModel)]="userFilter" (change)="updateFilter()" class="form-control">
            <option value="">Tous</option>
            <option *ngFor="let user of users" [value]="user">{{ user }}</option>
          </select>
        </td>
        <td>
          <select [(ngModel)]="categoryFilter" (change)="updateFilter()" class="form-control">
            <option value="">Toutes</option>
            <option *ngFor="let category of categories" [value]="category">{{ category }}</option>
          </select>
        </td>
        <td>
          <select [(ngModel)]="actionFilter" (change)="updateFilter()" class="form-control">
            <option value="">Toutes</option>
            <option value="errors">Uniquement les erreurs</option>
            <option value="non-errors">Uniquement les actions réussies</option>
          </select>
        </td>
        <td>
          <select [(ngModel)]="sectionFilter" (change)="updateFilter()" class="form-control">
            <option value="">Toutes</option>
            <option *ngFor="let section of sections" [value]="section">{{ section }}</option>
          </select>
        </td>
      </tr>
    </tbody>
    <tbody *ngFor="let log of logs" [ngClass]="{'log-error': log.isError}" *ngIf="showLog(log)">
      <tr (click)="toggleDetails(log.id)">
        <td>{{ log.id }}</td>
        <td>{{ log.date }}</td>
        <td title="{{ log.userEmail }}">{{ log.user }}</td>
        <td>{{ log.category }}</td>
        <td>{{ log.action }}</td>
        <td>{{ log.section }}</td>
      </tr>
      <tr *ngIf="displayDetails === log.id" (click)="toggleDetails(log.id)">
        <td colspan="6">
          <div *ngIf="log.data.multiple">
            <div *ngFor="let item of log.data.multiple">
              <div *ngFor="let info of item">
                <strong>{{ info.key }}:</strong> <span [innerHTML]="html(info.value)"></span>
              </div>
              <hr>
            </div>
          </div>
          <div *ngIf="!log.data.multiple">
            <div *ngFor="let info of log.data">
              <strong>{{ info.key }}:</strong> <span [innerHTML]="html(info.value)"></span>
            </div>
            <p *ngIf="!log.data">Pas de détails</p>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  
  <button class="btn btn-primary" (click)="loadMoreLogs()" *ngIf="!bottomReached && !loading">
    Charger plus de logs
  </button>
  <p *ngIf="loading">Chargement en cours...</p>
</div>


<!--div ng-controller="LogsController">
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
          <tr ng-show="displayDetails == log.id" ng-click="toggleDetails(log.id)" class="log-details">
            <td></td>
            <td colspan="5">
              <div ng-if="log.data.multiple">
                <div ng-repeat="item in log.data.multiple.slice().reverse()" class="log-multiple-data">
                  <div ng-repeat="info in item" class="row">
                    <div class="col-sm-2 text-right">
                      <strong>{{ info.key }}&nbsp;:&nbsp;</strong>
                    </div>
                    <div class="col-sm-10">
                      <span ng-bind-html="html(info.value)"></span>
                    </div>
                  </div>
                  <hr>
                </div>
              </div>
              <div ng-if="!log.data.multiple">
                <div ng-repeat="info in log.data" class="row">
                  <div class="col-sm-2 text-right">
                    <strong>{{ info.key }}&nbsp;:&nbsp;</strong>
                  </div>
                  <div class="col-sm-10">
                    <span ng-bind-html="html(info.value)"></span>
                  </div>
                </div>
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
</div-->
