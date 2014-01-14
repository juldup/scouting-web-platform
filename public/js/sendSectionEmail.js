$().ready(function() {
  $(".recipient-check-all").click(function(event) {
    var parent = $(event.target).closest('.recipient-list');
    console.log(parent);
    parent.find('.recipient-checkbox').prop('checked', true).trigger("change");
    return false;
  });
  $(".recipient-uncheck-all").click(function(event) {
    var parent = $(event.target).closest('.recipient-list');
    console.log(parent);
    parent.find('.recipient-checkbox').prop('checked', false).trigger("change");
    return false;
  });
  // Check form in javascript to avoid losing the attachments
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
    var extraRecipients = $("textarea#extra_recipients").val().split(",");
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
    // Compte le nombre de destinataires
    if ($(".recipient-list .recipient-checkbox:checked").length === 0 && extraRecipientsCount === 0) {
      alert("Il n'y a aucun destinataire sélectionné !");
      return false;
    }
    // Everything is correct
    return true;
  });
});

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
