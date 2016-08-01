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
 * This script is present on the section e-mail sending page
 */

// Number of attachment fields
var attachmentCount = 0;

$().ready(function() {
  // Checks all recipients within recipient list
  $(".recipient-check-all").click(function(event) {
    var parent = $(event.target).closest('.recipient-list-wrapper');
    parent.find('.recipient-checkbox').prop('checked', true).trigger("change");
    // Unhide list
    parent.find('.recipient-list').slideDown();
    parent.find('.recipient-list-warning').hide();
    return false;
  });
  // Unchecks all recipients within recipien list
  $(".recipient-uncheck-all").click(function(event) {
    var parent = $(event.target).closest('.recipient-list-wrapper');
    parent.find('.recipient-checkbox').prop('checked', false).trigger("change");
    // Unhide list
    parent.find('.recipient-list').slideDown();
    parent.find('.recipient-list-warning').hide();
    return false;
  });
  // Toggle recipient list visibility on label press
  $(".recipient-list-wrapper label.recipient-category").click(function(event) {
    var parent = $(event.target).closest('.recipient-list-wrapper');
    var warning = parent.find(".recipient-list-warning").first();
    var recipientList = parent.find(".recipient-list").first();
    if (recipientList.is(":visible")) {
      recipientList.slideUp(null, function() {
        warning.show();
      });
    } else {
      recipientList.slideDown(null, function() {
        warning.hide();
      });
    }
  });
  // Add an attachment button
  $("#add-attachment-button").click(function(event) {
    attachmentCount++;
    var clone = $(".attachment-input-wrapper").first().clone();
    clone.show();
    clone.find("input").attr('name', 'attachments[' + attachmentCount + ']');
    $(this).before(clone);
    clone.find('.remove-attachment').click(function() {
      $(this).closest(".attachment-input-wrapper").remove();
    });
    clone.find("input").trigger('click');
  });
  // Checks form in javascript to avoid losing the attachments
  $("#email-form").submit(function() {
    // Check subject
    var subject = $("input#subject").val().trim();
    if (subject === "" || subject === defaultSubject.trim()) {
      $("input#subject").focus();
      alert("Tu dois entrer un sujet !");
      return false;
    }
    // Check e-mail message
    var message = CKEDITOR.instances.body.getData();
    if (message.trim() === "") {
      alert("Tu dois écrire le contenu de l'e-mail.");
      CKEDITOR.instances.body.focus();
      return false;
    }
    // Check sender address
    var address = $("input#sender_address").val().trim();
    if (!validateEmail(address)) {
      $("input#sender_address").focus();
      alert("L'adresse de l'expéditeur n'est pas valide !");
      return false;
    }
    // Extra recipients
    var extraRecipients = $("textarea#extra_recipients").val().split(/[\s,;]+/);
    var extraRecipientsCount = 0;
    var extraRecipientError = "";
    extraRecipients.forEach(function(email) {
      if (email.trim() !== "") {
        if (validateEmail(email.trim())) {
          extraRecipientsCount++;
        } else {
          extraRecipientError += "L'adresse e-mail " + email + " n'est pas valide. ";
        }
      }
    });
    if (extraRecipientError !== "") {
      $("textarea#extra_recipients").focus();
      alert(extraRecipientError);
      return false;
    }
    // Make sure there is at least one recipient
    if ($(".recipient-list .recipient-checkbox:checked").length === 0 && extraRecipientsCount === 0) {
      alert("Il n'y a aucun destinataire sélectionné !");
      return false;
    }
    // Everything is correct
    return true;
  });
  // Show/hide signature when toggled
  $("input[name='sign_email']").change(function() {
    if ($(this).prop('checked')) {
      $(".email-signature-wrapper").removeClass('signature-hidden');
    } else {
      $(".email-signature-wrapper").addClass('signature-hidden');
    }
  });
});

/**
 * Returns whether an e-mail address is valid
 */
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
