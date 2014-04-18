// Page edition

// Insert image at current location
function editPageInsertImage(imageURL) {
  var element = CKEDITOR.dom.element.createFromHtml("<img style='max-width: 80%' src='" + imageURL + "'/>");
  CKEDITOR.instances['page_body'].insertElement(element);
}

// Uploads an image and adds it to the list
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
          " <a href='' onclick='return removeImage(" + data.image_id + ")'><span class='glyphicon glyphicon-remove'></span></a>" +
          " <span class='horiz-divider'></span>");
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
  }).fail(function(jqXHR, textStatus) {
  });
  return false;
}
