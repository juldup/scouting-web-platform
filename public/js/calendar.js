function addEvent(day) {
  $("#calendar_event_form [name='start_date_day']").val(day);
  $("#calendar_event_form [name='start_date_month']").val(currentMonth);
  $("#calendar_event_form [name='start_date_year']").val(currentYear);
  if (!$("#calendar_event_form").is(":visible") || $("#calendar_event_form [name='event_id']").val()) {
    $("#calendar_event_form [name='duration_in_days']").val(1);
    $("#calendar_event_form [name='event_name']").val("");
    $("#calendar_event_form [name='description']").val("");
    $("#calendar_event_form [name='event_type']").val('normal');
    $("#calendar_event_form [name='section']").val(currentSection);
    $("#calendar_event_form #delete_link").hide();
    $("#calendar_event_form").slideDown();
  }
  $("#calendar_event_form [name='event_id']").val("");
}

function dismissEvent() {
  console.log($("#calendar_event_form"));
  $("#calendar_event_form").slideUp();
}

function editEvent(eventId) {
  $("#calendar_event_form [name='event_id']").val(eventId);
  $("#calendar_event_form [name='start_date_day']").val(events[eventId].start_day);
  $("#calendar_event_form [name='start_date_month']").val(events[eventId].start_month);
  $("#calendar_event_form [name='start_date_year']").val(events[eventId].start_year);
  $("#calendar_event_form [name='duration_in_days']").val(events[eventId].duration);
  $("#calendar_event_form [name='event_name']").val(events[eventId].event_name);
  $("#calendar_event_form [name='description']").val(events[eventId].description);
  $("#calendar_event_form [name='event_type']").val(events[eventId].type);
  $("#calendar_event_form [name='section']").val(events[eventId].section);
  $("#calendar_event_form #delete_link").attr('href', events[eventId].delete_url);
  $("#calendar_event_form #delete_link").show();
  $("#calendar_event_form").slideDown();
}
