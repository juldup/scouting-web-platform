/**
 * This script is present on the section transfer page and provides
 * functionalities to transfer scouts from one section to another
 */

$().ready(function() {
  // Button to mark a scout for transfer to the current destination section
  $(".transfer-button").on('click', function() {
    // Get row of the member
    var row = $(this).closest(".member-row");
    // Update row
    row.find('.transfered').show();
    row.find('.untransfered').hide();
    // Mark transfer
    row.find('.transfered-checkbox').prop('checked', true);
    // Show submit button
    $(".submit-button").prop('disabled', false);
    return false;
  });
  // Button to unmark the transfer of a scout
  $(".untransfer-button").on('click', function() {
    // Get row
    var row = $(this).closest(".member-row");
    // Update row
    row.find('.transfered').hide();
    row.find('.untransfered').show();
    // Unmark transfer
    row.find('.transfered-checkbox').prop('checked', false);
    // Hide submit row if there are no more members marked for transfer
    if ($(".transfered:visible").length === 0) {
      $(".submit-button").prop('disabled', true);
    }
    return false;
  });
  // Selector to select the destination section
  $(".section-selector").on('change', function() {
    updateDestination();
  });
  // Initially select the default destination section
  updateDestination();
});

/**
 * Updates the hidden field containing the destination section
 */
function updateDestination() {
  var section = $(".section-selector option:selected").text();
  $(".destination-section").text(section);
}
