$().ready(function() {
  // Logo preview
  $("input[type='file'][name='logo']").change(function(event) {
    // Put selected image in logo preview
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function(theFile) {
      return function(event) {
        var src = event.target.result;
        $("img.website-logo-preview").attr('src', src);
      };
    }(file);
    reader.readAsDataURL(file);
  });
  // Document categories
  $('.document-category-remove').click(function() {
    $(this).closest(".document-category-row").remove();
  })
  $('.document-category-add').click(function() {
    var newElement = $('.document-category-prototype').clone(true);
    $(this).closest(".row").before(newElement);
    newElement.removeClass('document-category-prototype');
    newElement.show();
  });
  // Verified e-mail senders
  $('.safe-email-remove').click(function() {
    $(this).closest(".safe-email-row").remove();
  })
  $('.safe-email-add').click(function() {
    var newElement = $('.safe-email-row-prototype').clone(true);
    $(this).closest(".row").before(newElement);
    newElement.removeClass('safe-email-row-prototype');
    newElement.show();
  });
});
