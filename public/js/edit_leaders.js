function addLeader(sectionId) {
  $("#leader_form [name='member_id']").val("");
  $("#leader_form [name='first_name']").val("");
  $("#leader_form [name='last_name']").val("");
  $("#leader_form [name='birth_date']").val("");
  $("#leader_form [name='gender']").val("M");
  $("#leader_form [name='nationality']").val("");
  $("#leader_form [name='address']").val("");
  $("#leader_form [name='postcode']").val("");
  $("#leader_form [name='city']").val("");
  $("#leader_form [name='has_handicap']").prop("checked", false);
  $("#leader_form [name='handicap_details']").val("");
  $("#leader_form [name='comments']").val("");
  $("#leader_form [name='leader_name']").val("");
  $("#leader_form [name='leader_in_charge']").prop("checked", false);
  $("#leader_form [name='leader_description']").val("");
  $("#leader_form [name='leader_role']").val("");
  $("#leader_form [name='section']").val(sectionId);
  $("#leader_form [name='phone_member']").val("");
  $("#leader_form [name='phone_member_private']").prop("checked", false);
  $("#leader_form [name='email_member']").val("");
  $("#leader_form [name='totem']").val("");
  $("#leader_form [name='quali']").val("");
  $("#leader_form [name='family_in_other_units']").val(0);
  $("#leader_form [name='family_in_other_units_details']").val("");
  $("#leader_form #current_leader_picture").attr("src", "");
  $("#leader_form #current_leader_picture").hide();
  $("#leader_form").slideDown();
}

function dismissLeaderForm() {
  $("#leader_form").slideUp();
}

function editLeader(leaderId) {
  $("#leader_form [name='member_id']").val(leaderId);
  $("#leader_form [name='first_name']").val(leaders[leaderId].first_name);
  $("#leader_form [name='last_name']").val(leaders[leaderId].last_name);
  $("#leader_form [name='birth_date_day']").val(leaders[leaderId].birth_date_day);
  $("#leader_form [name='birth_date_month']").val(leaders[leaderId].birth_date_month);
  $("#leader_form [name='birth_date_year']").val(leaders[leaderId].birth_date_year);
  $("#leader_form [name='gender']").val(leaders[leaderId].gender);
  $("#leader_form [name='nationality']").val(leaders[leaderId].nationality);
  $("#leader_form [name='address']").val(leaders[leaderId].address);
  $("#leader_form [name='postcode']").val(leaders[leaderId].postcode);
  $("#leader_form [name='city']").val(leaders[leaderId].city);
  $("#leader_form [name='has_handicap']").prop("checked", leaders[leaderId].has_handicap);
  $("#leader_form [name='handicap_details']").val(leaders[leaderId].handicap_details);
  $("#leader_form [name='comments']").val(leaders[leaderId].comments);
  $("#leader_form [name='leader_name']").val(leaders[leaderId].leader_name);
  $("#leader_form [name='leader_in_charge']").prop("checked", leaders[leaderId].leader_in_charge);
  $("#leader_form [name='leader_description']").val(leaders[leaderId].leader_description);
  $("#leader_form [name='leader_role']").val(leaders[leaderId].leader_role);
  $("#leader_form [name='section']").val(leaders[leaderId].section_id);
  $("#leader_form [name='phone_member']").val(leaders[leaderId].phone_member);
  $("#leader_form [name='phone_member_private']").prop("checked", leaders[leaderId].phone_member_private);
  $("#leader_form [name='email_member']").val(leaders[leaderId].email);
  $("#leader_form [name='totem']").val(leaders[leaderId].totem);
  $("#leader_form [name='quali']").val(leaders[leaderId].quali);
  $("#leader_form [name='family_in_other_units']").val(leaders[leaderId].family_in_other_units);
  $("#leader_form [name='family_in_other_units_details']").val(leaders[leaderId].family_in_other_units_details);
  
  $("#leader_form #current_leader_picture").attr("src", leaders[leaderId].picture_url);
  if (leaders[leaderId].has_picture) {
    $("#leader_form #current_leader_picture").show();
  } else {
    $("#leader_form #current_leader_picture").hide();
  }
  
  $("#leader_form").slideDown();
}
