$().ready(function() {
  $(".editable-text").each(function() {
    // Add edit icon
    $(this).append("<span class='glyphicon glyphicon-edit'></span>");
    $(this).on('click', function() {
      $(this).changeToEditMode();
    });
  });
});

$.fn.changeToEditMode = function() {
  if ($(this).data('editing') !== true) {
    $(this).data('editing', true);
    $(this).find('.editable-text-value').hide();
    $(this).find('.glyphicon-edit').hide();
    $(this).append('<input type="text" class="editable-text-input">');
    var textInput = $(this).find('input');
    textInput.val($(this).find('.editable-text-value').text().trim());
    textInput.on('keyup', function(event) {
      if (event.which === 13 || event.keyCode === 13) {
        $(event.target).closest(".editable-text").submitEditableText();
      }
    });
    $(this).append(' <button class="btn btn-primary">OK</button>');
    $(this).find('button').on('click', function(event) {
      event.stopPropagation();
      $(this).closest(".editable-text").submitEditableText();
    });
  }
}

$.fn.changeToNormalMode = function() {
  $(this).data('editing', false);
  $(this).find('button').remove();
  $(this).find('input').remove();
  $(this).find('.editable-text-value').show();
  $(this).find('.glyphicon-edit').show();
}

$.fn.submitEditableText = function() {
  var newValue = $(this).find('input').val().trim();
  if (newValue === "") {
    $(this).changeToNormalMode();
  } else {
    var editableText = $(this);
    $.ajax({
      url: $(this).data('editable-submit-url'),
      type: "POST",
      data: {id: $(this).data('editable-id'), value: newValue}
    }).done(function(json) {
      editableText.changeToNormalMode();
      console.log("JSON: " + json);
      var data = JSON.parse(json);
      if (data.result === "Success") {
        editableText.find('.editable-text-value').text(newValue);
      } else {
        alert("Une erreur est survenue. La nouvelle valeur n'a pas été enregistrée.");
      }
    });
  }
}