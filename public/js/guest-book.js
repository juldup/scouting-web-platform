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
 * This script is present on the guest book page
 */

$().ready(function() {
  // Show new message form when button is clicked
  $(".guest-book-button").click(function() {
    $(".guest-book-button").hide();
    $(".guest-book-form").slideDown();
  });
  // Hide new message form when edition is canceled
  $(".guest-book-cancel").click(function() {
    $(".guest-book-form").slideUp(null, function() {
      $(".guest-book-button").show();
    });
    return false;
  });
});
