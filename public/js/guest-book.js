$().ready(function() {
  $(".guest-book-button").click(function() {
    $(".guest-book-button").hide();
    $(".guest-book-form").slideDown();
  });
  $(".guest-book-cancel").click(function() {
    $(".guest-book-form").slideUp(null, function() {
      $(".guest-book-button").show();
    });
    return false;
  });
});