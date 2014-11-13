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

/**
 * This script uses angular.js to provide a front-end tool to manage the attendance
 * of a section. Synchronization in done in batches through an AJAX call.
 */

/* ANGULAR MODULE */

// The angular module
var angularAttendance = angular.module('attendance', []);

// The angular controller
angularAttendance.controller('AttendanceController', function($scope) {
	
	/* DATA */
  
  // Whether the user can edit the values (must be set in the page)
  $scope.canEdit = canEdit;
  
  // List of events that are being monitored
  $scope.monitoredEvents = monitoredEvents;
  
  // List of unmonitored events that can be added
  $scope.unmonitoredEvents = unmonitoredEvents;
  
  // List of members
  $scope.members = members;
  
  // Date of the first and last displayed events
  $scope.minDate = "0000-00-00";
  $scope.maxDate = "9999-99-99";
  
  // Formats a date to display it as "DD/MM"
  $scope.formatDate = function(date) {
    return date.substring(8, 10) + "/" + date.substring(5, 7);
  };
  
  /* EDITION */
  
  /**
   * Recomputes the min and max date so that max 10 events are being displayed
   */
  $scope.updateTimeframe = function() {
    if ($scope.monitoredEvents.length <= 10) {
      $scope.minDate = "0000-00-00";
      $scope.maxDate = "9999-99-99";
      return;
    }
    $scope.minDate = $scope.monitoredEvents[$scope.monitoredEvents.length - 10].date;
    $scope.maxDate = "9999-99-99";
  };
  
  // Initially set the time frame
  $scope.updateTimeframe();
  
  /**
   * Shifts the displayed events so that the one on the left is now visible
   */
  $scope.shiftLeft = function() {
    if ($scope.minDate <= $scope.monitoredEvents[0].date) return;
    var i = $scope.monitoredEvents.length - 1;
    while (i > 0 && $scope.monitoredEvents[i].date >= $scope.minDate) i--;
    $scope.minDate = $scope.monitoredEvents[i].date;
    $scope.maxDate = $scope.monitoredEvents[Math.min(i + 9, $scope.monitoredEvents.length - 1)].date;
  };
  
  /**
   * Shifts the displayed events so that the one on the right is now visible
   */
  $scope.shiftRight = function() {
    if ($scope.maxDate >= $scope.monitoredEvents[$scope.monitoredEvents.length - 1].date) return;
    var i = 0;
    while (i < $scope.monitoredEvents.length - 1 && $scope.monitoredEvents[i].date <= $scope.maxDate) i++;
    $scope.maxDate = $scope.monitoredEvents[i].date;
    $scope.minDate = $scope.monitoredEvents[Math.max(i - 9, 0)].date;
  };
  
  /**
   * Changes the attendance status to an event for a member
   */
  $scope.toggle = function(member, event) {
    if (!canEdit) return;
    member.status["event_" + event.id] = !member.status["event_" + event.id];
    $scope.uploadChanges();
  };
  
  /**
   * Set the attendance status of all members to the given value
   */
  $scope.setAll = function(event, status) {
    if (!canEdit) return;
    // Check if there are members with different statuses
    var noneAttended = true;
    var allAttended = true;
    $scope.members.forEach(function(member) {
      if (member.status["event_" + event.id])
        noneAttended = false;
      else
        allAttended = false;
    });
    if (!noneAttended && !allAttended) {
      if (!confirm("Il y a déjà des présences/absences encodées pour cet événement. Les effacer ?")) {
        return;
      }
    }
    // Apply change
    $scope.members.forEach(function(member) {
      member.status["event_" + event.id] = status;
    });
    $scope.uploadChanges();
  };
  
  /**
   * Returns the number of members that have the given status for an event
   */
  $scope.countWithStatus = function(event, status) {
    var total = 0;
    $scope.members.forEach(function(member) {
      if ((member.status["event_" + event.id] && status) || (!member.status["event_" + event.id] && !status))
        total++;
    });
    return total;
  };
  
  /**
   * Returns the number of events with the given status for a member
   */
  $scope.countMemberStatus = function(member, status) {
    var total = 0;
    $scope.monitoredEvents.forEach(function(event) {
      if ((member.status["event_" + event.id] && status) || (!member.status["event_" + event.id] && !status))
        total++;
    });
    return total;
  };
  
  /**
   * Sorts a list of events by date
   */
  $scope.sortEvents = function(events) {
    events.sort(function(a, b) {
      if (a.date < b.date) return -1;
      if (a.date > b.date) return 1;
      // Same date, sort by id
      return a.id - b.id;
    });
  };
  
  /**
   * Removes an event from the monitored list
   */
  $scope.remove = function(event) {
    if (!canEdit) return;
    if (confirm("Supprimer l'activité \"" + event.title + "\" du " + $scope.formatDate(event.date) + " de la liste des présences ?")) {
      $scope.monitoredEvents.splice($scope.monitoredEvents.indexOf(event), 1);
      $scope.unmonitoredEvents.push(event);
      $scope.sortEvents($scope.unmonitoredEvents);
      $scope.updateTimeframe();
      $scope.$$phase || $scope.$apply();
      $scope.uploadChanges();
    }
    return false;
  };
  
  /**
   * Adds an event to the monitored list
   */
  $scope.addUnmonitoredEvent = function(eventId) {
    if (!canEdit) return;
    for (var i = 0; i < $scope.unmonitoredEvents.length; i++) {
      var event = $scope.unmonitoredEvents[i];
      if (event.id == eventId) {
        $scope.unmonitoredEvents.splice(i, 1);
        $scope.monitoredEvents.push(event);
        $scope.sortEvents($scope.monitoredEvents);
        $scope.updateTimeframe();
        $scope.$$phase || $scope.$apply();
        $scope.uploadChanges();
        // Reset select
        setTimeout(function() {
          $("select").val("");
          // Remove unwanted option added by angular
          $("select option[value=]").prev().remove();
        }, 0);
        return;
      }
    }
  };
  
  /* SYNCHRONIZATION */
  
  // Uploading status
  $scope.uploading = false;
  
  // Change counter (to avoid uploading when more recent changes have been made)
  $scope.uploadId = 0;
  
  $scope.updateModel = function() {console.log("updateModel()");};
  
  /**
   * Uploads the current state to the server
   */
  $scope.uploadChanges = function() {
    // If editing is not allowed, don't upload
    if (!$scope.canEdit) return;
    // Increment upload counter
    $scope.uploadId++;
    // Show synchronization icon
    $("#pending-commit").show();
    // Don't upload now if an upload is already running
    if ($scope.uploading) {
      return;
    }
    // Gather events
    var events = [];
    $scope.monitoredEvents.forEach(function(event) { events.push({id: event.id, monitored: true}); });
    $scope.unmonitoredEvents.forEach(function(event) { events.push({id: event.id, monitored: false}); });
    // Get current upload
    var uploadId = $scope.uploadId;
    // Set timeout in a short time, to avoid sending data all the time if
    // others changes are made subsequently
    setTimeout(function() {
      if (uploadId !== $scope.uploadId) {
        // There are more recent changes, don't upload now
      } else {
        // Upload now
        $scope.uploading = true;
        $.ajax({
          type: "POST",
          url: commitAttendanceChangesURL,
          data: {
            'data': JSON.stringify($scope.members),
            'events': JSON.stringify(events)
          }
        }).done(function(json) {
          try {
            data = JSON.parse(json);
            var errorMessage = null;
            if (data.result === "Success") {
              // Upload was successful
              // Stop uploading
              $scope.uploading = false;
              if (uploadId !== $scope.uploadId) {
                // Other changes are waiting for upload, upload them
                $scope.uploadChanges();
              } else {
                // No more pending upload, hide the synchronization icon
                $("#pending-commit").hide();
              }
            } else {
              // An error has occured
              console.error(data.message);
              errorMessage = data.message;
              throw "error";
            }
          } catch (err) {
            // On error, reload the page so the user can see what has actually been saved
            alert(errorMessage ? errorMessage : "Une erreur est survenue lors de l'enregistrement des présences.");
            // Reload page
            window.location = window.location;
          }
        });
      }
    }, 1000); // Upload in 1 second
  };
  
});

// Start module
angular.bootstrap(document, ['attendance']);

// Show page
$("#attendance-wrapper").show();
$("#wait-message").hide();
