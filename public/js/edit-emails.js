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
 * This script is present on the e-mail management page
 */

$().ready(function() {
  // Add confirmation on the archive buttons
  $(".archive-email-button").click(function() {
    return confirm("Archiver cet e-mail ?");
  });
  // Show/hide e-mail recipient list on click
  $(".email-recipient-list").click(function() {
    $(this).find(".email-recipient-list-content").toggle();
  })
});
