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
 * This script is present on the absence page
 */

$().ready(function() {
  // Show form when a member is selected
  $('.absence-member-button').click(function() {
    $('.absence-form').hide();
    var id = $(this).data('member-id');
    $('#form-' + id).slideDown();
  });
  // When an event is selected
  $('.select-event').change(function() {
    // Show or hide other event
    if ($(this).val() === "0") {
      $(this).closest('form').find('.input-other-event').show();
    } else {
      $(this).closest('form').find('.input-other-event').hide();
    }
    // Enable or disable submit button
    if ($(this).val() === "") {
      $(this).closest('form').find('.disabled-submit').show();
      $(this).closest('form').find('.enabled-submit').hide();
    } else {
      console.log('enabling');
      $(this).closest('form').find('.disabled-submit').hide();
      $(this).closest('form').find('.enabled-submit').show();
    }
  });
});
