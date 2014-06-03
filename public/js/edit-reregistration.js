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
 * This script is present on the reregistration management page
 */

$().ready(function() {
  // Save reregistration status when a reregistration button is clicked
  $(".reregister-member-button").on('click', function() {
    var row = $(this).closest('.member-row');
    var memberId = row.data('member-id');
    $.ajax({
      url: reregisterMemberURL,
      data: {member_id: memberId}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        row.find('.unreregistered').hide();
        row.find('.reregistered').show();
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
  // Cancel reregistration status when a cancel button is clicked
  $(".cancel-reregistration-button").on('click', function() {
    var row = $(this).closest('.member-row');
    var memberId = row.data('member-id');
    $.ajax({
      url: unreregisterMemberURL,
      data: {member_id: memberId}
    }).done(function(json) {
      data = JSON.parse(json);
      if (data.result === "Success") {
        row.find('.unreregistered').show();
        row.find('.reregistered').hide();
      } else {
        alert("Une erreur s'est produite : " + data.message);
      }
    });
    return false;
  });
  // Delete a member when a delete button is clicked
  $(".delete-member-button").on('click', function() {
    var row = $(this).closest('.member-row');
    var memberName = row.find('.member-name').text().trim();
    var memberId = row.data('member-id');
    if (confirm("Cette action va supprimer définitivement " + memberName + " du listing. Continuer ?")) {
      $.ajax({
        url: deleteMemberURL,
        data: {member_id: memberId}
      }).done(function(json) {
        data = JSON.parse(json);
        if (data.result === "Success") {
          row.remove();
        } else {
          alert("Une erreur s'est produite : " + data.message);
        }
      });
      
    }
    return false;
  });
});
