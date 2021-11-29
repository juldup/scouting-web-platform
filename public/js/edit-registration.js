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
  $("#member_form [name='leader_role_in_contact_page']").prop("checked", registrations[memberId].leader_role_in_contact_page).trigger("change");
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

// Registration e-mail selection filters
$().ready(function() {
  // Select all registrations
  $("#select-all-button").click(function() {
    $(".member-row .select-recipient-checkbox").each(function() {
      $(this).prop('checked', true);
    });
  });
  // Unselect all registrations
  $("#unselect-all-button").click(function() {
    $(".member-row .select-recipient-checkbox").each(function() {
      $(this).prop('checked', false);
    });
  });
  // Select all registrations in a category
  $(".select-all-category").click(function() {
    var category = $(this).data('category');
    $(".member-row").each(function() {
      if ($(this).data('category') == category) {
        $(this).find('.select-recipient-checkbox').prop('checked', true);
      }
    });
  });
  // Unselect all registrations in a category
  $(".unselect-all-category").click(function() {
    var category = $(this).data('category');
    $(".member-row").each(function() {
      if ($(this).data('category') == category) {
        $(this).find('.select-recipient-checkbox').prop('checked', false);
      }
    });
  });
  // Apply filters to a registration
  function selectOrUnselectAccordingToFilters() {
    var memberId = $(this).data('member-id');
    var member = registrations[memberId];
    var complies = compliesToFilters(member) == true ? true : false;
    if (complies) {
      $(this).find(".select-recipient-checkbox").prop('checked', true);
    } else {
      $(this).find(".select-recipient-checkbox").prop('checked', false);
    }
  }
  // Apply filters to all registrations
  $("#apply-filters-to-all").click(function() {
    $(".member-row").each(selectOrUnselectAccordingToFilters);
  });
  // Apply filters to all registrations of a category
  $(".apply-filters-to-category").click(function() {
    var category = $(this).data('category');
    $(".member-row").each(function() {
      if ($(this).data('category') == category) {
        $(this).each(selectOrUnselectAccordingToFilters);
      }
    });
  });
  // Enable or disable filters (initially and on change)
  function disableEnableStatusSubfilters() {
    if ($(this).is(":checked")) $(".filter-status-subfilter").attr('disabled', false);
    else $(".filter-status-subfilter").attr('disabled', true);
  };
  $("#filter-status").each(disableEnableStatusSubfilters);
  $("#filter-status").change(disableEnableStatusSubfilters);
  function disableEnableOrderSubfilters() {
    if ($(this).is(":checked")) $(".filter-order-subfilter").attr('disabled', false);
    else $(".filter-order-subfilter").attr('disabled', true);
  };
  $("#filter-order").each(disableEnableOrderSubfilters);
  $("#filter-order").change(disableEnableOrderSubfilters);
  // Submit recipient list to e-mail sending page
  $("#send-email-to-selected").click(function() {
    // Gather list of selected recipients
    var recipientList = [];
    $(".member-row").each(function() {
      if ($(this).find(".select-recipient-checkbox").is(":checked")) {
        var memberId = $(this).data('member-id');
        var email = registrations[memberId].email1;
        if (email != "") recipientList.push(email);
        email = registrations[memberId].email2;
        if (email != "") recipientList.push(email);
        email = registrations[memberId].email3;
        if (email != "") recipientList.push(email);
        if (registrations[memberId].is_leader) {
          email = registrations[memberId].email_member;
          if (email != "") recipientList.push(email);
        }
      }
    });
    $("#send-email-form #recipient-list-input").val(JSON.stringify(recipientList));
    $("#send-email-form").submit();
  });
});

function compliesToFilters(member) {
  if ($("#filter-status").prop('checked')) {
    if (member.registration_status == "Oui" && !$("#filter-status-oui").prop('checked')) {
      return false;
    }
    if (member.registration_status == "Non P" && !$("#filter-status-nonp").prop('checked')) {
      return false;
    }
    if (member.registration_status == "Non C" && !$("#filter-status-nonc").prop('checked')) {
      return false;
    }
    if (member.registration_status == "" && !$("#filter-status-nostatus").prop('checked')) {
      return false;
    }
  }
  if ($("#filter-order").prop('checked')) {
    var memberOrder = member.gender_order;
    var orderLimit = (member.gender == "M" ? $("#filter-order-m-limit") : $("#filter-order-f-limit")).val();
    var orderOperator = (member.gender == "M" ? $("#filter-order-m-operator") : $("#filter-order-f-operator")).val();
    if (orderOperator == "less" && memberOrder > orderLimit) {
      return false;
    }
    if (orderOperator == "more" && memberOrder < orderLimit) {
      return false;
    }
  }
  return true;
}
