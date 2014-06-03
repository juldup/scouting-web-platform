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
 * This script is present on the suggestion management page
 */

$().ready(function() {
  // Edit response button
  $(".suggestion-edit-response-button").click(function() {
    var responseBox = $(this).closest('.suggestion-response').find('.suggestion-edit-response');
    // Toggle response box
    if (responseBox.is(':visible')) {
      responseBox.slideUp();
    } else {
      $(".suggestion-edit-response").slideUp();
      $(this).closest('.suggestion-response').find('.suggestion-edit-response').slideDown();
      $(this).closest('.suggestion-response').find('.suggestion-edit-response textarea').focus();
    }
    return false;
  });
});