$().ready(function() {
  // Prevent Enter key from validating the form
  document.onkeypress = function(evt) {
    var evt = (evt) ? evt : ((event) ? event : null);
    var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
    if ((evt.keyCode == 13) && ((node.type == "text" || node.type == "checkbox"))) {
      // Source: http://stackoverflow.com/questions/2455225/how-do-i-move-focus-to-next-input-with-jquery#2456761
      var inputs = $(evt.target).closest('form').find('input:visible,select:visible,checkbox:visible,textarea:visible');
      inputs.eq( inputs.index($(evt.target)) + 1 ).focus();
      return false;
    }
  }
});