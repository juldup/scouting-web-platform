/**
 * This script offers a generic tool to have editable small texts on the page.
 * An editable text must appear in a tag like the following:
 * <span class="editable-text" data-editable-submit-url="%URL%" data-editable-id="%ID">
 *   <span class="editable-text-value">
 *     %INITIAL VALUE%
 *   </span>
 * </span>
 * 
 * An edition icon will be appended to the text. When the text or icon is clicked, an
 * input field and a save button appear. When saving the text, if it has changed, a POST ajax
 * request is sent to the submit url with this data: {id:%ID%, value:%NEW VALUE%}.
 */

$().ready(function() {
  // Initialize all editable texts in the page
  $(".editable-text:visible").initEditableText();
});

/**
 * Adds an edit icon and makes the text clickable for editing
 */
$.fn.initEditableText = function() {
  // Add edit icon
  $(this).append("<span class='glyphicon glyphicon-edit editable-edit-icon'></span>");
  // Add click action
  $(this).on('click', function() {
    $(this).changeEditableTextToEditMode();
  });
};

/**
 * Changes editable text to edit mode
 */
$.fn.changeEditableTextToEditMode = function() {
  if ($(this).data('editing') !== true) {
    // Dismiss all other texts 
    $('.editable-text').changeEditableTextToNormalMode();
    // Change editing status
    $(this).data('editing', true);
    // Hide text and edit icon
    $(this).find('.editable-text-value').hide();
    $(this).find('.editable-edit-icon').hide();
    // Create input text
    if ($(this).data('editable-input-type') === 'textarea') {
      $(this).append('<textarea class="editable-text-input" rows="3"></textarea>');
    } else {
      $(this).append('<input type="text" class="editable-text-input">');
    }
    var textInput = $(this).find('.editable-text-input');
    // Initialize input text value
    textInput.val($(this).find('.editable-text-value').text().trim());
    // Link keys to actions
    textInput.filter('input').on('keyup', function(event) {
      // On 'enter' key, submit text
      if (event.which === 13 || event.keyCode === 13) {
        $(event.target).closest(".editable-text").submitEditableText();
      }
    });
    textInput.on('keyup', function(event) {
      // On 'escape' key, cancel
      if (event.which === 27 || event.keyCode === 27) {
        $(event.target).closest(".editable-text").changeEditableTextToNormalMode();
      }
    });
    // Give focus to text input
    textInput.focus();
    textInput.select();
    // Add submit button
    $(this).append(' <button class="btn-sm btn-primary editable-submit-button">OK</button>');
    $(this).find('button.editable-submit-button').on('click', function(event) {
      event.stopPropagation();
      $(this).closest(".editable-text").submitEditableText();
    });
    // Add cancel button
    $(this).append(' <button class="btn-sm btn-default editable-cancel-button">Annuler</button>');
    $(this).find('button.editable-cancel-button').on('click', function(event) {
      event.stopPropagation();
      $(this).closest(".editable-text").changeEditableTextToNormalMode();
    });
  }
};

/**
 * Changes editable text back from edit mode
 */
$.fn.changeEditableTextToNormalMode = function() {
  // Change editing status back
  $(this).data('editing', false);
  // Remove form elements
  $(this).find('button.editable-submit-button').remove();
  $(this).find('button.editable-cancel-button').remove();
  $(this).find('.editable-text-input').remove();
  // Show normal elements
  $(this).find('.editable-text-value').show();
  $(this).find('.editable-edit-icon').show();
};

/**
 * Submits the value of an editable text
 */
$.fn.submitEditableText = function() {
  var newValue = $(this).find('.editable-text-input').val().trim();
  if ((newValue === "" && !$(this).data('editable-allow-empty'))
          || newValue === $(this).find('.editable-text-value').text().trim()) {
    // No change to submit, just cancel
    $(this).changeEditableTextToNormalMode();
  } else {
    // Remember editable text for after ajax query
    var editableText = $(this);
    // Submit using ajax
    $.ajax({
      url: $(this).data('editable-submit-url'),
      type: "POST",
      data: {id: $(this).data('editable-id'), value: newValue}
    }).done(function(json) {
      // Revert to normal mode
      editableText.changeEditableTextToNormalMode();
      var data = JSON.parse(json);
      if (data.result === "Success") {
        // Update text value
        editableText.find('.editable-text-value').text(newValue);
      } else {
        // Display error message
        alert("Une erreur est survenue. La nouvelle valeur n'a pas été enregistrée.");
      }
    });
  }
};
