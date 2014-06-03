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
 * This script is present on all the page content editor pages
 */

/**
 * Inserts the given image at the current location in the text
 */
function editPageInsertImage(imageURL) {
  var element = CKEDITOR.dom.element.createFromHtml("<img style='max-width: 80%' src='" + imageURL + "'/>");
  CKEDITOR.instances['page_body'].insertElement(element);
}

// Initialize functionality to upload an image and add it to the list
$().ready(function() {
  if (typeof image_upload_url != 'undefined') {
    var element = document.getElementById('uploader');
    upclick(
      {
      element:element, 
      action: image_upload_url, 
      onstart: 
        function(filename) {},
      oncomplete:
        function(json) {
          data = JSON.parse(json);
          if (data.result == "OK") {
            addImageToList(data);
          } else {
            alert(data.message);
          }
        }
      }
    );
  }
});

/**
 * Adds an image the to list of images
 */
function addImageToList(data) {
  $("#edit_page_form #image_list").append(
          "<span id='image_" + data.image_id + "'>" +
          " <a href=\"javascript:editPageInsertImage('" + data.url + "');\">" +
          "<img src='" + data.url + "' class='image_preview_edit_page'/></a>" +
          " <a href='' onclick='return removeImage(" + data.image_id + ")'><span class='glyphicon glyphicon-remove'></span></a>" +
          " <span class='horiz-divider'></span>");
}

// Initially add all images from the initial_images array to the image list
$().ready(function() {
  if (typeof initial_images !== "undefined") {
    initial_images.forEach(function(image_data) {
      addImageToList(image_data);
    });
  }
});

/**
 * Removes an image from the list and saves the change
 */
function removeImage(image_id) {
  var url = image_remove_url.replace("image_id", image_id);
  $.ajax(url).done(function(json) {
    data = JSON.parse(json);
    if (data.result == "OK") {
      $("#image_" + data.image_id).remove();
    }
  }).fail(function(jqXHR, textStatus) {
  });
  return false;
}
