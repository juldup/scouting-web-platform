$().ready(function() {
  $(".warning-delete").click(function() {
    return confirm("Veux-tu vraiment supprimer cet utilisateur ?");
  });
});
