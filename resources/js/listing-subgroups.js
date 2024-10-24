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
 * This script is present on the subgroup listing
 */
$().ready(function() {
  $(".close-button").click(function() {
    $(this).closest(".subgroup-listing-details").hide();
  });
  $(".subgroup-listing-details").click(function() {
    $(this).hide();
  });
  $('.subgroup-listing-details-panel').click(function(event) {
    event.stopPropagation();
  });
  $(document).on('keydown', function(event) {
    if(event.key == "Escape") {
      $(".subgroup-listing-details").hide();
    }
  });
});

/**
 * Shows the member details section of a given member
 */
window.showMemberDetails = function(memberId) {
  var element = $("#details_" + memberId);
  $(".details_member:visible").hide();
  element.show();
  $(document).focus();
};
