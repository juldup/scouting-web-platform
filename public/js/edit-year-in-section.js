$().ready(function() {
  $(".increase-year-button, .decrease-year-button").on('click', function() {
    var row = $(this).closest('.member-row');
    var memberId = row.data('member-id');
    var currentYear = parseInt(row.find('.member-year').text().trim());
    var newYear;
    if ($(this).hasClass('increase-year-button')) newYear = currentYear + 1;
    else newYear = currentYear - 1;
    if (newYear <= 0) return false;
    $.ajax({
      url: changeYearURL,
      data: {member_id: memberId, year: newYear}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        row.find('.member-year').text(newYear);
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
  $(".increase-all-button").on('click', function() {
    $.ajax({
      url: changeYearURL,
      data: {section_id: currentSectionId}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        var newYears = data.years;
        $('.member-row').each(function() {
          var memberId = $(this).data('member-id');
          if (memberId !== undefined) {
            var newYear = newYears[memberId];
            $(this).find('.member-year').text(newYear);
          }
        });
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
});
