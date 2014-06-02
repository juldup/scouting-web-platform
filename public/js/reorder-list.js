/**
 * This script provides a generic tool to have lists that can be reordered
 * by drag and drop.
 * 
 * The data on the page must be organized as such:
 *   <div class="draggable-table">
 *     <div class="draggable-row" data-draggable-id="%ID1%">
 *     <div class="draggable-row" data-draggable-id="%ID2%">
 *     ...
 *   </div>
 * The table can be a <table> and the rows <tr>.
 * 
 * When the order is changed, the saveDraggableOrder is called if it is defined.
 * saveDraggableOrder(table, newOrder) takes two arguments:
 *   - table: a jquery object containing the related .draggable-table
 *   - newOrder: the ids of the rows separated by spaces
 * 
 */

$().ready(function() {
  // Make all rows draggable
  $(".draggable-table").each(function() {
    $(this).data('order', '');
    $(this).find(".draggable-row").initDraggableRow();
  });
});

/**
 * Link functions to drag events on the given row
 */
$.fn.initDraggableRow = function() {
  $(this).attr('onDragStart', "draggableDragStart(event)");
  $(this).attr('onDragOver', "draggableDragOver(event)");
  $(this).attr('onDragEnd', "draggableDragEnd(event)");
  $(this).attr('draggable', "true");
};

/**
 * Computes and returns the current order of the rows in the table
 */
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

/**
 * Called when a drag is started on a row
 */
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

/**
 * Called when something is being dragged on a row
 */
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

/**
 * Called when the drag is being dropped
 */
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
