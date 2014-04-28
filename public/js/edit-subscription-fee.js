$().ready(function() {
  $(".toggle-subscription-paid-button").click(function() {
    var row = $(this).closest("[data-member-id]");
    var memberId = row.data('member-id');
    row.toggleClass('paid-member unpaid-member');
    addSubscriptionPendingChange(memberId, row.hasClass('paid-member'));
  });
});

var subscriptionPendingChanges = {};
var sendingData = false;

function addSubscriptionPendingChange(memberId, state) {
  console.log("member id " + memberId + " to " + state);
  subscriptionPendingChanges["member-" + memberId] = state;
  setTimeout(commitChanges, 0);
}

function commitChanges() {
  if (sendingData) return;
  if (!Object.keys(subscriptionPendingChanges).length) return;
  console.log ("url: " + commitSubscriptionFeeChangesURL);
  sendingData = true;
  $("#pending-commit").show();
  console.log(subscriptionPendingChanges);
  $.ajax({
    type: "POST",
    url: commitSubscriptionFeeChangesURL,
    data: subscriptionPendingChanges
  }).done(function(json) {
      sendingData = false; // TODO remove
    console.log(json);
    data = JSON.parse(json);
    if (data.result === "Success") {
      sendingData = false;
      if (Object.keys(subscriptionPendingChanges).length) {
        commitChanges();
      } else {
        $("#pending-commit").hide();
      }
    } else {
      alert("Une erreur est survenue lors de l'enregistrement du statut de paiement de la cotisation.");
      // Reload page
      window.location = window.location;
    }
  });
  subscriptionPendingChanges = {};
}
