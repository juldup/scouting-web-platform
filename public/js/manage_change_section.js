$().ready(function() {
  $(".transfer-button").on('click', function() {
    var row = $(this).closest(".member-row");
    row.find('.transfered').show();
    row.find('.untransfered').hide();
    row.find('.transfered-checkbox').prop('checked', true);
    $(".submit-button").prop('disabled', false);
    return false;
  });
  $(".untransfer-button").on('click', function() {
    var row = $(this).closest(".member-row");
    row.find('.transfered').hide();
    row.find('.untransfered').show();
    row.find('.transfered-checkbox').prop('checked', false);
    if ($(".transfered:visible").length === 0) {
      $(".submit-button").prop('disabled', true);
    }
    return false;
  });
  $(".section-selector").on('change', function() {
    updateDestination();
  });
  updateDestination();
});

function updateDestination() {
  var section = $(".section-selector option:selected").text();
  $(".destination-section").text(section);
}