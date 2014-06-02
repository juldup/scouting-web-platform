/**
 * This script is present on the e-mail management page
 */

$().ready(function() {
  // Add confirmation on the archive buttons
  $(".archive-email-button").click(function() {
    return confirm("Archiver cet e-mail ?");
  });
  // Show/hide e-mail recipient list on click
  $(".email-recipient-list").click(function() {
    $(this).find(".email-recipient-list-content").toggle();
  })
});
