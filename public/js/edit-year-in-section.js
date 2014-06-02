/**
 * This script is present on the year in section management page
 */

$().ready(function() {
  // Increase/decrease year button action
  $(".increase-year-button, .decrease-year-button").on('click', function() {
    // Get row, its member and their current year in the section
    var row = $(this).closest('.member-row');
    var memberId = row.data('member-id');
    var currentYear = parseInt(row.find('.member-year').text().trim());
    // Compute new year
    var newYear;
    if ($(this).hasClass('increase-year-button')) newYear = currentYear + 1;
    else newYear = currentYear - 1;
    // Make sure year is at least 1
    if (newYear <= 0) return false;
    // Save change
    $.ajax({
      url: changeYearURL,
      data: {member_id: memberId, year: newYear}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        // Update text once it has been saved
        row.find('.member-year').text(newYear);
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
  // Increase all by one button action
  $(".increase-all-button").on('click', function() {
    // Send request to server
    $.ajax({
      url: changeYearURL,
      data: {section_id: currentSectionId}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        var newYears = data.years;
        // Update all years on the page
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
