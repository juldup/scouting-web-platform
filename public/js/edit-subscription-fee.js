/**
 * This script is present on the subscription fee management page
 */

$().ready(function() {
  // Toggle fee paid button
  $(".toggle-subscription-paid-button").click(function() {
    var row = $(this).closest("[data-member-id]");
    var memberId = row.data('member-id');
    row.toggleClass('paid-member unpaid-member');
    addSubscriptionPendingChange(memberId, row.hasClass('paid-member'));
  });
});

// List of subscription fee paid pending changes
var subscriptionPendingChanges = {};

// Whether a request is in progress
var sendingData = false;

/**
 * Adds the fee paid change to the pending list
 */
function addSubscriptionPendingChange(memberId, state) {
  subscriptionPendingChanges["member-" + memberId] = state;
  setTimeout(commitChanges, 0);
}

/**
 * Sends a request to save all pending changes
 */
function commitChanges() {
  // Don't run two current request
  if (sendingData) return;
  // Return if there are no more pending changes to send
  if (!Object.keys(subscriptionPendingChanges).length) return;
  // Flag as request active
  sendingData = true;
  // Show synchronization icon
  $("#pending-commit").show();
  // Send data
  $.ajax({
    type: "POST",
    url: commitSubscriptionFeeChangesURL,
    data: subscriptionPendingChanges
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result === "Success") {
      // Changes were successfully saved
      sendingData = false;
      if (Object.keys(subscriptionPendingChanges).length) {
        // Upload remaining changes
        commitChanges();
      } else {
        // Not more changes to upload, hide synchronization icon
        $("#pending-commit").hide();
      }
    } else {
      // An error occured
      alert("Une erreur est survenue lors de l'enregistrement du statut de paiement de la cotisation.");
      // Reload page
      window.location = window.location;
    }
  });
  // Empty pending changes object after it has been sent
  subscriptionPendingChanges = {};
}
