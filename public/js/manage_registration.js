$().ready(function() {
  $("#member_form input[name='is_leader']").change(function(obj) {
    console.log("toggle");
    if ($("#member_form input[name='is_leader']").prop("checked")) {
      $("#member_form .leader_specific").show();
    } else {
      $("#member_form .leader_specific").hide();
    }
  });
  if (!$("#member_form input[name='is_leader']").prop("checked")) {
    $("#member_form .leader_specific").hide();
  }
});

function dismissMemberForm() {
  $("#member_form").slideUp();
}

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
  $("#member_form [name='has_handicap']").prop("checked", registrations[memberId].has_handicap);
  $("#member_form [name='handicap_details']").val(registrations[memberId].handicap_details);
  $("#member_form [name='comments']").val(registrations[memberId].comments);
  $("#member_form [name='leader_name']").val(registrations[memberId].leader_name);
  $("#member_form [name='leader_in_charge']").prop("checked", registrations[memberId].leader_in_charge);
  $("#member_form [name='leader_description']").val(registrations[memberId].leader_description);
  $("#member_form [name='leader_role']").val(registrations[memberId].leader_role);
  $("#member_form [name='section']").val(registrations[memberId].section_id);
  $("#member_form [name='phone1']").val(registrations[memberId].phone1);
  $("#member_form [name='phone1_owner']").val(registrations[memberId].phone1_owner);
  $("#member_form [name='phone1_private']").prop("checked", registrations[memberId].phone1_private);
  $("#member_form [name='phone2']").val(registrations[memberId].phone2);
  $("#member_form [name='phone2_owner']").val(registrations[memberId].phone2_owner);
  $("#member_form [name='phone2_private']").prop("checked", registrations[memberId].phone2_private);
  $("#member_form [name='phone3']").val(registrations[memberId].phone3);
  $("#member_form [name='phone3_owner']").val(registrations[memberId].phone3_owner);
  $("#member_form [name='phone3_private']").prop("checked", registrations[memberId].phone3_private);
  $("#member_form [name='phone_member']").val(registrations[memberId].phone_member);
  $("#member_form [name='phone_member_private']").prop("checked", registrations[memberId].phone_member_private);
  $("#member_form [name='email1']").val(registrations[memberId].email1);
  $("#member_form [name='email2']").val(registrations[memberId].email2);
  $("#member_form [name='email3']").val(registrations[memberId].email3);
  $("#member_form [name='email_member']").val(registrations[memberId].email_member);
  $("#member_form [name='totem']").val(registrations[memberId].totem);
  $("#member_form [name='quali']").val(registrations[memberId].quali);
  $("#member_form [name='family_in_other_units']").val(registrations[memberId].family_in_other_units);
  $("#member_form [name='family_in_other_units_details']").val(registrations[memberId].family_in_other_units_details);
  $("#member_form [name='is_leader']").prop("checked", registrations[memberId].is_leader);
  if (registrations[memberId].is_leader) {
    $("#member_form .leader_specific").show();
  } else {
    $("#member_form .leader_specific").hide();
  }
  $("#member_form").slideDown();
}
