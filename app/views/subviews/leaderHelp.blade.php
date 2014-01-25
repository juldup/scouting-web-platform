@section('back_to_top')
  <a href="#"><span class="glyphicon glyphicon-hand-up"></span></a>
@stop

<a name='{{ $help }}'></a>
<div class="well">

@if ($help == 'general')
  <h2><a name='aide_infos_generales'></a>Informations générales sur la structure du site</h2>
  <p>Ce site a été conçu pour être modulable.  Toutes les informations annuelles (listing, photos, calendrier, documents, etc.) sont changeables.<p>
  <p>Il y a deux manières de visiter le site : en tant que visiteur, parent, scout, ou bien en tant qu'animateur.  Les animateurs ont le droit de modifier toutes les informations se trouvant sur le site.  Sois donc prudent avec ce que tu fais, car certaines opérations ne peuvent être annulées.</p>
  <h3>Les droits d'accès</h3>
  <p>Les accès aux pages et informations du site dépendent du statut du visiteur :</p>
  <table style='margin-left: 50px'>
    <tr><td style='vertical-align: top'><span class='important'>Non inscrit</span>&nbsp;: <td>Accès limité aux pages publiques.</tr>
    <tr><td style='vertical-align: top'><span class='important'>Visiteur</span>&nbsp;: <td>Il peut écrire dans le livre d'or, mais n'a accès à aucune information privée.</tr>
    <tr><td style='vertical-align: top'><span class='important'>Membre</span>&nbsp;: <td>Un membre (scout ou parent) peut consulter les listings limités, télécharger les documents, voir les e-mail, les photos et créer des fiches santé pour sa famille.  Un compte d'utilisateur est automatiquement membre si son adresse e-mail a été validée et fait partie de nos listings.</tr>
    <tr><td style='vertical-align: top'><span class='important'>Animateur</span>&nbsp;: <td>Un animateur peut accéder au coin des animateurs. Certains droits lui sont attribués par l'animateur d'unité.</tr>
    <tr><td style='vertical-align: top'><span class='important'>Webmaster</span>&nbsp;: <td>Il n'a aucune limitation.</tr>
  </table>
  <h3>Les onglets</h3>
  <p>Chaque section possède un onglet (voir en haut de la page).  Changer d'onglet adapte le site à la section, tant pour les visiteurs que pour les animateurs.  En particulier, les données modifiables sont limitées à celles de ta section, à moins que tu n'aies des privilèges spéciaux.</p>
@endif

@if ($help == 'calendrier')
  <legend>Calendrier @yield('back_to_top')</legend>
  <p>Cette page permet d'ajouter/modifier/supprimer des événements dans le calendrier.  Pour modifier le calendrier d'une section, il faut sélectionner l'onglet de la section.</p>
  <h3>Ajouter un événement</h3>
  <ol>
    <li>Sélectionne dans le calendrier la date de début de l'événement
    <li>Une fenêtre apparaît pour l'ajout d'événement
    <li>Choisis le nombre de jours (en comptant le premier et le dernier jour de l'activité, ex: vendredi à dimanche = 3 jours).
    <li>Activité = nom de l'activité (telle qu'elle apparaitra dans le calendrier)
    <li>Description = détails de l'activité (apparait quand on laisse la souris sur un événement et affichée dans la liste d'événements)
    <li>Le type d'événement déterminera l'icône ; les types <i>Animateurs uniquement</i> et <i>Nettoyage toilettes</i> ne seront visibles que par les animateurs
    <li>Vérifie que la bonne section est sélectionnée
    <li><span class='important'>Enregistrer</span> ajoute immédiatement l'événement dans le calendrier
  </ol>
  <h3>Modifier un événement</h3>
  Il suffit de cliquer sur l'événement dans le calendrier pour le modifier.
  <h3>Supprimer un événement</h3>
  <p>Il est inutile de supprimer les événements qui se sont déjà déroulés (ceux-ci sont grisés dans la liste en bas, et les événements des années précédentes ne sont plus affichés dans la liste).
  <p>Pour supprimer un événement, deux possibilités :
  <ul>
    <li>Cliquer sur l'événement dans le calendrier, et cliquer sur supprimer.
    <li>Cliquer directement sur le bouton supprimer dans la liste.
  </ul>
@endif

@if ($help == 'photos')
  <legend>Gérer les photos @yield('back_to_top')</legend>
  <h3>Créer un album</h3>
  <p>Tu peux créer un album via <span class='important'>Ajouter un album</span>. L'idéal est de créer un album par réunion ou activité.</p>
  <p>Tu peux <span class='important'>réordonner</span> les répertoires en les glissant.</p>
  <p>Tu peux <span class='important'>supprimer</span> un répertoire à condition qu'il soit vide. Pour le vider, clique dessus et déplace ses photos vers la poubelle.</p>
  <h3>Mettre des photos en ligne</h3>
  <p>Clique sur un répertoire pour y ajouter des photos. Glisse des photos dans la zone appropriée</p>
  <p>Les photos au format <span class='important'>jpg</span> et <span class='important'>png</span> sont acceptées.
  <p>Les photos seront affichées par ordre alphabétique des noms de fichiers. Tu peux cependant sélectionner la photo de couverture via le<img height=24 style="vertical-align: middle" src='images/photoSecondaire.png'>; elle apparaitra en premier.</p>
  <p>Tu peux faire <span class='important'>tourner</span> une photo mal orientée. Tu peux également lui adjoindre un <span class='important'>commentaire</span> qui apparaitra sous la photo.</p>
  <p>Tu peux supprimer des photos en les glissant vers la poubelle, ou les glisser vers un autre album.</p>
@endif

@if ($help == 'documents')
<legend>Gérer les documents @yield('back_to_top')</legend>
  <p>Cette page te permet d'ajouter, modifier et archiver des documents. Place ici tous les documents destinés aux parents ou aux animés, en particulier les convocations envoyées aux parents.
  <p>Pour ajouter un document, il suffit de cliquer sur <span class='important'>Ajouter un document</span>, en indiquer le nom et la description, choisir le fichier et valider.
     Un document peut également être modifié, remplacé ou supprimé. Pour remplacer un document, modifie-le et sélectionne un nouveau fichier.
  <p>Il n'est pas possible se supprimer des documents qui sont en ligne depuis plus d'une semaine.  Les documents obsolètes peuvent être archivés, et seront donc toujours disponibles.
     Si un document doit absolument être supprimé car son contenu n'est pas adéquat, contacte le <a href="envoiEmail.php?dest=webmaster">webmaster du site</a>.
  <h3>Les différents champs à compléter</h3>
  <ul>
    <li><span class='important'>Titre</span>&nbsp;: c'est le nom qui sera affiché dans la liste
    <li><span class='important'>Description</span>&nbsp;: la description sera affichée en-dessous
    <li><span class='important'>Catégorie</span>&nbsp;: les documents sont triés par catégorie, le choix de la catégorie est limité
    <li><span class='important'>Fichier</span>&nbsp;: sélectionne le fichier à télécharger
    <li><span class='important'>Nom du fichier</span>&nbsp;: nom qu'aura le fichier quand on le téléchargera.  Pour garder le nom d'origine, laisse-le vide.
    <li><span class='important'>Public</span>&nbsp;: si cette case est cochée, le document est public, donc visible par tous les internautes et pas juste les membres
    <li><span class='important'>Valider&nbsp;</span>: enregistre le document
  </ul>
@endif

</div>