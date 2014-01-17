$().ready(function() {
  $(".suggestion-edit-response-button").click(function() {
    var responseBox = $(this).closest('.suggestion-response').find('.suggestion-edit-response');
    if (responseBox.is(':visible')) {
      responseBox.slideUp();
    } else {
      $(".suggestion-edit-response").slideUp();
      $(this).closest('.suggestion-response').find('.suggestion-edit-response').slideDown();
    }
    return false;
  });
});