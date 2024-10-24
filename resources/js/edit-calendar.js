/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

/**
 * This script is present on the calendar management page and provides
 * functionalities to add, modify and delete events
 */

/**
 * Empties and shows the event form for a given day
 */
window.addEvent = function(day) {
  $("#calendar_event_form legend:first").html("Ajouter un événement");
  $("#calendar_event_form [name='start_date_day']").val(day);
  $("#calendar_event_form [name='start_date_month']").val(currentMonth);
  $("#calendar_event_form [name='start_date_year']").val(currentYear);
  if (!$("#calendar_event_form").is(":visible") || $("#calendar_event_form [name='event_id']").val()) {
    $("#calendar_event_form [name='duration_in_days']").val(1);
    $("#calendar_event_form [name='event_name']").val("");
    $("#calendar_event_form [name='description']").val("");
    $("#calendar_event_form [name='event_type']").val('normal');
    $("#calendar_event_form [name='section']").val(currentSection);
    $("#calendar_event_form [name='section']").prop('disabled', false);
    $("#calendar_event_form #delete_link").hide();
    $("#calendar_event_form").slideDown();
  }
  $("#calendar_event_form [name='event_id']").val("");
  document.getElementById("event_name").focus();
  updateMultiSectionSubform();
}

/**
 * Hides the event form
 */
window.dismissEvent = function() {
  $("#calendar_event_form").slideUp();
}

/**
 * Sets the form to match an existing event and shows it
 */
window.editEvent = function(eventId) {
  $("#calendar_event_form legend:first").html("Modifier un événement");
  $("#calendar_event_form [name='event_id']").val(eventId);
  $("#calendar_event_form [name='start_date_day']").val(events[eventId].start_day);
  $("#calendar_event_form [name='start_date_month']").val(events[eventId].start_month);
  $("#calendar_event_form [name='start_date_year']").val(events[eventId].start_year);
  $("#calendar_event_form [name='duration_in_days']").val(events[eventId].duration);
  $("#calendar_event_form [name='event_name']").val(events[eventId].event_name);
  $("#calendar_event_form [name='description']").val(events[eventId].description);
  $("#calendar_event_form [name='event_type']").val(events[eventId].type);
  $("#calendar_event_form [name='section']").val(events[eventId].section);
  $("#calendar_event_form [name='section']").prop('disabled', 'disabled');
  $("#calendar_event_form #delete_link").attr('href', events[eventId].delete_url);
  $("#calendar_event_form #delete_link").show();
  $("#calendar_event_form").slideDown();
  updateMultiSectionSubform();
  document.getElementById("event_name").focus();
}

// Set multi-section form to show or hide
window.updateMultiSectionSubform = function() {
  if ($("#calendar_event_form [name='section']").val() == "multi") {
    $("#calendar_event_form .multi-section-subform").show();
  } else {
    $("#calendar_event_form .multi-section-subform").hide();
  }
}

$().ready(function() {
  $("#calendar_event_form [name='section']").on('change', function() {
    updateMultiSectionSubform();
  });
});
