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

var saveDraggableOrder = saveAlbumOrder;