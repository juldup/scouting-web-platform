/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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

$().ready(function() {
  // Submit scout-to-leader form when a scout is selected
  $("#scout_to_leader select").bind('change', function() {
    $("#scout_to_leader form").trigger('submit');
  });
  // Add confirmation on delete button
  $(".warning-delete").click(function() {
    return confirm("Veux-tu vraiment supprimer cet animateur du listing ?");
  });
  // Hide the current edit form
  $(".dismiss-form").click(function(event) {
    event.preventDefault();
    $(this).closest('.member-form-wrapper').slideUp();
  });
  // When starting with the form displayed, hide the form that does not correspond to the current modification
  if ($("#own-data-form [name='member_id']").val() && $.inArray($("#own-data-form [name='member_id']").val(), ownedLeaders) != -1) {
    $("#member_form").hide();
  } else {
    $("#own-data-form").hide();
  }
});

/**
 * Empties and shows the leader edit form
 */
function addLeader(sectionId) {
  $("#member_form legend:first").html("Ajouter un animateur");
  $("#member_form [name='member_id']").val("");
  $("#member_form [name='first_name']").val("");
  $("#member_form [name='last_name']").val("");
  $("#member_form [name='birth_date']").val("");
  $("#member_form [name='gender']").val("M");
  $("#member_form [name='nationality']").val("");
  $("#member_form [name='address']").val("");
  $("#member_form [name='postcode']").val("");
  $("#member_form [name='city']").val("");
  $("#member_form [name='has_handicap']").prop("checked", false).trigger("change");
  $("#member_form [name='handicap_details']").val("");
  $("#member_form [name='comments']").val("");
  $("#member_form [name='leader_name']").val("");
  $("#member_form [name='leader_in_charge']").prop("checked", false).trigger("change");
  $("#member_form [name='leader_description']").val("");
  $("#member_form [name='leader_role']").val("");
  $("#member_form [name='section']").val(sectionId);
  $("#member_form [name='subgroup']").val("");
  $("#member_form [name='role']").val("");
  $("#member_form [name='phone_member']").val("");
  $("#member_form [name='phone_member_private']").prop("checked", false).trigger("change");
  $("#member_form [name='email_member']").val("");
  $("#member_form [name='totem']").val("");
  $("#member_form [name='quali']").val("");
  $("#member_form img.edit_listing_picture").attr("src", "");
  $("#member_form [name='family_in_other_units']").val(0);
  $("#member_form [name='family_in_other_units_details']").val("");
  $("#member_form #current_leader_picture").attr("src", "");
  $("#member_form #current_leader_picture").hide();
  $("#member_form").slideDown();
}

/**
 * Hides the member form
 */
function dismissMemberForm() {
  $("#member_form").slideUp();
}

/**
 * Shows the member for to edit the user's own data and
 * hides the regular member form
 */
function editOwnData(leaderId) {
  showEditLeaderForm($("#own-data-form"), leaderId);
  if ($("#member_form").is(":visible")) {
    $("#own-data-form").show();
    $("#member_form").hide();
  } else {
    $("#own-data-form").slideDown();
  }
}

/**
 * Sets the edit leader form to match a given member and shows it.
 * Sets the form title according to scoutToLeader boolean (whether we are turning a scout into a leader).
 * Hides the edit own data form if it is visible.
 */
function editLeader(leaderId, scoutToLeader) {
  if (scoutToLeader)
    $("#member_form legend:first").html("Transformer un scout en animateur");
  else
    $("#member_form legend:first").html("Modifier un animateur");
  showEditLeaderForm($("#member_form"), leaderId);
  if ($("#own-data-form").is(":visible")) {
    $("#own-data-form").hide();
    $("#member_form").show();
  } else {
    $("#member_form").slideDown();
  }
}

/**
 * Sets the given leader form to match the geven leader and shows it
 */
function showEditLeaderForm(form, leaderId) {
  form.find("[name='member_id']").val(leaderId);
  form.find("[name='first_name']").val(leaders[leaderId].first_name);
  form.find("[name='last_name']").val(leaders[leaderId].last_name);
  form.find("[name='birth_date_day']").val(leaders[leaderId].birth_date_day);
  form.find("[name='birth_date_month']").val(leaders[leaderId].birth_date_month);
  form.find("[name='birth_date_year']").val(leaders[leaderId].birth_date_year);
  form.find("[name='gender']").val(leaders[leaderId].gender);
  form.find("[name='nationality']").val(leaders[leaderId].nationality);
  form.find("[name='address']").val(leaders[leaderId].address);
  form.find("[name='postcode']").val(leaders[leaderId].postcode);
  form.find("[name='city']").val(leaders[leaderId].city);
  form.find("[name='has_handicap']").prop("checked", leaders[leaderId].has_handicap).trigger("change");
  form.find("[name='handicap_details']").val(leaders[leaderId].handicap_details);
  form.find("[name='comments']").val(leaders[leaderId].comments);
  form.find("[name='leader_name']").val(leaders[leaderId].leader_name);
  form.find("[name='leader_in_charge']").prop("checked", leaders[leaderId].leader_in_charge).trigger("change");
  form.find("[name='leader_description']").val(leaders[leaderId].leader_description);
  form.find("[name='leader_role']").val(leaders[leaderId].leader_role);
  form.find("[name='section']").val(leaders[leaderId].section_id);
  form.find("[name='subgroup']").val(leaders[leaderId].subgroup);
  form.find("[name='role']").val(leaders[leaderId].role);
  form.find("[name='phone_member']").val(leaders[leaderId].phone_member);
  form.find("[name='phone_member_private']").prop("checked", leaders[leaderId].phone_member_private).trigger("change");
  form.find("[name='email_member']").val(leaders[leaderId].email_member);
  form.find("[name='totem']").val(leaders[leaderId].totem);
  form.find("[name='quali']").val(leaders[leaderId].quali);
  form.find("img.edit_listing_picture").attr("src", leaders[leaderId].picture_url);
  form.find("[name='family_in_other_units']").val(leaders[leaderId].family_in_other_units);
  form.find("[name='family_in_other_units_details']").val(leaders[leaderId].family_in_other_units_details);
  
  form.find("#current_leader_picture").attr("src", leaders[leaderId].picture_url);
  if (leaders[leaderId].has_picture) {
    form.find("#current_leader_picture").show();
  } else {
    form.find("#current_leader_picture").hide();
  }
}
