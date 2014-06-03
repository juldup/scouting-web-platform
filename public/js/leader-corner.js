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
 * This script is present on the leader corner page
 */

$().ready(function() {
  // Show help when clicking the help button
  $(".help-badge").click(function(event) {
    event.stopPropagation();
    var help = $(this).closest(".leader-help-item").data('leader-help');
    $(".leader-corner-help:visible").hide();
    $(".leader-corner-help[data-leader-help='" + help + "'").show();
    $(".leader-help-general").hide();
  });
  // Hide help when clicking on the back-to-top icon
  $(".back-to-top").click(function(event) {
    $(".leader-corner-help:visible").hide();
    $(".leader-help-general").show();
  });
});
