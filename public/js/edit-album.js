/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
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
 * This script is present on the album management page and provides
 * functionalities to add, modify and delete photos from the current
 * album
 */

/**
 * Uploads the photo order after photos have been reordered (see reorder-list.js)
 */
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

// Set order upload function (see reorder-list.js)
var saveDraggableOrder = savePhotoOrder;

/**
 * Removes a photo from the list and saves the change
 */
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

// Initialize drag zone to add new photos
$().ready(function() {
  $("#photo-drop-area").each(function() {
    $(this).attr('ondragover', 'draggingOverPhotoDropArea(event)');
    $(this).attr('onDragLeave', 'draggingOverPhotoDropAreaDone(event)');
    $(this).attr('ondrop', 'addPictures(event)');
    $(this).attr('onClick', 'selectPicturesManually()');
    // Disable pointer events on children to avoid interferences
    $(this).children().css('pointer-events', 'none');
  });
  initRotateButtons($("body"));
});

/**
 * Called when the photo drop area is being dragged over
 */
function draggingOverPhotoDropArea(event) {
  event.preventDefault();
  $("#photo-drop-area").addClass('drag-over');
}

/**
 * Called when the drag leaves the the photo drop area
 */
function draggingOverPhotoDropAreaDone(event) {
  $("#photo-drop-area").removeClass('drag-over');
}

/**
 * Called when the photo drop area is being clicked, presents a
 * file chooser to choose pictures
 */
function selectPicturesManually() {
  $("#file-input").trigger("click");
}

/**
 * Called when photos have been added manually
 */
function picturesManuallySelected() {
  var files = $("#file-input").prop("files");
  if (!files) {
    alert("Désolés. Ton navigateur ne permet pas cette opération.\nEssaie d'utiliser Google Chrome ou Firefox.");
    return;
  }
  addPicturesFromList(files);
}

// Picture counter to create unique ids
var newPictureCount = 0;

// List of pictures waiting to be uploaded
var picturesToUpload = new Array();

// Whether a picture is currently being uploaded
var uploadInProgress = false;

// Id of the photo currently being uploaded
var currentUploadId = 0;

/**
 * Called when files are being dragged onto the photo drag area.
 * Adds the photos to the pending upload list.
 */
function addPictures(event) {
  event.preventDefault();
  draggingOverPhotoDropAreaDone(event);
  var dt = event.dataTransfer;
  var files = dt.files;
  addPicturesFromList(files);
}

/**
 * Adds all photo from the file list to the photo list
 * and to the pending upload list
 */
function addPicturesFromList(files) {
  for (var i = 0; i < files.length; i++) {
    var file = files[i];
    // Make sure the photo is an jpeg or png image
    if (file.type === "image/jpeg" || file.type === "image/png") {
      // Generate new id
      newPictureCount++;
      // Create a new row for the photo
      var rowId = "photo_row_new_" + newPictureCount;
      var prototype = $("#upload-row-prototype");
      var newRow = prototype.clone();
      prototype.before(newRow);
      newRow.show();
      newRow.attr('id', rowId);
      // Add picture to upload queue
      picturesToUpload.push({"file": file, "id": newPictureCount});
      // Try uploading next picture
      uploadNextPicture();
    }
  }
}

/**
 * Tries to upload the next photo from the pending list
 */
function uploadNextPicture() {
  // Return if another upload is already happening
  if (uploadInProgress) return;
  if (picturesToUpload.length !== 0) {
    // Flag that there is an upload in progress
    uploadInProgress = true;
    // Get first pending file from the list
    fileData = picturesToUpload.shift();
    // Get uploaded photo id
    currentUploadId = fileData.id;
    // Show upload status on the page
    $("#photo_row_new_" + fileData.id + " .status").html("Envoi en cours...");
    // Create data for post
    data = new FormData();
    data.append('id', currentUploadId);
    data.append('file', fileData.file);
    data.append('album_id', currentAlbumId);
    // Upload file
    $.ajax({
      url: uploadPhotoURL,
      type: "POST",
      data: data,
      cache: false,
      processData: false, // Don't process the files
      contentType: false // Set content type to false as jQuery will tell the server its a query string request
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        // Photo successfully uploaded
        // Create new row for this photo
        var prototype = $("#photo-row-prototype");
        var newRow = prototype.clone();
        newRow.find('.photo-thumbnail img').attr('src', data.photo_thumbnail_url);
        newRow.addClass('draggable-row');
        newRow.attr('id', "photo-" + data.photo_id);
        newRow.data('draggable-id', data.photo_id);
        newRow.data('photo-id', data.photo_id);
        newRow.initDraggableRow();
        newRow.find('.editable-text').data('editable-id', data.photo_id);
        newRow.find('.editable-text').initEditableText();
        newRow.show();
        $("#photo_row_new_" + data.id).before(newRow);
        initRotateButtons(newRow);
      } else {
        // Upload failed
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

/**
 * Link rotate action to rotate buttons within a given page element
 */
function initRotateButtons($container) {
  $container.find(".rotate-clockwise-button").click(function() {
    var photoId = $(this).closest(".photo-row").data("photo-id");
    rotatePicture(photoId, true);
  });
  $container.find(".rotate-anticlockwise-button").click(function() {
    var photoId = $(this).closest(".photo-row").data("photo-id");
    rotatePicture(photoId, false);
  });
}

/**
 * Rotates a photo (clockwise or counterclockwise) and uploads the change
 */
function rotatePicture(photoId, clockwise) {
  $.ajax({
      url: rotatePhotoURL,
      type: "GET",
      data: { photo_id: photoId, clockwise: clockwise}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        // Refresh preview
        var img = $("#photo-" + photoId + " .photo-thumbnail img");
        var src = img.attr('src');
        if (src) {
          if (src.indexOf("?") !== -1) src = src.substring(0, src.indexOf("?"));
          src = src + "?" + new Date().getTime();
          img.attr('src', src);
        }
      } else {
        // Display error message to user
        alert("Une erreur est survenue. L'image n'a pas été tournée.");
      }
    });
}
