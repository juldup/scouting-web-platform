/**
 * This script is present on the suggestion management page
 */

$().ready(function() {
  // Edit response button
  $(".suggestion-edit-response-button").click(function() {
    var responseBox = $(this).closest('.suggestion-response').find('.suggestion-edit-response');
    // Toggle response box
    if (responseBox.is(':visible')) {
      responseBox.slideUp();
    } else {
      $(".suggestion-edit-response").slideUp();
      $(this).closest('.suggestion-response').find('.suggestion-edit-response').slideDown();
      $(this).closest('.suggestion-response').find('.suggestion-edit-response textarea').focus();
    }
    return false;
  });
});