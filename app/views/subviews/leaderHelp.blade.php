@section('back_to_top')
  <a href="#" class="back-to-top"></a>
@stop

<a name='{{ $help }}'>&nbsp;</a>
<div class="well help-content">

@if ($help == 'general')
  <legend>Informations générales sur la structure du site</legend>
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

@if ($help == 'fiches-sante')
  <legend>Fiches santé @yield('back_to_top')</legend>
  {{-- TODO --}}
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

@if ($help == 'nouvelles')
  <legend>Gérer les nouvelles @yield('back_to_top') </legend>
  <p>Indique dans les nouvelles tout ce que tu veux communiquer (réunion spéciale qui s'est bien déroulée, produit en vente par la section, etc.)
  <p>Pour créer une nouvelle nouvelle, clique sur <span class='important'>Ajouter une nouvelle</span>.  Choisis le titre, et n'oublie pas la section.
  <p>La date d'une nouvelle est la date à laquelle la nouvelle est écrite.  Cette date figurera dans la liste des nouvelles.  Les nouvelles de plus d'un an ne sont plus affichées.  <!--Les nouvelles de plus d'un mois ne sont plus affichées dans la barre défilante.-->
  <p>Il est également possible de modifier une nouvelle.  Quand tu ne veux plus qu'une nouvelle s'affiche, clique sur <span class='important'>Modifier</span> et désélectionne la case <span class='important'>afficher</span>.  Si tu veux la supprimer définitivement, clique sur <span class='important'>Supprimer</span> (la nouvelle reste présente dans la base de données).
@endif

@if ($help == 'emails')
  <legend>Envoi d'un e-mail aux parents @yield('back_to_top')</legend>
  <p>Cet outil te permet d'envoyer un e-mail à tous les parents d'une section.
     L'adresse de chacun restera confidentielle, ceci permet d'éviter le spamming et l'abu d'envoi d'e-mails de la part des parents.
  <ol>
    <li>Entre le sujet de ton message (une en-tête est déjà encodée, libre à toi de la garder).
    <li>Tape le message
    <li>Vérifie l'adresse de l'expéditeur
    <li>Tu peux joindre jusqu'à 4 documents
    <li>Vérifie les destinataires (décoche ceux à qui tu ne veux pas envoyer l'e-mail).
        Par défaut, les destinataires sont les parents, les animés (éclaireurs et pionniers), les animateurs de la section et les animateurs d'unité.
        Tu peux cocher ou décocher toute la liste une partie grâce aux cases à cocher et aux boutons.
        Tu peux également ajouter des destinataires supplémentaires.
  </ol>
  <p>Note que si le nombre d'e-mails partant du site est trop important, certains e-mails seront automatiquement envoyés plus tard.</p>
@endif

@if ($help == 'inscriptions')
  <legend>Gérer les inscriptions, désinscriptions et passages @yield('back_to_top')</legend>
  <p>Cette page permet d'inscrire des nouveaux scouts ou animateurs, de changer des scouts d'année ou de section.</p>
  <h3>Validation d'une nouvelle inscription</h3>
  <p>Dans la liste, clique sur <span class='important'>Inscrire</span> à côté du nom du scout à inscrire.
     Un formulaire apparaît, avec les informations entrées par les parents pré-encodées.
     Vérifie les données <span class='important'>attentivement</span> avant d'inscrire définitivement le scout, en particulier la section dans laquelle il s'inscrit.
  <p>Une demande d'inscription erronée ou non acceptée peut être supprimée.
  <h3>Réinscription</h3>
  ...
  <h3>Passage d'une section à une autre</h3>
  <p>Cet outil te permet de faire évoluer des scouts d'une section à une autre.
  <p>Premièrement, sélectionne la section d'origine et la section d'arrivée, et clique sur <span class='important'>OK</span>.
  <p>Sélectionne les scouts que tu veux faire monter (ils sont classés par année, les scouts devant monter sont donc normalement en haut de la liste).  <span class='important'>Faire monter</span> change les scouts sélectionnés de section, met leur année à 1 et supprime leur nom de patrouille/sizaine/hutte.
  <p>Si une fausse manœuvre a été effectuée, tu peux refaire l'opération dans l'autre sens, ou directement faire le changement dans le listing.
  <h3>Augmentation de l'année des animés</h3>
  <p>Cet outil permet d'augmenter d'une année une liste d'animés.
     Sélectionne tout d'abord la section, puis la liste d'animés à augmenter d'une année.
     Ceux-ci apparaissent par ordre inverse d'année.
     L'année actuelle apparaît entre parenthèses.
  <p><span class='important'>Attention</span> à ne pas sélectionner les animés qui viennent de monter dans la section et dont l'année est à 1.
     Il vaut mieux faire l'opération de changement d'année avant l'opération de changement de section.
@endif

@if ($help == 'listing')
  <legend>Modifier le listing @yield('back_to_top')</legend>
  <p>Le listing du site web est le listing officiel de l'unité.  Il est disponible en ligne à tous les membres, et est utilisé pour mettre à jour le listing de la fédération.  <span class='important'>Il est donc primordial qu'il soit bien entretenu.</span>
  <p>Cette page permet de modifier le listing et de l'exporter.</p>
  <h3>Modifier et exporter le listing</h3>
  <p>Pour modifier une ligne du listing, il suffit de cliquer sur le bouton <a class='button' onClick='self.location="gestionEditionListing.php"'>Modifier</a>.
     Dans le tableau qui apparait, clique sur un champ pour le modifier. Les changements sont immédiats (dès qu'on clique hors du champ de saisie).
  <p>Il est possible de supprimer un scout du listing via ce formulaire.
  <p>Tu peux exporter le listing en format <span class='important'>Excel</span> ou <span class='important'>CSV</span>.
     Il est également possible de sortir les adresses imprimables sur des enveloppes. 
  <h3>Listing pour la fédération</h3>
  <p>Cet outil n'est à utiliser que par l'animateur d'unité responsable des listings.
  <p>Pour que cet outil fonctionne correctement, il est important que le listing soit correctement complété.
  <p>Pour obtenir un listing pour la fédé, il faut
    <ol>
      <li>Sauvergarder le listing actuel (le nom est la date et l'heure de la sauvegarde).
      <li>Sélectionner deux dates : la date du listing précédent donné à la fédé, et la date du dernier listing.
      <li>Cliquer sur <span class='important'>Exporter</span>.  Une page s'affiche avec le listing au format fédé.
      <li>Il est possible de l'exporter sous deux formats :
        <ul>
          <li>PDF : format à imprimer et envoyer, avec les modifications barrées et corrigées
          <li>Excel : format à envoyer par e-mail, avec uniquement les nouvelles valeurs et les membres supprimés
        </ul>
      <li>Le même fichier sera toujours regénéré à partir des deux mêmes dates, et il est tout à fait possible d'exporter les deux formats, car les données sauvegardées ne peuvent plus être changées.
    </ol>
  <p>Le bouton <span class='important'>Mode Excel</span> permet de prévisualiser le contenu du fichier Excel.
  <p>Avec cet outil, il est également possible d'obtenir un listing sans les modifications&nbsp;: il suffit de sélectionner deux fois la date voulue.
@endif

@if ($help == 'pages')
  <legend>Modifier les pages du site @yield('back_to_top')</legend>
  <p>Cet outil te permet de modifier la page d'accueil et d'uniforme des sections, et de la page d'accueil du site, de la présentation de l'unité et de la charte.
  <p>Pour modifier les pages de ta section, sélectionne tout d'abord le bon onglet.
  <h3>Modification de la page</h3>
  <p>Pour modifier la page, il suffit de cliquer sur le bouton <span class='important'>Modifier</span>.
     Il est ensuite possible de modifier le code <span class='important'>html</span> de la page (il faut évidemment s'y connaître un peu, ou demander un coup de main).
     Pour enregistrer, clique sur le bouton d'enregistrement.
  <p>Dès l'enregistrement, la page est directement accessible aux visiteurs.
  <h3>Photos dans la page</h3>
  <p>Il est possible d'ajouter des photos dans la page.
     Chacune des photos doit être ajoutée via le champ d'ajout de photo.
     Le bouton <span class='important'>+</span> permet d'ajouter plusieurs photos.
  <p>Dans le code, il suffit de mettre une balise image classique (<span class='important'>&lt;img src="taPhoto.jpg" alt="texte" /&gt;</span>).
     Le nom de l'image doit correspondre au nom du fichier chargé.
  <p>Lorsque la page est enregistrée, le chemin d'accès de la photo sera automatiquement changé (par exemple en <span class='important'>images/taSection/taPhoto.jpg</span>).
     Ne t'inquiète pas de cela.
  <h3>Aperçu de la page</h3>
  <p>En-dessous du bloc d'édition se trouve un aperçu de la page. Note que dans l'aperçu, les nouvelles photos ne seront pas encore présentes.
@endif

@if ($help == 'sections')
  <legend>Modification des sections @yield('back_to_top')</legend>
  <h3>Données d'une section (dans l'onglet de la section)</h3>
  <p>Il est possible de modifier :
    <ul>
      <li>L'adresse e-mail de la section</li>
      <li>La couleur de la section (clique sur <span class='important'>Sélectionner</span>)</span></li>
      <li><span class='interdit'>Les codes fédé de la section (normalement, à ne jamais modifier)</span></li>
    </ul>
  <p>Clique sur <span class='important'>Valider</span> pour enregistrer tes changements.
  <h3>Créer et renommer des sections (dans l'onglet "Unité")</h3>
  <p>Tu peux changer l'ordre dans lequel les sections apparaissent dans les onglets et dans les listes.</p>
  <p>Tu peux créer une nouvelle section. Encode les informations suivantes&nbsp;:
     <ul>
       <li><span class='important'>Nom</span> : nom de la section (p.ex. Waingunga)</li>
       <li><span class='important'>"la section"</span> : utilisé pour remplir certains textes du site (p.ex. la meute Waingunga)</li>
       <li><span class='important'>"de la section"</span> : utlisé pour remplir certains textes du site (p.ex. de la Waingunga)</li>
       <li><span class='important'>Couleur</span> : la couleur utilisée pour le calendrier</li>
       <li><span class='important'>E-mail</span> : l'adresse e-mail de la section (pour l'envoi de courriers électroniques et la page de contacts)</li>
       <li><span class='important'>Code fédération</span> : le code donné par la fédération à la section, utilisé notamment pour déterminer le type d'onglet à afficher (p.ex. B 1, L 2, E 3, P 1)</li>
     </ul>
  <p>Tu peux renommer une section existante.</p>
  <p>Tu peux supprimer une section si elle n'existe plus.
     Attention, cette opération ne peut être défaite.
     Il vaut parfois mieux garder la section et indiquer dans sa page d'accueil qu'elle n'existe plus.
  </p>
@endif

@if ($help == 'animateurs')
  <legend>Les animateurs @yield('back_to_top')</legend>
  <p>Le listing des animateurs sert à deux choses&nbsp;:
    <ul>
      <li>Mettre des informations publiques sur la page dédiée aux animateurs.</li>
      <li>Il sert de listing officiel pour la fédé.</li>
    </ul>
  Il est donc important que les informations qui s'y trouvent soient correctes et complètes.
  <h3>Inscrire un nouvel animateur</h3>
  <p>Lorsqu'un nouvel animateur entre dans l'unité, il faut procéder aux étapes suivantes&nbsp;:
    <ol>
      <li>Le nouvel animateur doit s'inscrire dans l'unité via le <a href='inscription.php'>formulaire d'inscription</a>.</li>
      <li>Le webmaster doit <a href='gestion.php?page=listing#nouvelles_inscriptions'>valider son inscription</a>.</li>
    </ol>
  </p>
  <h3>Modifier le listing des animateurs</h3>
  <p>Cette page présente un listing simplifié de tous les animateurs.
     Clique sur le bouton en vis-à-vis d'un animateur pour voir toutes ses données.
     Il y a également un lien pour voir le listing complet.</p>
  <p>Le bouton <span class='important'>Modifier</span> permet de modifier les données d'un animateur.</p>
  <p><span class='important'>Ce listing est très important et doit être complété entièrement</span>, car il sert pour le listing fédé.
     Les données (nom d'animateur, responsable, section, description, rôle, photo, GSM 1 et e-mail) servent aussi à compléter la page de présentation des animateurs.
     Complète donc <span class='important'>tous les champs</span>.
  <p>Pour ajouter ou modifier la photo de l'animateur, utilise le champ approprié.
  <h3>Changer un animé en animateur</h3>
  <p>Sélectionne un animé dans la liste, et encode son nouveau nom d'animateur (avec une majuscule).</p>
  <p>Ensuite, il faut <span class='important'>mettre à jour</span> ses données (adresse e-mail, description, photo, etc.), et lui octroyer des privilèges.</p>
  <p>Attention, cette opération ne peut pas être défaite.</p>
  <h3>Supprimer un animateur</h3>
  <p>Pour supprimer un animateur qui n'anime plus dans l'unité, il suffit de cliquer sur le bouton <span class='important'>Modifier</span>, et d'ensuite cliquer sur <span class='important'>Supprimer</span>.
     L'animateur supprimé restera dans les archives (sauf s'il a été ajouté et supprimé au cours d'une même année).
  <h3>Privilèges des animateurs</h3>
  <p>Chaque animateur a un certains nombre d'actions qu'il peut faire et qu'il ne peut pas faire sur le site.
     Il est possible d'octroyer ces privilèges un à un pour chacun des animateurs.</p>
  <p>Pour simplifier la gestion, il est possible d'appliquer un ensemble de privilèges prédéfinis via le menu déroulant au-dessus de l'animateur concerné.</p>
@endif

@if ($help == 'liens')
  <legend>Les liens @yield('back_to_top')</legend>
  <p>Il y a moyen de modifier la liste des liens hypertextes qui se trouvent à la page <a href='liens.php'>Liens utiles</a>.
     La liste de liens est commune à toute l'unité.
     Il est facilement possible d'ajouter, modifier et supprimer un lien.
  <p>Les champs à compléter sont:
     <ul>
       <li><span class='important'>Titre</span> : le nom du lien</li>
       <li><span class='important'>Adresse</span> : l'URL du site</li>
       <li><span class='important'>Description</span> : une description plus longue que le titre</li>
     </ul>
  </p>
@endif

@if ($help == 'tresorerie')
  <legend>Trésorerie @yield('back_to_top')</legend>
  Cet outil te permet de consulter et modifier les comptes financiers de ta section ou de l'unité.
  <p>Les comptes sont divisés en catégories, qui correspondent aux diverses activités de l'année (journée spéciale, camp, vente de calendriers, achat de matériel, etc.).  Pour chaque catégorie, un bilan total est calculé.</p>
  <p>Pour ajouter une ligne, clique sur le petit <span class='important'>"+"</span> à la fin de la catégorie ciblée, ou entre le nom d'une nouvelle catégorie.  Une nouvelle ligne vide, à la date d'aujourd'hui, apparait.  Tu peux la compléter :
    <ul>
      <li><span class='important'>Date</span> : la date de référence.
      <li><span class='important'>Motif</span> : la raison du flux d'argent.
      <li><span class='important'>Liquide et compte en banque</span> : la valeur (en euros) du flux.
      <li><span class='important'>Commentaire</span> : pour s'il y a des choses à préciser.
      <li><span class='important'>Reçus</span> : si tu utilises une numérotation des tickets de caisse, cette colonne est prévue pour l'y mettre.
    </ul>
  Clique ensuite sur <span class='important'>Enregistrer</span> pour enregistrer d'un coup toutes les modifications apportées aux différentes lignes.
  </p>
  <p>Tu peux supprimer une ligne en utilisant le petit <span class='important'>"-"</span> à la fin.</p>
  <p>Note que la première entrée de la trésorerie est remplie automatiquement : il s'agit de l'héritage de l'année précédente, calculé automatiquement (et mise à jour si des modifications sont apportées aux comptes de l'année précédente).</p>
  <p><span class='important'>Tout afficher</span> et <span class='important'>Tout cacher</span> te permettent de montrer ou cacher les catégorie pour y voir plus clair.  Chaque catégorie peut être cachée ou montrée individuellement.</p>
  <h3>Paiement des cotisations</h3>
  <p>Cet outil permet au trésorier d'unité de valider les paiements des cosations.
     La liste de gauche permet de valider les paiements, et la liste de droite permet d'annuler un paiement pour corriger une erreur.
     N'oublie pas d'enregistrer les modifications après avoir coché les cases.</p>
  <p>Tu peux réinitialiser la liste, par exemple au début d'une nouvelle année.</p>
@endif

@if ($help == 'parametres')
  <legend>Paramètres du site @yield('back_to_top')</legend>
  <p>Sur cette page, tu peux:
    <ul>
      <li>Modifier le prix des cotisations (désactives-en l'affichage pour les cacher des parents)</li>
      <li>Désactiver les inscriptions pour l'année suivante (n'oublie pas de les réactiver au moment opportun)</li>
      <li>Décider, pour chaque page du site, si elle est accessible ou non (si non, elle disparaitra du menu)</li>
    </ul>
  </p>
@endif

@if ($help == 'liste-membres')
<legend>Liste des membres @yield('back_to_top')</legend>
<p>Cette page affiche tous les membres inscrits sur le site, avec leur statut et leur dernière date de visite.
<p>Il est possible de supprimer un membre, mais pas d'en modifier les paramètres.
@endif

@if ($help == 'changements-recents')
  <legend>Changements récents @yield('back_to_top')</legend>
  <p>Cette page t'offre un aperçu des changements récemment effectués sur le site.
@endif

</div>