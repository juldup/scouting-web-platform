function savePhotoOrder(table, photoOrder) {
  $.ajax({
    type: "POST",
    url: savePhotoOrderURL,
    data: { photo_order: photoOrder }
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
    } else {
      alert("Le nouvel ordre des photos n'a pas pu être sauvé");
      // Reload page
      window.location = window.location;
    }
  });
}
var saveDraggableOrder = savePhotoOrder;

function deletePhoto(photoButton) {
  if (!confirm("Veux-tu vraiment supprimer cette photo ?")) return false;
  var row = $(photoButton).closest('.photo-row');
  var photoId = row.data('draggable-id');
  $.ajax({
    type: "GET",
    url: deletePhotoURL,
    data: { photo_id: photoId }
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result == "Success") {
      row.remove();
    } else {
      alert("Une erreur est survenue. La photo n'a pas pu être supprimée.");
    }
  });
  
  return false;
}

$().ready(function() {
  $("#photo-drop-area").each(function() {
    $(this).attr('ondragover', 'draggingOverPhotoDropArea(event)');
    $(this).attr('onDragLeave', 'draggingOverPhotoDropAreaDone(event)');
    $(this).attr('ondrop', 'addPictures(event)');
    $(this).attr('onClick', 'selectPicturesManually()');
    // Disable pointer events on children to avoid interferences
    $(this).children().css('pointer-events', 'none');
  });
});

function draggingOverPhotoDropArea(event) {
  event.preventDefault();
  $("#photo-drop-area").addClass('drag-over');
}

function draggingOverPhotoDropAreaDone(event) {
  $("#photo-drop-area").removeClass('drag-over');
}

function selectPicturesManually() {
  $("#file-input").trigger("click");
}

function picturesManuallySelected() {
  var files = $("#file-input").prop("files");
  addPicturesFromList(files);
}

var newPictureCount = 0;
var picturesToUpload = new Array();
var uploadInProgress = false;
var currentUploadId = 0;
function addPictures(event) {
  event.preventDefault();
  draggingOverPhotoDropAreaDone(event);
  var dt = event.dataTransfer;
  var files = dt.files;
  addPicturesFromList(files);
}

function addPicturesFromList(files) {
  for (var i = 0; i < files.length; i++) {
    var file = files[i];
    if (file.type === "image/jpeg" || file.type === "image/png") {
      newPictureCount++;
      // Création d'une nouvelle ligne pour cette photo
      var rowId = "photo_row_new_" + newPictureCount;
      var prototype = $("#upload-row-prototype");
      var newRow = prototype.clone();
      prototype.before(newRow);
      newRow.show();
      newRow.attr('id', rowId);
      // Add picture to queue
      picturesToUpload.push({"file": file, "id": newPictureCount});
      // Try uploading next picture
      uploadNextPicture();
    }
  }
}

function uploadNextPicture() {
  if (uploadInProgress) return;
  if (picturesToUpload.length !== 0) {
    uploadInProgress = true;
    fileData = picturesToUpload.shift();
    currentUploadId = fileData.id;
    lastPercentage = 0;
    $("#photo_row_new_" + fileData.id + " .status").html("Envoi en cours...");
    data = new FormData();
    data.append('id', currentUploadId);
    data.append('file', fileData.file);
    data.append('album_id', currentAlbumId);
    $.ajax({
      url: uploadPhotoURL,
      type: "POST",
      data: data,
      cache: false,
      processData: false, // Don't process the files
      contentType: false // Set content type to false as jQuery will tell the server its a query string request
    }).done(function(json) {
      console.log("data: " + json);
      data = JSON.parse(json);
      if (data.result === "Success") {
        // Create new row for this photo
        var prototype = $("#photo-row-prototype");
        var newRow = prototype.clone();
        newRow.find('.photo-thumbnail img').attr('src', data.photo_thumbnail_url);
        newRow.addClass('draggable-row');
        newRow.attr('id', "photo-" + data.photo_id);
        newRow.data('draggable-id', data.photo_id);
        newRow.initDraggableRow();
        newRow.find('.editable-text').data('editable-id', data.photo_id);
        newRow.find('.editable-text').initEditableText();
        newRow.show();
        $("#photo_row_new_" + data.id).before(newRow);
      } else {
        // Display error message to user
        alert("Une erreur est survenue lors du transfert d'image");
      }
      // Remove temporary row
      $("#photo_row_new_" + data.id).remove();
      // Upload next picture (if any)
      uploadInProgress = false;
      uploadNextPicture();
    });
  }
}