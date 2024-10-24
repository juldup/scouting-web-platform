/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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
 * This script is present on the page management page
 */

$().ready(function() {
  // Delete button
  $(".delete-page-button").click(function(event) {
    return confirm("Supprimer cette page ?");
  });
});



// Set save new order function (see reorder-list.js)
var saveDraggableOrder = function(table, pageOrder) {
  $.ajax({
    type: "POST",
    url: savePageOrderURL,
    data: { page_order: pageOrder }
  }).done(function(json) {
    var data = JSON.parse(json);
    if (data.result === "Success") {
      // OK, do nothing
    } else {
      alert("Le nouvel ordre des pages n'a pas pu être sauvé.");
      // Reload page
      window.location = window.location;
    }
  }).error(function() {
    alert("Le nouvel ordre des pages n'a pas pu être sauvé.");
    // Reload page
    window.location = window.location;
  });
};
