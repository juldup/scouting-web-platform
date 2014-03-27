$().ready(function() {
  $("#member_form input[name='is_leader']").change(function(obj) {
    if ($("#member_form input[name='is_leader']").prop("checked")) {
      $("#member_form .leader_specific").show();
    } else {
      $("#member_form .leader_specific").hide();
    }
  });
  if (!$("#member_form input[name='is_leader']").prop("checked")) {
    $("#member_form .leader_specific").hide();
  }
  $("#member_form select[name='subgroup_select']").change(function() {
    $("#member_form input[name='subgroup']").val($(this).val());
  });
});

$().ready(function() {
  $(".warning-delete").click(function() {
    return confirm("Veux-tu vraiment supprimer ce membre du listing ?");
  });
});

function dismissMemberForm() {
  $("#member_form").slideUp();
}

function editMember(memberId) {
  $("#member_form [name='member_id']").val(memberId);
  $("#member_form [name='first_name']").val(members[memberId].first_name);
  $("#member_form [name='last_name']").val(members[memberId].last_name);
  $("#member_form [name='birth_date_day']").val(members[memberId].birth_date_day);
  $("#member_form [name='birth_date_month']").val(members[memberId].birth_date_month);
  $("#member_form [name='birth_date_year']").val(members[memberId].birth_date_year);
  $("#member_form [name='gender']").val(members[memberId].gender);
  $("#member_form [name='nationality']").val(members[memberId].nationality);
  $("#member_form [name='address']").val(members[memberId].address);
  $("#member_form [name='postcode']").val(members[memberId].postcode);
  $("#member_form [name='city']").val(members[memberId].city);
  $("#member_form [name='has_handicap']").prop("checked", members[memberId].has_handicap).trigger("change");
  $("#member_form [name='handicap_details']").val(members[memberId].handicap_details);
  $("#member_form [name='comments']").val(members[memberId].comments);
  $("#member_form [name='leader_name']").val(members[memberId].leader_name);
  $("#member_form [name='leader_in_charge']").prop("checked", members[memberId].leader_in_charge).trigger("change");
  $("#member_form [name='leader_description']").val(members[memberId].leader_description);
  $("#member_form [name='leader_role']").val(members[memberId].leader_role);
  $("#member_form [name='section']").val(members[memberId].section_id);
  $("#member_form [name='subgroup']").val(members[memberId].subgroup);
  $("#member_form [name='phone1']").val(members[memberId].phone1);
  $("#member_form [name='phone1_owner']").val(members[memberId].phone1_owner);
  $("#member_form [name='phone1_private']").prop("checked", members[memberId].phone1_private).trigger("change");
  $("#member_form [name='phone2']").val(members[memberId].phone2);
  $("#member_form [name='phone2_owner']").val(members[memberId].phone2_owner);
  $("#member_form [name='phone2_private']").prop("checked", members[memberId].phone2_private).trigger("change");
  $("#member_form [name='phone3']").val(members[memberId].phone3);
  $("#member_form [name='phone3_owner']").val(members[memberId].phone3_owner);
  $("#member_form [name='phone3_private']").prop("checked", members[memberId].phone3_private).trigger("change");
  $("#member_form [name='phone_member']").val(members[memberId].phone_member);
  $("#member_form [name='phone_member_private']").prop("checked", members[memberId].phone_member_private).trigger("change");
  $("#member_form [name='email1']").val(members[memberId].email1);
  $("#member_form [name='email2']").val(members[memberId].email2);
  $("#member_form [name='email3']").val(members[memberId].email3);
  $("#member_form [name='email_member']").val(members[memberId].email_member);
  $("#member_form [name='totem']").val(members[memberId].totem);
  $("#member_form [name='quali']").val(members[memberId].quali);
  $("#member_form [name='family_in_other_units']").val(members[memberId].family_in_other_units);
  $("#member_form [name='family_in_other_units_details']").val(members[memberId].family_in_other_units_details);
  $("#member_form [name='is_leader']").prop("checked", members[memberId].is_leader).trigger("change");
  if (members[memberId].is_leader) {
    $("#member_form .leader_specific").show();
  } else {
    $("#member_form .leader_specific").hide();
  }
  $("#member_form").slideDown();
  // Focus on first field
  document.getElementById("first_name").focus();
}

function showMemberDetails(memberId) {
  var element = $("#details_" + memberId);
  var visible = element.is(":visible");
  if (visible) {
    element.hide();
  } else {
    $(".details_member:visible").hide();
    element.show();
  }
}