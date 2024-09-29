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

// The angular module
var angularLogs = angular.module('logs', []);

// The angular controller
angularLogs.controller('LogsController', function ($scope, $sce) {
	
  // Current list of logs in reverse order
	$scope.logs = [];
  
  // Last log of the list (to request the subsequent ones)
  $scope.lastKownLogId = 0;
  
  // Currently selected log (details of one log can be shown at a time)
  $scope.displayDetails = null;
  
  // Whether the last log has been downloaded
  $scope.bottomReached = false;
  
  // Filters
  $scope.categories = [];
  $scope.categoryFilter = "";
  $scope.users = [];
  $scope.userFilter = "";
  $scope.sections = [];
  $scope.sectionFilter = "";
  $scope.loading = false;
  $scope.actionFilter = "";
  
  $scope.html = function(value) {
    console.log(value);
    return $sce.trustAsHtml(value);
  };
  
  $scope.test = $sce.trustAsHtml("<strong>Hello world</strong>");
  
  /**
   * Fetches the next logs from the server
   */
  $scope.loadMoreLogs = function() {
    // Flag as loading
    $scope.loading = true;
    $scope.$$phase || $scope.$apply();
    // Mark request
    $.ajax({
      type: 'GET',
      url: loadMoreLogsURL.replace('LOG_ID', $scope.lastKownLogId),
      success: function(json, xxx, yyy) {
        var data = JSON.parse(json);
        var atLeastOneVisible = false;
        // Add all logs to the list
        data.forEach(function(newLog) {
          // Add log
          $scope.logs.push(newLog);
          $scope.lastKownLogId = newLog.id;
          // Update category filter list
          if ($scope.categories.indexOf(newLog.category) === -1 && newLog.category) {
            $scope.categories.push(newLog.category);
            $scope.categories.sort();
          }
          // Update user filter list
          if ($scope.users.indexOf(newLog.user) === -1 && newLog.user) {
            $scope.users.push(newLog.user);
            $scope.users.sort();
          }
          // Update section filter list
          if ($scope.sections.indexOf(newLog.section) === -1 && newLog.section) {
            $scope.sections.push(newLog.section);
            $scope.sections.sort();
          }
          // Check if this log is visible under the current filters
          if (!atLeastOneVisible && $scope.showLog(newLog)) {
            atLeastOneVisible = true;
          }
        });
        // Check if the last log has been downloaded
        if (data.length < logsPerRequest) $scope.bottomReached = true;
        if (atLeastOneVisible || $scope.bottomReached) {
          // Done
          $scope.loading = false;
          $scope.$$phase || $scope.$apply();
        } else {
          // No new visible log, try downloading more
          $scope.loadMoreLogs();
        }
      },
      error: function() {
        alert("Une erreur est survenue. Recharge la page.");
        $scope.loading = false;
        $scope.$$phase || $scope.$apply();
      }
    });
  };
  
  // Initially load logs
  $scope.loadMoreLogs();
	
  /**
   * [Called from page] Shows/hides the details of a log
   */
  $scope.toggleDetails = function(logId) {
    $scope.displayDetails = $scope.displayDetails == logId ? null : logId;
    $scope.$$phase || $scope.$apply();
  };
  
  /**
   * Refresh the page with the new filter values
   */
  $scope.updateFilter = function() {
    $scope.$$phase || $scope.$apply();
  };
  
  /**
   * Returns whether a given log is visible with the current filters
   */
  $scope.showLog = function(log) {
    return (!$scope.categoryFilter || log.category === $scope.categoryFilter) &&
            (!$scope.userFilter || log.user === $scope.userFilter) &&
            (!$scope.sectionFilter || log.section === $scope.sectionFilter) &&
            (!$scope.actionFilter || ($scope.actionFilter === "errors" && log.isError) || ($scope.actionFilter === "non-errors" && !log.isError));
  };
  
});

// Start module
angular.bootstrap(document, ['logs']);
