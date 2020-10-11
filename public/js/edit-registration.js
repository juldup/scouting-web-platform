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
 * This script is present on the registration management page
 */

$().ready(function() {
  // Show/hide leader specific fields when the is_leader switch is toggled
  $("#member_form input[name='is_leader']").change(function(obj) {
    if ($("#member_form input[name='is_leader']").prop("checked")) {
      $("#member_form .leader_specific").show();
    } else {
      $("#member_form .leader_specific").hide();
    }
  });
  // Initially hide the leader specific fields if needed
  if (!$("#member_form input[name='is_leader']").prop("checked")) {
    $("#member_form .leader_specific").hide();
  }
  // Add to waiting list button
  $(".toggle-waiting-list-button").on('click', function() {
    // Get row, its member and waiting list status
    var row = $(this).closest('.member-row');
    var memberId = row.data('member-id');
    var inWaitingList = $(this).data('in-waiting-list');
    // Save change
    $.ajax({
      url: toggleWaitingListURL,
      data: {member_id: memberId, in_waiting_list: inWaitingList}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        // Update text once it has been saved
        if (inWaitingList) {
          row.find('.is-in-waiting-list').show();
          row.find('.is-not-in-waiting-list').hide();
        } else {
          row.find('.is-in-waiting-list').hide();
          row.find('.is-not-in-waiting-list').show();
        }
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
});

/**
 * Hide the member form
 */
function dismissMemberForm() {
  $("#member_form").slideUp();
}

/**
 * Sets the member form to match a member and shows it
 */
function editRegistration(memberId) {
  $("#member_form [name='member_id']").val(memberId);
  $("#member_form [name='first_name']").val(registrations[memberId].first_name);
  $("#member_form [name='last_name']").val(registrations[memberId].last_name);
  $("#member_form [name='birth_date_day']").val(registrations[memberId].birth_date_day);
  $("#member_form [name='birth_date_month']").val(registrations[memberId].birth_date_month);
  $("#member_form [name='birth_date_year']").val(registrations[memberId].birth_date_year);
  $("#member_form [name='gender']").val(registrations[memberId].gender);
  $("#member_form [name='nationality']").val(registrations[memberId].nationality);
  $("#member_form [name='address']").val(registrations[memberId].address);
  $("#member_form [name='postcode']").val(registrations[memberId].postcode);
  $("#member_form [name='city']").val(registrations[memberId].city);
  $("#member_form [name='has_handicap']").prop("checked", registrations[memberId].has_handicap).trigger("change");
  $("#member_form [name='handicap_details']").val(registrations[memberId].handicap_details);
  $("#member_form [name='comments']").val(registrations[memberId].comments);
  $("#member_form [name='leader_name']").val(registrations[memberId].leader_name);
  $("#member_form [name='leader_in_charge']").prop("checked", registrations[memberId].leader_in_charge).trigger("change");
  $("#member_form [name='leader_description']").val(registrations[memberId].leader_description);
  $("#member_form [name='leader_role']").val(registrations[memberId].leader_role);
  $("#member_form [name='section']").val(registrations[memberId].section_id);
  $("#member_form [name='phone1']").val(registrations[memberId].phone1);
  $("#member_form [name='phone1_owner']").val(registrations[memberId].phone1_owner);
  $("#member_form [name='phone1_private']").prop("checked", registrations[memberId].phone1_private).trigger("change");
  $("#member_form [name='phone2']").val(registrations[memberId].phone2);
  $("#member_form [name='phone2_owner']").val(registrations[memberId].phone2_owner);
  $("#member_form [name='phone2_private']").prop("checked", registrations[memberId].phone2_private).trigger("change");
  $("#member_form [name='phone3']").val(registrations[memberId].phone3);
  $("#member_form [name='phone3_owner']").val(registrations[memberId].phone3_owner);
  $("#member_form [name='phone3_private']").prop("checked", registrations[memberId].phone3_private).trigger("change");
  $("#member_form [name='phone_member']").val(registrations[memberId].phone_member);
  $("#member_form [name='phone_member_private']").prop("checked", registrations[memberId].phone_member_private).trigger("change");
  $("#member_form [name='email1']").val(registrations[memberId].email1);
  $("#member_form [name='email2']").val(registrations[memberId].email2);
  $("#member_form [name='email3']").val(registrations[memberId].email3);
  $("#member_form [name='email_member']").val(registrations[memberId].email_member);
  $("#member_form [name='totem']").val(registrations[memberId].totem);
  $("#member_form [name='quali']").val(registrations[memberId].quali);
  $("#member_form [name='family_in_other_units']").val(registrations[memberId].family_in_other_units);
  $("#member_form [name='family_in_other_units_details']").val(registrations[memberId].family_in_other_units_details);
  $("#member_form [name='is_leader']").prop("checked", registrations[memberId].is_leader).trigger("change");
  if (registrations[memberId].is_leader) {
    $("#member_form .leader_specific").show();
  } else {
    $("#member_form .leader_specific").hide();
  }
  $("#member_form").slideDown();
}

// Show edit registration priority for a member
function editRegistrationPriority(memberId) {
  var element = $("#advanced-registration-edit-" + memberId);
  $(".advanced-registration-edit:visible").hide();
  element.show();
  $(document).focus();
};

// Close registration priority edit panel
$().ready(function() {
  $(".close-button").click(function() {
    $(this).closest(".advanced-registration-edit").hide();
  });
  $(".advanced-registration-edit").click(function() {
    $(this).hide();
  });
  $('.advanced-registration-edit-panel').click(function(event) {
    event.stopPropagation();
  });
  $(document).on('keydown', function(event) {
    if(event.key == "Escape") {
      $(".advanced-registration-edit").hide();
    }
  });
});
