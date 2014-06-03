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
 * This script is present on the document management page
 */

// Add confirmation on archive buttons
$().ready(function() {
  $(".archive-document-button").click(function() {
    return confirm("Archiver ce document ?");
  });
});

/**
 * Empties and shows the document form
 */
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

/**
 * Hides the document form
 */
function dismissDocumentForm() {
  $("#document_form").slideUp();
}

/**
 * Sets the document form to match a document and shows it
 */
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
  if (documents[docId].delete_url === "")
    $("#document_form #delete_link").hide();
  else
    $("#document_form #delete_link").show();
  $("#document_form").slideDown();
}
