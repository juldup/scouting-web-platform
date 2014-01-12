// Init: make rows draggable
$().ready(function() {
  $(".draggable-table").each(function() {
    $(this).data('order', '');
    $(this).find(".draggable-row").each(function() {
      $(this).attr('onDragStart', "draggableDragStart(event)");
      $(this).attr('onDragOver', "draggableDragOver(event)");
      $(this).attr('onDragEnd', "draggableDragEnd(event)");
      $(this).attr('draggable', "true");
    });
  });
});

// Compute the current order of the rows in the table
$.fn.computeCurrentOrder = function() {
  var order = "";
  // For each row in order
  $(this).find(".draggable-row").each(function() {
    // Add its id to the list
    order += $(this).data('draggable-id') + " ";
  });
  // Remove trailing space and return order
  return order.trim();
};

// Start dragging
function draggableDragStart(event) {
  // Get the table involved in this event
  var table = $(event.target).closest('.draggable-table');
  // Get the row being dragged
  var movingRow = $(event.target).closest(".draggable-row");
  // Mark the row being dragged
  movingRow.addClass("dragged-row");
  // Compute and save current order
  table.data('order', table.computeCurrentOrder());
}

// Drag over another element
function draggableDragOver(event) {
  // Prevent any default behavior
  event.preventDefault();
  // Get the table involved in this event
  var table = $(event.target).closest('.draggable-table');
  // Get the row being dragged over
  var currentTarget = $(event.target).closest(".draggable-row");
  // Get the row being dragged
  var movingRow = table.find(".dragged-row").first();
  // Make sure this is not the event of the row being dragged over itself
  if (currentTarget[0] !== movingRow[0]) {
    // Reorder rows
    if (currentTarget.index() > movingRow.index()) {
      currentTarget.after(movingRow);
    } else {
      currentTarget.before(movingRow);
    }
  }
}

// Drag ending
function draggableDragEnd(event) {
  // Prevent any default behavior
  event.preventDefault();
  // Get the table involved in this event
  var table = $(event.target).closest('.draggable-table');
  // Get the row that was being dragged
  var movingRow = $(event.target).closest(".draggable-row");
  // Remove dragged marker class
  movingRow.removeClass("dragged-row");
  // Compute the new order
  var newOrder = table.computeCurrentOrder();
  // Save the new order if it is different from before dragging
  if (newOrder !== table.data('order')) {
    if (typeof saveDraggableOrder === 'function') {
      saveDraggableOrder(table, newOrder);
    } else {
      console.warn("Function saveDraggableOrder does not exist");
    }
  }
}
