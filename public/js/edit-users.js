/**
 * This script is present on the user management page
 */

$().ready(function() {
  // Add confirmation on delete buttons
  $(".warning-delete").click(function() {
    return confirm("Veux-tu vraiment supprimer cet utilisateur ?");
  });
});
