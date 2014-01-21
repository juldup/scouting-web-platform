function addDocument() {
  $("#document_form legend:first").html("Nouveau document");
  $("#document_form [name='doc_id']").val("");
  $("#document_form [name='doc_title']").val("");
  $("#document_form [name='description']").val("");
  $("#document_form [name='category'").val("Divers");
  $("#document_form [name='public']").prop("checked", false).trigger("change");
  $("#document_form [name='filename']").val("");
  $("#document_form [name='file']").val("");
  $("#document_form #delete_link").hide();
  $("#document_form").slideDown();
}

function dismissDocumentForm() {
  $("#document_form").slideUp();
}

function editDocument(docId) {
  $("#document_form legend:first").html("Modifier un document");
  $("#document_form [name='doc_id']").val(docId);
  $("#document_form [name='doc_title']").val(documents[docId].title);
  $("#document_form [name='description']").val(documents[docId].description);
  $("#document_form [name='category'").val(documents[docId].category);
  if (!$("#document_form [name='category'").val()) {
    $("#document_form [name='category'").val("Divers");
  }
  $("#document_form [name='public']").prop("checked", documents[docId].public).trigger("change");
  $("#document_form [name='filename']").val("");
  $("#document_form [name='file']").val("");
  $("#document_form #delete_link").attr('href', documents[docId].delete_url);
  $("#document_form #delete_link").show();
  $("#document_form").slideDown();
}
