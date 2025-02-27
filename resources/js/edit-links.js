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
 * This script is present on the link management page
 */

/**
 * Empties and shows the link form
 */
window.addLink = function() {
  $("#link_form [name='link_id']").val("");
  $("#link_form [name='link_title']").val("");
  $("#link_form [name='link_url']").val("");
  $("#link_form [name='link_description']").val("");
  $("#link_form #delete_link").hide();
  $("#link_form").slideDown();
}

/**
 * Hides the link form
 */
window.dismissLinkForm = function() {
  $("#link_form").slideUp();
}

/**
 * Sets the link form to match an existing link and shows it
 */
window.editLink = function(linkId) {
  $("#link_form [name='link_id']").val(linkId);
  $("#link_form [name='link_title']").val(links[linkId].title);
  $("#link_form [name='link_url']").val(links[linkId].url);
  $("#link_form [name='link_description']").val(links[linkId].description);
  $("#link_form #delete_link").attr('href', links[linkId].delete_url);
  $("#link_form #delete_link").show();
  $("#link_form").slideDown();
}
