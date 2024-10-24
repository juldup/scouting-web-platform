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

/**
 * This script uses angular.js to provide a front-end interface to see the website's logs
 */


import { Component, NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { HttpClient } from '@angular/common/http';
import { HttpClientModule } from '@angular/common/http';
import { DomSanitizer } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';

// Log interface for strong typing
interface Log {
  id: number;
  date: string;
  user: string;
  category: string;
  action: string;
  section: string;
  details: string;
  isError?: boolean;
}

// Main Logs Component
@Component({
  selector: 'app-logs',
  template: `
    <div>
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
          <tr *ngFor="let log of logs" (click)="toggleDetails(log.id)">
            <td>{{ log.id }}</td>
            <td>{{ log.date }}</td>
            <td>{{ log.user }}</td>
            <td>{{ log.category }}</td>
            <td>{{ log.action }}</td>
            <td>{{ log.section }}</td>
          </tr>
          <tr *ngIf="displayDetails === log.id">
            <td colspan="6" [innerHTML]="html(log.details)"></td>
          </tr>
        </tbody>
      </table>

      <button (click)="loadMoreLogs()" [disabled]="loading || bottomReached">Load More Logs</button>
      <div *ngIf="loading">Loading...</div>
      <div *ngIf="bottomReached">No more logs available.</div>
    </div>
  `
})
export class LogsComponent {
  logs: Log[] = [];
  lastKownLogId: number = 0;
  displayDetails: number | null = null;
  bottomReached: boolean = false;
  categories: string[] = [];
  users: string[] = [];
  sections: string[] = [];
  loading: boolean = false;
  categoryFilter: string = '';
  userFilter: string = '';
  sectionFilter: string = '';
  actionFilter: string = '';

  constructor(private http: HttpClient, private sanitizer: DomSanitizer) {}

  // Function to sanitize HTML content
  html(value: string) {
    return this.sanitizer.bypassSecurityTrustHtml(value);
  }

  // Function to fetch more logs from the server
  loadMoreLogs() {
    if (this.loading || this.bottomReached) return;

    this.loading = true;
    const url = window.loadMoreLogsURL.replace('LOG_ID', this.lastKownLogId.toString());
    
    this.http.get(url).subscribe({
      next: (response: any) => {
        const data: Log[] = response; // Assuming the server returns logs as an array of objects
        let atLeastOneVisible = false;

        // Add all logs to the list
        data.forEach(newLog => {
          this.logs.push(newLog);
          this.lastKownLogId = newLog.id;

          // Update category filter list
          if (newLog.category && this.categories.indexOf(newLog.category) === -1) {
            this.categories.push(newLog.category);
            this.categories.sort();
          }
          // Update user filter list
          if (newLog.user && this.users.indexOf(newLog.user) === -1) {
            this.users.push(newLog.user);
            this.users.sort();
          }
          // Update section filter list
          if (newLog.section && this.sections.indexOf(newLog.section) === -1) {
            this.sections.push(newLog.section);
            this.sections.sort();
          }
          // Check if this log is visible under the current filters
          if (!atLeastOneVisible && this.showLog(newLog)) {
            atLeastOneVisible = true;
          }
        });

        // Check if the last log has been downloaded
        if (data.length < window.logsPerRequest) {
          this.bottomReached = true;
        }
        
        if (atLeastOneVisible || this.bottomReached) {
          this.loading = false;
        } else {
          // No new visible log, try downloading more
          this.loadMoreLogs();
        }
      },
      error: () => {
        alert("Une erreur est survenue. Recharge la page.");
        this.loading = false;
      }
    });
  }

  // Function to toggle log details
  toggleDetails(logId: number) {
    this.displayDetails = this.displayDetails === logId ? null : logId;
  }

  // Function to check if a log is visible based on filters
  showLog(log: Log) {
    return (
      (!this.categoryFilter || log.category === this.categoryFilter) &&
      (!this.userFilter || log.user === this.userFilter) &&
      (!this.sectionFilter || log.section === this.sectionFilter) &&
      (!this.actionFilter || 
        (this.actionFilter === 'errors' && log.isError) || 
        (this.actionFilter === 'non-errors' && !log.isError))
    );
  }
}

// Define Angular Module
@NgModule({
  declarations: [LogsComponent],
  imports: [BrowserModule, FormsModule, HttpClientModule],
  bootstrap: [LogsComponent]
})
export class LogsModule {}





// // The angular module
// window.angularLogs = angular.module('logs', []);

// // The angular controller
// window.angularLogs.controller('LogsController', function ($scope, $sce) {
	
//   // Current list of logs in reverse order
// 	$scope.logs = [];
  
//   // Last log of the list (to request the subsequent ones)
//   $scope.lastKownLogId = 0;
  
//   // Currently selected log (details of one log can be shown at a time)
//   $scope.displayDetails = null;
  
//   // Whether the last log has been downloaded
//   $scope.bottomReached = false;
  
//   // Filters
//   $scope.categories = [];
//   $scope.categoryFilter = "";
//   $scope.users = [];
//   $scope.userFilter = "";
//   $scope.sections = [];
//   $scope.sectionFilter = "";
//   $scope.loading = false;
//   $scope.actionFilter = "";
  
//   $scope.html = function(value) {
//     console.log(value);
//     return $sce.trustAsHtml(value);
//   };
  
//   $scope.test = $sce.trustAsHtml("<strong>Hello world</strong>");
  
//   /**
//    * Fetches the next logs from the server
//    */
//   $scope.loadMoreLogs = function() {
//     // Flag as loading
//     $scope.loading = true;
//     $scope.$$phase || $scope.$apply();
//     // Mark request
//     window.jquery.ajax({
//       type: 'GET',
//       url: window.loadMoreLogsURL.replace('LOG_ID', $scope.lastKownLogId),
//       success: function(json, xxx, yyy) {
//         var data = JSON.parse(json);
//         var atLeastOneVisible = false;
//         // Add all logs to the list
//         data.forEach(function(newLog) {
//           // Add log
//           $scope.logs.push(newLog);
//           $scope.lastKownLogId = newLog.id;
//           // Update category filter list
//           if ($scope.categories.indexOf(newLog.category) === -1 && newLog.category) {
//             $scope.categories.push(newLog.category);
//             $scope.categories.sort();
//           }
//           // Update user filter list
//           if ($scope.users.indexOf(newLog.user) === -1 && newLog.user) {
//             $scope.users.push(newLog.user);
//             $scope.users.sort();
//           }
//           // Update section filter list
//           if ($scope.sections.indexOf(newLog.section) === -1 && newLog.section) {
//             $scope.sections.push(newLog.section);
//             $scope.sections.sort();
//           }
//           // Check if this log is visible under the current filters
//           if (!atLeastOneVisible && $scope.showLog(newLog)) {
//             atLeastOneVisible = true;
//           }
//         });
//         // Check if the last log has been downloaded
//         if (data.length < window.logsPerRequest) $scope.bottomReached = true;
//         if (atLeastOneVisible || $scope.bottomReached) {
//           // Done
//           $scope.loading = false;
//           $scope.$$phase || $scope.$apply();
//         } else {
//           // No new visible log, try downloading more
//           $scope.loadMoreLogs();
//         }
//       },
//       error: function() {
//         alert("Une erreur est survenue. Recharge la page.");
//         $scope.loading = false;
//         $scope.$$phase || $scope.$apply();
//       }
//     });
//   };
  
//   // Initially load logs
//   $scope.loadMoreLogs();
	
//   /**
//    * [Called from page] Shows/hides the details of a log
//    */
//   $scope.toggleDetails = function(logId) {
//     $scope.displayDetails = $scope.displayDetails == logId ? null : logId;
//     $scope.$$phase || $scope.$apply();
//   };
  
//   /**
//    * Refresh the page with the new filter values
//    */
//   $scope.updateFilter = function() {
//     $scope.$$phase || $scope.$apply();
//   };
  
//   /**
//    * Returns whether a given log is visible with the current filters
//    */
//   $scope.showLog = function(log) {
//     return (!$scope.categoryFilter || log.category === $scope.categoryFilter) &&
//             (!$scope.userFilter || log.user === $scope.userFilter) &&
//             (!$scope.sectionFilter || log.section === $scope.sectionFilter) &&
//             (!$scope.actionFilter || ($scope.actionFilter === "errors" && log.isError) || ($scope.actionFilter === "non-errors" && !log.isError));
//   };
  
// });

// // Start module
// window.angular.bootstrap(document, ['logs']);
