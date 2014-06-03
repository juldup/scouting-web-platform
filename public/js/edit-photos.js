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
