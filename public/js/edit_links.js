function addLink() {
  $("#link_form [name='link_id']").val("");
  $("#link_form [name='link_title']").val("");
  $("#link_form [name='link_url']").val("");
  $("#link_form [name='link_description']").val("");
  $("#link_form #delete_link").hide();
  $("#link_form").slideDown();
}

function dismissLinkForm() {
  $("#link_form").slideUp();
}

function editLink(linkId) {
  $("#link_form [name='link_id']").val(linkId);
  $("#link_form [name='link_title']").val(links[linkId].title);
  $("#link_form [name='link_url']").val(links[linkId].url);
  $("#link_form [name='link_description']").val(links[linkId].description);
  $("#link_form #delete_link").attr('href', links[linkId].delete_url);
  $("#link_form #delete_link").show();
  $("#link_form").slideDown();
}
