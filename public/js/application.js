
// Login

// Make sure there is a login and password
function checkLogin() {
  if (document.login.login_username.value === '') {
    document.login.login_username.focus();
    document.login.login_username.select();
    return false;
  }
  if (document.login.login_password.value === '') {
    document.login.login_password.focus();
    document.login.login_password.select();
    return false;
  }
  return true;
}
// Connecte l'utilisateur s'il a entré un pseudo et un mot de passe
function submitLogin() {
  if (checkLogin()) document.login.submit();
}
// Valide la connexion si 'enter' est pressé
function checkEnter(e) {
  if (e.which === 13 || e.keyCode === 13) submitLogin();
}

// Page edition

// Insert image at current location
function editPageInsertImage(imageURL) {
  var textarea = $("#inputPane");
  var textToInsert = "![](" + imageURL + ")";
  var position = textarea.getCursorPosition();
  $("#inputPane").val(textarea.val().substring(0, position) + textToInsert + textarea.val().substring(position));
  convertText();
}

// Returns the cursor position inside a field
(function ($, undefined) {
    $.fn.getCursorPosition = function() {
        var el = $(this).get(0);
        var pos = 0;
        if ('selectionStart' in el) {
            pos = el.selectionStart;
        } else if('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
})(jQuery);

// Uploads an image and adds to the list
$().ready(function() {
  if (typeof image_upload_url != 'undefined') {
    var element = document.getElementById('uploader');
    upclick(
      {
      element:element, 
      action: image_upload_url, 
      onstart: 
        function(filename) {},
      oncomplete:
        function(json) {
          data = JSON.parse(json);
          if (data.result == "OK") {
            addImageToList(data);
          } else {
            alert(data.message);
          }
        }
      }
    );
  }
});

function addImageToList(data) {
  $("#edit_page_form #image_list").append(
          "<span id='image_" + data.image_id + "'>" +
          " <a href=\"javascript:editPageInsertImage('" + data.url + "');\">" +
          "<img src='" + data.url + "' class='image_preview_edit_page'/></a>" +
          " <input type='button' onclick='removeImage(" + data.image_id + ")' value='-' />");
}

$().ready(function() {
  if (typeof initial_images !== "undefined") {
    initial_images.forEach(function(image_data) {
      addImageToList(image_data);
    });
  }
});

function removeImage(image_id) {
  var url = image_remove_url.replace("image_id", image_id);
  $.ajax(url).done(function(json) {
    data = JSON.parse(json);
    if (data.result == "OK") {
      $("#image_" + data.image_id).remove();
    }
    convertText(true);
  }).fail(function(jqXHR, textStatus) {
    console.log(textStatus);
  });
}