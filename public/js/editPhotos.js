var albumOrder;
var movingAlbum;

function computeAlbumOrder() {
  var order = "";
  $("#album-table tr.album-row").each(function() {
    order += $(this).data('album-id') + " ";
  });
 return order.trim();
}

function dragStartAlbum(event) {
  movingAlbum = $(event.target).closest("tr");
  movingAlbum.addClass("album-drag");
  albumOrder = computeAlbumOrder();
}

function dragOverAlbum(event) {
  event.preventDefault();
  var currentTarget = $(event.target).closest("tr");
  if (currentTarget != movingAlbum) {
    if (currentTarget.index() > movingAlbum.index()) {
      currentTarget.after(movingAlbum);
    } else {
      currentTarget.before(movingAlbum);
    }
  }
}

function dragEndAlbum(event) {
  event.preventDefault();
  movingAlbum.removeClass("album-drag");
  var newAlbumOrder = computeAlbumOrder();
  if (newAlbumOrder != albumOrder) {
    saveAlbumOrder(newAlbumOrder);
  }
}

function saveAlbumOrder(albumOrder) {
  $.ajax({
    type: "POST",
    url: saveAlbumOrderURL,
    data: { album_order: albumOrder }
  }).done(function(json) {
    console.log(json);
    data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
    } else {
      alert("Le nouvel ordre des albums n'a pas pu être sauvé");
      // Reload page
      window.location = window.location;
    }
  });
}

$().ready(function() {
  $("#album-table tr.album-row").each(function() {
    $(this).attr('onDragOver', "dragOverAlbum(event)");
    $(this).attr('onDragStart', "dragStartAlbum(event)");
    $(this).attr('onDragEnd', "dragEndAlbum(event)");
    $(this).attr('draggable', "true");
    
  });
});