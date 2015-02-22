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
 * This script provides a generic tool to have lists that can be reordered
 * by drag and drop.
 * 
 * The data on the page must be organized as such:
 *   <table>
 *     <tbody class="draggable-tbody">
 *       <tr class="draggable-row" data-draggable-id="%ID1%">
 *       <tr class="draggable-row" data-draggable-id="%ID2%">
 *       ...
 *     </tbody>
 *   </table>
 * The tbody can be a <tbody> and the rows <tr>.
 * 
 * When the order is changed, the saveDraggableOrder is called if it is defined.
 * saveDraggableOrder(tbody, newOrder) takes two arguments:
 *   - tbody: a jquery object containing the related .draggable-tbody
 *   - newOrder: the ids of the rows separated by spaces
 * 
 */

$().ready(function() {
  // Make all rows draggable
  $(".draggable-tbody").each(function() {
    $(this).data('order', '');
  });
  $(".draggable-tbody").sortable({
    start: function(event, ui) {
      ui.placeholder.height(ui.item.height());
      draggableDragStart(event);
    },
    scroll: true,
    stop: function(event, ui) { draggableDragEnd(event); },
    helper: function(event, ui) {
      // Make the dragged row as large as the original one
      ui.children().each(function() {
          $(this).width($(this).width());
        });
      return ui;
    }
  });
});

/**
 * Computes and returns the current order of the rows in the table
 */
$.fn.computeCurrentOrder = function() {
  var order = "";
  // For each row in order
  $(this).find(".draggable-row").each(function() {
    // Add its id to the list
    var id = $(this).data('draggable-id');
    if (id != undefined) order += $(this).data('draggable-id') + " ";
  });
  // Remove trailing space and return order
  return order.trim();
};

/**
 * Called when a drag is started on a row
 */
function draggableDragStart(event) {
  // Get the table involved in this event
  var table = $(event.target).closest('.draggable-tbody');
  // Get the row being dragged
  var movingRow = $(event.target).closest(".draggable-row");
  // Mark the row being dragged
  movingRow.addClass("dragged-row");
  // Compute and save current order
  table.data('order', table.computeCurrentOrder());
}

/**
 * Called when the drag is being dropped
 */
function draggableDragEnd(event) {
  // Get the table involved in this event
  var table = $(event.target).closest('.draggable-tbody');
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
