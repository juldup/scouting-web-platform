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
 * This script is present on the leader privilege management page
 */

$().ready(function() {
  // When a privilege is toggled, add it to the list of pending changes
  $(".privilege-checkbox").on("switch-change", function(event) {
    var privilege = $(this).data('privilege-id');
    var scope = $(this).data('scope');
    var leaderId = $(this).data('leader-id');
    var state = $(this).prop('checked');
    addPendingChange(privilege, scope, leaderId, state);
  });
  // When a privilege category setter is clicked, set all privileges in the category
  $(".privileges-check-all").click(function(event) {
    event.preventDefault();
    var category = $(this).data("category");
    var leaderId = $(this).data("leader-id");
    $("input[data-category='" + category + "'][data-leader-id='" + leaderId + "']:enabled").each(function() {
      $(this).prop('checked', true).trigger('change');
    });
  });
  // When a privilege category unsetter is clicked, set all privileges in the category
  $(".privileges-uncheck-all").click(function(event) {
    event.preventDefault();
    var category = $(this).data("category");
    var leaderId = $(this).data("leader-id");
    $("input[data-category='" + category + "'][data-leader-id='" + leaderId + "']:enabled").each(function() {
      $(this).prop('checked', false).trigger('change');
    });
  });
});

// List of changes that need to be uploaded
var pendingChanges = {};

// Whether data is currently being uploaded (to avoid multiple concurrent requests)
var sendingData = false;

/**
 * Adds a change to the list of pending changes
 * 
 * @param {string} privilege  Id of the privilege
 * @param {string} scope  'U' for unit or 'S' for section
 * @param {integer} leaderId  Leader affected by the change
 * @param {boolean} state  New state
 */
function addPendingChange(privilege, scope, leaderId, state) {
  pendingChanges[privilege + ":" + scope + ":" + leaderId] = state;
  setTimeout(commitChanges, 0);
}

/**
 * Uploads all the changes from the pending list
 */
function commitChanges() {
  // If a request is alrady in progress, return
  if (sendingData) return;
  // If there are no more changes to upload, return
  if (!Object.keys(pendingChanges).length) return;
  // Set upload in progress flag
  sendingData = true;
  // Show synchronization icon
  $("#pending-commit").show();
  // Upload data
  $.ajax({
    type: "POST",
    url: commitPrivilegeChangesURL,
    data: pendingChanges
  }).done(function(json) {
    var data = JSON.parse(json);
    if (data.result === "Success") {
      // Changes have been successfully changed
      // Unset flag
      sendingData = false;
      if (Object.keys(pendingChanges).length) {
        // There are awaiting changes, upload them
        commitChanges();
      } else {
        // Not more pending changes, hide synchronization icon
        $("#pending-commit").hide();
      }
    } else {
      // An error has occured
      alert("Une erreur est survenue lors de l'enregistrement des privilèges.");
      // Reload page
      window.location = window.location;
    }
  });
  // After triggering the upload, empty the list of pending changes
  pendingChanges = {};
}
