$().ready(function() {
  $(".edit-button").click(function(event) {
    event.preventDefault();
    editSection($(this).closest("[data-section-id]").data('section-id'));
  });
  $(".details-button").click(function(event) {
    event.preventDefault();
    showSectionDetails($(this).closest("[data-section-id]").data('section-id'));
  });
  $(".dismiss-form").click(function(event) {
    $(this).closest("#section_form").slideUp();
  });
  $("#section_form #delete_button").click(function(event) {
    event.preventDefault();
    if (confirm("Supprimer définitivement cette section ?") && confirm("Attention ! Cette opération ne pourra pas être annulée")) {
      var sectionId = $("#section_form [name='section_id']").val();
      window.location = sections[sectionId].delete_url;
    }
  });
  $("#section_form .color-sample").click(function(event) {
    var thisColorSample = $(this);
    thisColorSample.colorpicker({
      component: thisColorSample,
      color: $("#section_form [name='section_color']").val()
    }).on('changeColor', function(event) {
      thisColorSample.css('background-color', event.color.toHex());
      $("#section_form [name='section_color']").val(event.color.toHex());
    }).on('hidePicker', function(event) {
      console.log('disable');
      thisColorSample.colorpicker('destroy');
    });
    thisColorSample.colorpicker('show');
  });
  // Initialize color selector background with current color in form
  $("#section_form .color-sample").css('background-color', $("#section_form [name='section_color']").val())
  // Collapse details on section drag
  $(".draggable-row").on('dragstart', function() {
    // Set timeout to process this after the drag initialization, otherwise the drag might get canceled
    setTimeout(function() {
      $(".details_section:visible").hide();
    }, 0);
  });
});

function editSection(sectionId) {
  $("#section_form [name='section_id']").val(sectionId);
  $("#section_form [name='section_name']").val(sections[sectionId].name);
  $("#section_form [name='section_email']").val(sections[sectionId].email);
  $("#section_form [name='section_type']").val(sections[sectionId].type);
  $("#section_form [name='section_type_number']").val(sections[sectionId].type_number);
  $("#section_form [name='section_color']").val(sections[sectionId].color);
  $("#section_form .color-sample").css('background-color', sections[sectionId].color);
  $("#section_form [name='section_la_section']").val(sections[sectionId].la_section);
  $("#section_form [name='section_de_la_section']").val(sections[sectionId].de_la_section);
  $("#section_form [name='section_subgroup_name']").val(sections[sectionId].subgroup_name);
  $("#section_form #delete_button").show();
  $("#section_form #delete_button").attr('href', sections[sectionId].delete_url);
  $("#section_form").slideDown();
}

function showSectionDetails(sectionId) {
  var element = $(".details_section[data-section-id='" + sectionId + "']");
  var visible = element.is(":visible");
  if (visible) {
    element.hide();
  } else {
    $(".details_section:visible").hide();
    element.show();
  }
}

function saveDraggableOrder(table, sectionOrder) {
  $.ajax({
    type: "POST",
    url: saveSectionOrderURL,
    data: { section_order: sectionOrder }
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
      console.log("Order saved");
    } else {
      alert("Le nouvel ordre des sections n'a pas pu être sauvé.");
      // Reload page
      window.location = window.location;
    }
  });
}
