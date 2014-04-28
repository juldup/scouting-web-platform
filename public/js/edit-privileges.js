$().ready(function() {
  $(".privilege-checkbox").on("switch-change", function(event) {
    var privilege = $(this).data('privilege-id');
    var scope = $(this).data('scope');
    var leaderId = $(this).data('leader-id');
    var state = $(this).prop('checked');
    addPendingChange(privilege, scope, leaderId, state);
  });
  $(".privileges-check-all").click(function(event) {
    event.preventDefault();
    var category = $(this).data("category");
    var leaderId = $(this).data("leader-id");
    $("input[data-category='" + category + "'][data-leader-id='" + leaderId + "']:enabled").each(function() {
      $(this).prop('checked', true).trigger('change');
    });
  });
  $(".privileges-uncheck-all").click(function(event) {
    event.preventDefault();
    var category = $(this).data("category");
    var leaderId = $(this).data("leader-id");
    $("input[data-category='" + category + "'][data-leader-id='" + leaderId + "']:enabled").each(function() {
      $(this).prop('checked', false).trigger('change');
    });
  });
});

var pendingChanges = {};
var sendingData = false;

function addPendingChange(privilege, scope, leaderId, state) {
  pendingChanges[privilege + ":" + scope + ":" + leaderId] = state;
  setTimeout(commitChanges, 0);
}

function commitChanges() {
  if (sendingData) return;
  if (!Object.keys(pendingChanges).length) return;
  sendingData = true;
  $("#pending-commit").show();
  $.ajax({
    type: "POST",
    url: commitPrivilegeChangesURL,
    data: pendingChanges
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result === "Success") {
      sendingData = false;
      if (Object.keys(pendingChanges).length) {
        commitChanges();
      } else {
        $("#pending-commit").hide();
      }
    } else {
      alert("Une erreur est survenue lors de l'enregistrement des privilèges.");
      // Reload page
      window.location = window.location;
    }
  });
  pendingChanges = {};
}
