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
 * This script uses angular.js to provide a front-end tool to manage the payments for the events
 * of a section. Synchronization in done in batches through an AJAX call.
 */

/* ANGULAR MODULE */

// The angular module
var angularPayment = angular.module('payment', []);

// The angular controller
angularPayment.controller('PaymentController', function($scope) {
	
	/* DATA */
  
  // Whether the user can edit the values (must be set in the page)
  $scope.canEdit = canEdit;
  
  // List of events
  $scope.events = events;
  
  // List of deleted events
  $scope.deletedEvents = [];
  
  // List of members
  $scope.members = members;
  
  // Id of the first and last displayed events
  $scope.minId = 0;
  $scope.maxId = 999999999;
  
  // Max number of events displayed
  var frameSize = 6;
  
  /* EDITION */
  
  /**
   * Recomputes the min and max id so that max *frameSize* events are being displayed
   */
  $scope.updateTimeframe = function() {
    if ($scope.events.length <= frameSize) {
      $scope.minId = 0;
      $scope.maxId = 999999999;
      return;
    }
    $scope.minId = $scope.events[$scope.events.length - frameSize].id;
    $scope.maxId = 999999999;
  };
  
  // Initially set the time frame
  $scope.updateTimeframe();
  
  /**
   * Shifts the displayed events so that the one on the left is now visible
   */
  $scope.shiftLeft = function() {
    if ($scope.minId <= $scope.events[0].id) return;
    var i = $scope.events.length - 1;
    while (i > 0 && $scope.events[i].id >= $scope.minId) i--;
    $scope.minId = $scope.events[i].id;
    $scope.maxId = $scope.events[Math.min(i + frameSize - 1, $scope.events.length - 1)].id;
  };
  
  /**
   * Shifts the displayed events so that the one on the right is now visible
   */
  $scope.shiftRight = function() {
    if ($scope.maxId >= $scope.events[$scope.events.length - 1].id) return;
    var i = 0;
    while (i < $scope.events.length - 1 && $scope.events[i].id <= $scope.maxId) i++;
    $scope.maxId = $scope.events[i].id;
    $scope.minId = $scope.events[Math.max(i - (frameSize-1), 0)].id;
  };
  
  /**
   * Changes the payment status to an event for a member
   */
  $scope.toggle = function(member, event) {
    if (!canEdit) return;
    member.status["event_" + event.id] = !member.status["event_" + event.id];
    $scope.uploadChanges();
  };
  
  /**
   * Set the payment status of all members to the given value
   */
  $scope.setAll = function(event, status) {
    if (!canEdit) return;
    // Check if there are members with different statuses
    var nonePaid = true;
    var allPaid = true;
    $scope.members.forEach(function(member) {
      if (member.status["event_" + event.id])
        nonePaid = false;
      else
        allPaid = false;
    });
    if (!nonePaid && !allPaid) {
      if (!confirm("Il y a déjà des paiements encodés pour cette activité. Les effacer ?")) {
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
    $scope.events.forEach(function(event) {
      if ((member.status["event_" + event.id] && status) || (!member.status["event_" + event.id] && !status))
        total++;
    });
    return total;
  };
  
  /**
   * Sorts a list of events by id
   */
  $scope.sortEvents = function(events) {
    events.sort(function(a, b) {
      if (a.id < b.id) return -1;
      if (a.id > b.id) return 1;
      return 0;
    });
  };
  
  /**
   * Removes an event from the monitored list
   */
  $scope.remove = function(event) {
    // Check if there are members with different statuses
    var somePaid = false;
    $scope.members.forEach(function(member) {
      if (member.status["event_" + event.id]) {
        somePaid = true;
      }
    });
    if (somePaid) {
      if (!confirm("Il y a déjà des paiements encodés pour cette activité. La supprimer quand même ?")) {
        return false;
      }
    }
    // Request delete
    $.ajax({
      url: deleteEventURL,
      type: "POST",
      data: {
        eventId: event.id
      },
      success: function(data) {
        $scope.events.splice($scope.events.indexOf(event), 1);
        $scope.updateTimeframe();
        $scope.$$phase || $scope.$apply();
      },
      error: function(data) {
        errorMessage = "Une erreur est survenue lors de la suppression d'une activité.";
        if (data && data.responseJSON && data.responseJSON.errorMessage) errorMessage = data.responseJSON.errorMessage;
        alert(errorMessage);
      }
    });
    return false;
  };
  
  /**
   * Adds an event to the list
   */
  $scope.addEvent = function() {
    // Check new name validity
    var newEventName = $("#new-event-input").val().trim();
    if (!newEventName) {
      alert("Tu dois entrer un nom pour cette activité.");
      return;
    }
    for (var i = 0; i < $scope.events.length; i++) {
      if ($scope.events[i].name == newEventName) {
        alert("Cette activité existe déjà. Choisis un autre nom.");
        return;
      }
    }
    // Submit
    $("#new-event-form input[type=submit]").prop('disabled', true);
    $.ajax({
      url: postNewEventURL,
      type: "POST",
      data: {
        name: newEventName
      },
      success: function(data) {
        $("#new-event-form input[type=submit]").prop('disabled', false);
        $("#new-event-input").val("");
        console.log(data);
        $scope.events.push({id: data.id, name: newEventName});
        $scope.updateTimeframe();
        $scope.$$phase || $scope.$apply();
      },
      error: function(data) {
        $("#new-event-form input[type=submit]").prop('disabled', false);
        errorMessage = "Une erreur est survenue lors de l'ajout d'une activité.";
        if (data && data.responseJSON && data.responseJSON.errorMessage) errorMessage = data.responseJSON.errorMessage;
        alert(errorMessage);
      }
    });
  };
  
  /* SYNCHRONIZATION */
  
  // Uploading status
  $scope.uploading = false;
  
  // True if some changes are yet unsynchronized
  $scope.unsynchronized = false;
  
  // Change counter (to avoid uploading when more recent changes have been made)
  $scope.uploadId = 0;
  
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
    $scope.unsynchronized = true;
    // Don't upload now if an upload is already running
    if ($scope.uploading) {
      return;
    }
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
          url: commitPaymentChangesURL,
          data: {
            'data': JSON.stringify($scope.members)
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
                $scope.unsynchronized = false;
              }
            } else {
              // An error has occured
              console.error(data.message);
              errorMessage = data.message;
              throw "error";
            }
          } catch (err) {
            // On error, reload the page so the user can see what has actually been saved
            alert(errorMessage ? errorMessage : "Une erreur est survenue lors de l'enregistrement des paiements.");
            // Reload page
            window.location = window.location;
          }
        });
      }
    }, 1000); // Upload in 1 second
  };
  
  // Prevent leaving page before everything is synchronized
  window.onbeforeunload = function() {
    if ($scope.unsynchronized) {
      return 'Les changements ne sont pas encore tous sauvés. Quitter quand même ?';
    }
  };
  $('a').filter('[href!="#"]').on('click', function () {
    if ($scope.unsynchronized) {
      return confirm('Les changements ne sont pas encore tous sauvés. Quitter quand même ?');
    }
  });
  
});

// Start module
angular.bootstrap(document, ['payment']);

// Show page
$("#payment-wrapper").show();
$("#wait-message").hide();
