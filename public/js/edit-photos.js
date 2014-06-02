/**
 * This script is present on the photo management page
 */

$().ready(function() {
  // Add confirmation on archive buttons
  $(".archive-photo-album-button").click(function() {
    return confirm("Archiver cet album ?");
  });
});

/**
 * Uploads the new album order (see reorder-list.js)
 */
function saveAlbumOrder(table, albumOrder) {
  $.ajax({
    type: "POST",
    url: saveAlbumOrderURL,
    data: { album_order: albumOrder }
  }).done(function(json) {
    data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
    } else {
      alert("Le nouvel ordre des albums n'a pas pu être sauvé.");
      // Reload page
      window.location = window.location;
    }
  });
}

// Set save new order function (see reorder-list.js)
var saveDraggableOrder = saveAlbumOrder;
