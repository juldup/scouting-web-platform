/**
 * This script is present on the link management page
 */

/**
 * Empties and shows the link form
 */
function addLink() {
  $("#link_form [name='link_id']").val("");
  $("#link_form [name='link_title']").val("");
  $("#link_form [name='link_url']").val("");
  $("#link_form [name='link_description']").val("");
  $("#link_form #delete_link").hide();
  $("#link_form").slideDown();
}

/**
 * Hides the link form
 */
function dismissLinkForm() {
  $("#link_form").slideUp();
}

/**
 * Sets the link form to match an existing link and shows it
 */
function editLink(linkId) {
  $("#link_form [name='link_id']").val(linkId);
  $("#link_form [name='link_title']").val(links[linkId].title);
  $("#link_form [name='link_url']").val(links[linkId].url);
  $("#link_form [name='link_description']").val(links[linkId].description);
  $("#link_form #delete_link").attr('href', links[linkId].delete_url);
  $("#link_form #delete_link").show();
  $("#link_form").slideDown();
}
