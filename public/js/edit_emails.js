$().ready(function() {
  $(".archive-email-button").click(function() {
    return confirm("Archiver cet e-mail ?");
  });
  $(".email-recipient-list").click(function() {
    $(this).find(".email-recipient-list-content").toggle();
  })
});