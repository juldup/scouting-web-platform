/**
 * This script is present on the guest book page
 */

$().ready(function() {
  // Show new message form when button is clicked
  $(".guest-book-button").click(function() {
    $(".guest-book-button").hide();
    $(".guest-book-form").slideDown();
  });
  // Hide new message form when edition is canceled
  $(".guest-book-cancel").click(function() {
    $(".guest-book-form").slideUp(null, function() {
      $(".guest-book-button").show();
    });
    return false;
  });
});
