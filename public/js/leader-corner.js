var shownHelp = false;

$().ready(function() {
  // Show help when clicking the help button
  $(".help-badge").click(function(event) {
    event.stopPropagation();
    shownHelp = true;
    var help = $(this).closest(".leader-help-item").data('leader-help');
    $(".leader-corner-help:visible").hide();
    $(".leader-corner-help[data-leader-help='" + help + "'").show();
    $(".leader-help-general").hide();
  });
  // Hide help when clicking anywhere else
  $("body").click(function(event) {
    if (shownHelp) {
      shownHelp = false;
      $(".leader-corner-help:visible").hide();
      $(".leader-help-general").show();
    }
  });
});
