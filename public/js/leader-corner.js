/**
 * This script is present on the leader corner page
 */

$().ready(function() {
  // Show help when clicking the help button
  $(".help-badge").click(function(event) {
    event.stopPropagation();
    var help = $(this).closest(".leader-help-item").data('leader-help');
    $(".leader-corner-help:visible").hide();
    $(".leader-corner-help[data-leader-help='" + help + "'").show();
    $(".leader-help-general").hide();
  });
  // Hide help when clicking on the back-to-top icon
  $(".back-to-top").click(function(event) {
    $(".leader-corner-help:visible").hide();
    $(".leader-help-general").show();
  });
});
