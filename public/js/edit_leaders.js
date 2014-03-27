$().ready(function() {
  $("#scout_to_leader select").bind('change', function() {
    $("#scout_to_leader form").trigger('submit');
  });
  $(".warning-delete").click(function() {
    return confirm("Veux-tu vraiment supprimer ce membre du listing ?");
  });
});

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
  $("#member_form [name='phone_member']").val("");
  $("#member_form [name='phone_member_private']").prop("checked", false).trigger("change");
  $("#member_form [name='email_member']").val("");
  $("#member_form [name='totem']").val("");
  $("#member_form [name='quali']").val("");
  $("#member_form [name='family_in_other_units']").val(0);
  $("#member_form [name='family_in_other_units_details']").val("");
  $("#member_form #current_leader_picture").attr("src", "");
  $("#member_form #current_leader_picture").hide();
  $("#member_form").slideDown();
}

function dismissMemberForm() {
  $("#member_form").slideUp();
}

function editLeader(leaderId, scoutToLeader) {
  if (scoutToLeader)
    $("#member_form legend:first").html("Transformer un scout en animateur");
  else
    $("#member_form legend:first").html("Modifier un animateur");
  $("#member_form [name='member_id']").val(leaderId);
  $("#member_form [name='first_name']").val(leaders[leaderId].first_name);
  $("#member_form [name='last_name']").val(leaders[leaderId].last_name);
  $("#member_form [name='birth_date_day']").val(leaders[leaderId].birth_date_day);
  $("#member_form [name='birth_date_month']").val(leaders[leaderId].birth_date_month);
  $("#member_form [name='birth_date_year']").val(leaders[leaderId].birth_date_year);
  $("#member_form [name='gender']").val(leaders[leaderId].gender);
  $("#member_form [name='nationality']").val(leaders[leaderId].nationality);
  $("#member_form [name='address']").val(leaders[leaderId].address);
  $("#member_form [name='postcode']").val(leaders[leaderId].postcode);
  $("#member_form [name='city']").val(leaders[leaderId].city);
  $("#member_form [name='has_handicap']").prop("checked", leaders[leaderId].has_handicap).trigger("change");
  $("#member_form [name='handicap_details']").val(leaders[leaderId].handicap_details);
  $("#member_form [name='comments']").val(leaders[leaderId].comments);
  $("#member_form [name='leader_name']").val(leaders[leaderId].leader_name);
  $("#member_form [name='leader_in_charge']").prop("checked", leaders[leaderId].leader_in_charge).trigger("change");
  $("#member_form [name='leader_description']").val(leaders[leaderId].leader_description);
  $("#member_form [name='leader_role']").val(leaders[leaderId].leader_role);
  $("#member_form [name='section']").val(leaders[leaderId].section_id);
  $("#member_form [name='phone_member']").val(leaders[leaderId].phone_member);
  $("#member_form [name='phone_member_private']").prop("checked", leaders[leaderId].phone_member_private).trigger("change");
  $("#member_form [name='email_member']").val(leaders[leaderId].email_member);
  $("#member_form [name='totem']").val(leaders[leaderId].totem);
  $("#member_form [name='quali']").val(leaders[leaderId].quali);
  $("#member_form [name='family_in_other_units']").val(leaders[leaderId].family_in_other_units);
  $("#member_form [name='family_in_other_units_details']").val(leaders[leaderId].family_in_other_units_details);
  
  $("#member_form #current_leader_picture").attr("src", leaders[leaderId].picture_url);
  if (leaders[leaderId].has_picture) {
    $("#member_form #current_leader_picture").show();
  } else {
    $("#member_form #current_leader_picture").hide();
  }
  
  $("#member_form").slideDown();
}
