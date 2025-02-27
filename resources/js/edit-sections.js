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
 * This script is present on the section management page
 */

$().ready(function() {
  // Edit button
  $(".edit-button").click(function(event) {
    event.preventDefault();
    editSection($(this).closest("[data-section-id]").data('section-id'));
  });
  $(".edit-limited-button").click(function(event) {
    event.preventDefault();
    editSectionLimited($(this).closest("[data-section-id]").data('section-id'));
  });
  // Details button
  $(".details-button").click(function(event) {
    event.preventDefault();
    showSectionDetails($(this).closest("[data-section-id]").data('section-id'));
  });
  // Create section button
  $(".add-button").click(function(event) {
    event.preventDefault();
    createSection();
  });
  // Dismiss form button
  $(".dismiss-form").click(function(event) {
    $(this).closest("#section_form").slideUp();
    $(this).closest("#section-form-limited").slideUp();
  });
  // Delete button
  $("#section_form #delete_button").click(function(event) {
    event.preventDefault();
    if (confirm("Supprimer définitivement cette section ?") && confirm("Attention ! Cette opération ne pourra pas être annulée")) {
      var sectionId = $("#section_form [name='section_id']").val();
      window.location = sections[sectionId].delete_url;
    }
  });
  // Color button
  $("#section_form .color-sample").click(function(event) {
    var thisColorSample = $(this);
    thisColorSample.colorpicker({
      component: thisColorSample,
      color: $("#section_form [name='section_color']").val()
    }).on('changeColor', function(event) {
      thisColorSample.css('background-color', event.color.toHex());
      $("#section_form [name='section_color']").val(event.color.toHex());
    }).on('hidePicker', function(event) {
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

/**
 * Empties and shows the section form to add a new section
 */
function createSection(sectionId) {
  var defaultColor = "#FF8800";
  $("#section_form legend").text("Créer une nouvelle section");
  $("#section_form [name='section_id']").val(0);
  $("#section_form [name='section_name']").val("");
  $("#section_form [name='section_email']").val("");
  $("#section_form [name='section_category']").val("");
  $("#section_form [name='section_type']").val("");
  $("#section_form [name='section_type_number']").val("");
  $("#section_form [name='section_color']").val(defaultColor);
  $("#section_form .color-sample").css('background-color', defaultColor);
  $("#section_form [name='section_calendar_shortname']").val("");
  $("#section_form [name='section_la_section']").val("");
  $("#section_form [name='section_de_la_section']").val("");
  $("#section_form [name='section_subgroup_name']").val("");
  $("#section_form [name='section_start_age']").val("");
  $("#section_form [name='google_calendar_link']").val("");
  $("#section_form #icalendar_link").html("cette url sera disponible lorsque la section aura été créée");
  $("#section_form #delete_button").hide();
  $("#section_form #delete_button").attr('href', "");
  $("#section_form").slideDown();
}

/**
 * Sets the section form to match an existing section and shows it
 */
function editSection(sectionId) {
  $("#section_form legend").text("Modifier la section");
  $("#section_form [name='section_id']").val(sectionId);
  $("#section_form [name='section_name']").val(sections[sectionId].name);
  $("#section_form [name='section_email']").val(sections[sectionId].email);
  $("#section_form [name='section_category']").val(sections[sectionId].category);
  $("#section_form [name='section_type']").val(sections[sectionId].type);
  $("#section_form [name='section_type_number']").val(sections[sectionId].type_number);
  $("#section_form [name='section_color']").val(sections[sectionId].color);
  $("#section_form .color-sample").css('background-color', sections[sectionId].color);
  $("#section_form [name='section_calendar_shortname']").val(sections[sectionId].calendar_shortname);
  $("#section_form [name='section_la_section']").val(sections[sectionId].la_section);
  $("#section_form [name='section_de_la_section']").val(sections[sectionId].de_la_section);
  $("#section_form [name='section_subgroup_name']").val(sections[sectionId].subgroup_name);
  $("#section_form [name='section_start_age']").val(sections[sectionId].start_age);
  $("#section_form [name='google_calendar_link']").val(sections[sectionId].google_calendar_link);
  $("#section_form #icalendar_link").html(sections[sectionId].export_calendar_url);
  $("#section_form #delete_button").show();
  $("#section_form #delete_button").attr('href', sections[sectionId].delete_url);
  $("#section_form").slideDown();
  $("#section-form-limited").slideUp();
}

/**
 * Sets the limited section form to match an existing section and shows it
 */
function editSectionLimited(sectionId) {
  $("#section-form-limited legend").text("Modifier la section " + sections[sectionId].name);
  $("#section-form-limited [name='section_id']").val(sectionId);
  $("#section-form-limited [name='section_email']").val(sections[sectionId].email);
  $("#section-form-limited [name='section_subgroup_name']").val(sections[sectionId].subgroup_name);
  $("#section-form-limited").slideDown();
  $("#section_form").slideUp();
}

/**
 * Shows the details of the given section
 */
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

/**
 * Save the new order of the sections (see reorder-list.js)
 */
function saveDraggableOrder(table, sectionOrder) {
  $.ajax({
    type: "POST",
    url: saveSectionOrderURL,
    data: { section_order: sectionOrder }
  }).done(function(json) {
    var data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
    } else {
      alert("Le nouvel ordre des sections n'a pas pu être sauvé.");
      // Reload page
      window.location = window.location;
    }
  });
}
