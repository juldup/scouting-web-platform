function savePhotoOrder(table, photoOrder) {
  $.ajax({
    type: "POST",
    url: savePhotoOrderURL,
    data: { photo_order: photoOrder }
  }).done(function(json) {
    console.log("data: " + json);
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