<?php
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
?>

<?php if (!isset($leader_corner_page)) $leader_corner_page = false; ?>

<div class="help-wrapper">
  <div class="help-toggle-button">
    <a class="help-badge" href=""></a>
  </div>
  <div class="well help-content" style="display: none;">

    <legend>Aide</legend>

@if ($help == 'edit-health-cards')
  <p>
    Sur cette page, tu peux accéder aux fiches santé de ta section.
    Tu peux rapidement voir quelles sont les fiches manquantes et les fiches expirant prochainement.
  </p>
  <p>
    Tu peux&nbsp;:
    <ul>
      <li>Télécharger <strong>chaque fiche individuellement</strong></li>
      <li>Télécharger <strong>toutes les fiches</strong> d'un seul coup</li>
      <li>Télécharger le <strong>résumé des fiches</strong>. Ce résumé t'indique de manière claire ce qu'il faut savoir pour chaque scout de ta section.</li>
    </ul>
  </p>
@endif

@if ($help == 'edit-calendar')
  <p>Cette page permet d'ajouter/modifier/supprimer des événements dans le calendrier.
    Pour modifier le calendrier d'une section, il faut sélectionner cette section dans le menu.</p>
  <h3>Ajouter un événement</h3>
  <ol>
    <li>Sélectionne dans le calendrier la date de début de l'événement</li>
    <li>
      Une fenêtre apparait pour l'ajout d'événement, complète les informations de l'événement&nbsp;:
      <ul>
        <li><strong>Durée</strong>&nbsp;: nombre de jours en comptant le premier et le dernier jour de l'activité (ex: vendredi à dimanche = 3 jours).</li>
        <li><strong>Activité</strong>&nbsp;: nom de l'activité, telle qu'elle apparaitra dans le calendrier</li>
        <li><strong>Description</strong>&nbsp;: détails de l'activité, indiques-y toutes les infos pratiques</li>
        <li>Le <strong>type d'événement</strong> déterminera l'icône ; les types <em>Animateurs</em> et <em>Nettoyage</em> ne seront visibles que par les animateurs</li>
        <li><strong>Section</strong>&nbsp;: vérifie que la bonne section est sélectionnée</li>
      </ul>
    </li>
    <li>Enregistre pour ajouter l'événement dans le calendrier
  </ol>
  <h3>Modifier un événement</h3>
  <p>Il suffit de cliquer sur l'événement dans le calendrier pour le modifier.</p>
  <h3>Supprimer un événement</h3>
  <p>Il est inutile de supprimer les événements qui se sont déjà déroulés.
    Pour supprimer un événement, clique sur l'événement dans le calendrier, puis clique sur supprimer.</p>
@endif

@if ($help == 'attendance')
  <p>Cette page permet de noter les présences des membres de la section aux activités du calendrier.</p>
  <h3>Ajouter une activité</h3>
  <p>Pour ajouter une activité, elle doit être présente dans le calendrier.</p>
  <p>Si une activité est présente dans le calendrier, elle apparait dans la liste "Ajouter une activité à la liste des présences". La <strong>sélectionner</strong> l'ajoutera immédiatement.</p>
  <p>On peut retirer une activité ajoutée par erreur en cliquant sur la croix à côté de la date</p>
  <h3>Les informations de la grille</h3>
  <p>Chaque ligne correspond à un membre de la section (les animateurs sont au bas de la liste).</p>
  <p>Chaque colonne correspond à une activité (seule la date est indiquée).</p>
  <p><strong>Cliquer sur une case</strong> change le status (absent &#8644; présent). Il est possible de noter tout le monde absent/présent d'un seul clic.</p>
  <p>Le total de présences et d'absences par membre est affiché. Le total par activité est également affiché en bas.</p>
  <p>S'il y a plus de 10 activités, seules les 10 dernières seront affichées. Il est alors possible de naviguer avec les flèches <strong>&lt;&lt;&lt;</strong> et <strong>&gt;&gt;&gt;</strong>.</p>
@endif

@if ($help == 'edit-photos')
  <h3>Créer un album</h3>
  <p>Tu peux créer un album via le bouton <strong><em>Créer un nouvel album</em></strong>. L'idéal est de créer un album par réunion ou activité.</p>
  <p>Une fois l'album créé, commence par lui donner un titre en cliquant sur <span class="glyphicon glyphicon-edit"></span>.</p>
  <p>Clique ensuite sur <strong><em>Modifier l'album</em></strong> pour lui ajouter des photos.</p>
  <h3>Opérations sur les répertoires</h3>
  <p>Tu peux <strong>réordonner</strong> les répertoires en les glissant.</p>
  <p>Tu peux <strong>supprimer</strong> un répertoire à condition qu'il soit vide.
    Pour le vider, clique sur <strong><em>Modifier l'album</em></strong> et supprime les photos qu'il contient.</p>
  <h3>Archiver</h3>
  <p>Tu peux <strong>archiver</strong> un album pour qu'il n'apparaisse plus dans les photos de l'année courante.</p>
  <p>Les albums sont automatiquement archivés après un an.</p>
@endif

@if ($help == 'edit-album')
  <h3>Ajouter des photos</h3>
  <p>Glisse des photos depuis ton ordinateur vers la zone appropriée ou clique sur cette zone pour
    sélectionner une ou plusieurs photos.</p>
  <p>Les photos au format <strong>jpeg</strong> et <strong>png</strong> sont acceptées.</p>
  <h3>Opérations sur les photos</h3>
  <p>Tu peux <strong>réordonner</strong> les photos en les glissant-déplaçant.</p>
  <p>Tu peux faire <strong>tourner</strong> une photo mal orientée.</p>
  <p>
    Tu peux également lui adjoindre une <strong>description</strong> qui apparaitra sous la photo.</p>
  <p><strong>Supprimer</strong> une photo l'enlève définitivement de l'album.</p>
@endif

@if ($help == 'edit-documents')
  <p>Cette page te permet d'ajouter, modifier et archiver des documents. Place ici tous les documents destinés aux parents ou aux scouts, en particulier les convocations envoyées aux parents.</p>
  <h3>Ajouter un document</h3>
  <p>
    Pour ajouter un document, il suffit de cliquer sur <strong><em>Ajouter un nouveau document</em></strong>.
  </p>
  <p>
    Un formulaire apparait. Remplis-le avant de l'enregistrer&nbsp;:
  </p>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Titre</strong> </div>
    <div class="col-md-10">C'est le nom qui sera affiché dans la liste</div>
  </div>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Description</strong> </div>
    <div class="col-md-10">La description sera affichée en-dessous</div>
  </div>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Document</strong> </div>
    <div class="col-md-10">Sélectionne le fichier à télécharger</div>
  </div>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Nom du fichier</strong> </div>
    <div class="col-md-10">Nom qu'aura le fichier quand on le téléchargera. Pour garder le nom d'origine, laisse-le vide.</div>
  </div>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Catégorie</strong> </div>
    <div class="col-md-10">Les documents sont triés par catégorie, le choix de la catégorie est limité</div>
  </div>
  <div class="row">
    <div class="col-md-2 text-right"><strong>Public</strong> </div>
    <div class="col-md-10">
      <p>
        Si cette case est cochée, le document est public, donc visible par tous les internautes et pas juste les membres
      </p>
      <div class="alert alert-danger">
        <div>
          Il est conseillé de garder les documents <strong>privés</strong>.
        </div>
        <div>
          En effet, ils contiennent souvent des informations qui ne devraient pas tomber dans n'importe quelles mains.
        </div>
      </div>
    </div>
  </div>
  <h3>Modifier un document</h3>
  <p>
    Un document peut également être modifié ou remplacé.
    Pour remplacer un document, modifie-le et sélectionne un nouveau fichier.</p>
  <p>
  <h3>Supprimer et archiver</h3>
  <p>
    Pour supprimer un document de moins d'une semaine, clique sur <strong><em>Modifier</em></strong> puis <strong><em>Supprimer</em></strong>.
    Il n'est pas possible se supprimer des documents qui sont en ligne depuis plus d'une semaine.
  </p>
  <p>
    Les documents obsolètes peuvent être archivés, et seront donc toujours disponibles. Les documents sont automatiquement archivés après un an.
  </p>
  <p>
     Si un document doit absolument être supprimé car son contenu n'est pas adéquat,
     contacte le <a href="{{ URL::route('personal_email', array('type' => 'webmaster', 'member_id' => 0)) }}">webmaster du site</a>.
  </p>
@endif

@if ($help == 'edit-news')
  <p>
    Indique dans les actualités tout ce que tu veux communiquer
    (réunion spéciale qui s'est bien déroulée, produit en vente par la section, etc.)
  </p>
  <h3>Ajouter une nouvelle</h3>
  <p>
    Pour créer une nouvelle nouvelle, clique sur <strong><em>Ajouter une nouvelle</em></strong>.
  </p>
  <p>
    La date d'une nouvelle est la date à laquelle la nouvelle est écrite.
    Cette date figurera dans la liste des nouvelles.
    Les nouvelles de plus d'un an ne sont plus affichées.
  </p>
  <h3>Modifier et supprimer</h3>
  <p>
    Il est également possible de <strong>modifier</strong> une nouvelle.
    La date de la nouvelle restera la date à laquelle elle a été créée.
  </p>
  <p>
    Pour <strong>supprimer</strong> une nouvelle, clique sur <strong><em>Modifier</em></strong> puis
    sur <strong><em>Supprimer</em></strong>. Il est préférable de ne jamais supprimer les nouvelles.
  </p>
@endif

@if ($help == 'email-section')
  <p>Cet outil te permet d'envoyer un e-mail à tous les parents d'une section.
     L'adresse de chacun restera confidentielle, ceci permet d'éviter le spamming et l'abu d'envoi d'e-mails de la part des parents.
  <ol>
    <li>Entre le sujet de ton message (une en-tête est déjà encodée, libre à toi de la garder).</li>
    <li>Tape le message</li>
    <li>Vérifie le nom et l'adresse de l'expéditeur</li>
    <li>Tu peux joindre des documents</li>
    <li>Vérifie les destinataires (décoche ceux à qui tu ne veux pas envoyer l'e-mail).
      Tu peux cocher/décocher tous les membres d'une catégorie d'un seul clic.
      <br />
      Tu peux également ajouter des destinataires supplémentaires. Encode-les séparés par des virgules.</li>
  </ol>
  <p>
    Note&nbsp;: l'e-mail sera pas envoyé instantanément, l'envoi sera réparti dans les minutes ou les heures suivantes.
    Il sera envoyé a l'adresse de l'expéditeur en dernier. À ce moment, tu sauras que tout le monde l'a reçu.
  </p>
  <p>&nbsp;</p>
  <div class="alert alert-danger">
    <div>Les e-mails envoyés depuis cette page seront répertoriés sur le site et seront visibles par tous les membres de l'unité.</div>
    <div><strong>N'utilise pas cet outil pour envoyer des e-mails personnels.</strong></div>
  </div>
@endif

@if ($help == 'edit-emails')
  <p>
    Sur cette page, tu peux voir tous les e-mails envoyés aux parents depuis le site.
  </p>
  <p>
    Tu peux voir la liste d'adresses e-mail auxquelles chaque e-mail a été envoyé.
  </p>
  <p>
    Tu peux <strong>supprimer</strong> un e-mail dans un délai de 7 jours après l'envoi.
    Ensuite, il n'est plus possible de supprimer un e-mail, mais les e-mails peuvent toujours être archivés.
    Le but de ceci est de garder une trace des e-mails envoyés.
  </p>
@endif

@if ($help == 'edit-new-registrations')
  <p>Cet outil permet d'inscrire des nouveaux scouts ou animateurs ayant rempli le formulaire d'inscription. <em>Utilise les onglets pour voir les autres fonctionnalités.</em></p>
  <h3>Validation d'une nouvelle inscription</h3>
  <p>Dans la liste, clique sur <strong><em>Inscrire</em></strong> à côté du nom du scout à inscrire.
     Un formulaire apparaît, avec les informations entrées par les parents pré-encodées.
     Vérifie les données attentivement avant d'inscrire définitivement le scout, en particulier la section dans laquelle il s'inscrit.
  </p>
  <p>Une demande d'inscription erronée ou non acceptée peut être supprimée.</p>
@endif

@if ($help == 'edit-reregistrations')
  <p>Cet outil permet de gérer les réinscriptions. <em>Utilise les onglets pour voir les autres fonctionnalités.</em></p>
  <h3>Réinscriptions</h3>
  <p> Utiliser cette page n'est pas indispensable pour
    le bon déroulement des réinscriptions, mais elle permet d'avoir une vue sur les scouts qui se réinscrivent
    et ceux qui n'ont pas encore décidé ou quittent l'unité.
  </p>
  <p>Les parents peuvent eux-même réinscrire leurs enfants via la page d'inscription s'ils sont connectés sur le site. Cela peut être une manière de gérer les réinscriptions de l'unité.</p>
  <p>Sur cette page, tu peux le marquer les scouts comme réinscrits, les désinscrire <strong>(attention&nbsp;: effet immédiat)</strong>, ou annuler
  leur réinscription.</p>
  <p>Le statut de réinscription est remis à zéro le <strong>1er janvier</strong>. Il est donc possible de gérer les réinscriptions pour l'année suivante à partir du 1er janvier.</p>
@endif

@if ($help == 'edit-year-in-section')
  <p>Cet outil permet d'augmenter d'une année une liste de scouts. <em>Utilise les onglets pour voir les autres fonctionnalités.</em></p>
  <h3>Augmentation de l'année des scouts</h3>
  <p>
     Tu peux augmenter l'année de tous les scouts d'un coup, ou régler l'année de chacun indépendemment.
     Les scouts apparaissent par ordre inverse d'année.
  </p>
  <p><strong>Attention</strong> à ne pas sélectionner les scouts qui viennent de monter dans la section et dont l'année est à 1.
     Il vaut mieux faire l'opération de changement d'année avant l'opération de changement de section.
  </p>
@endif

@if ($help == 'edit-member-section')
  <p>
    Cet outil te permet de faire évoluer des scouts d'une section à une autre.
    <em>Utilise les onglets pour voir les autres fonctionnalités.</em>
  </p>
  <h3>Passage d'une section à une autre</h3>
  <p>
    Premièrement, sélectionne la section de destination.
  </p>
  <p>
    Sélectionne les scouts que tu veux faire passer en cliquant sur <strong><em>Faire passer</em></strong> (ils sont classés par année, les scouts devant monter
    d'une section sont donc normalement en haut de la liste).
    Le passage n'est fait que lorsque tu cliques sur <strong><em>Enregistrer les transferts</em></strong>.
    Cela change les scouts sélectionnés de section, met leur année à 1 et supprime leur nom de patrouille/sizaine/hutte.
  </p>
  <p>
    Si une fausse manœuvre a été effectuée, tu peux refaire l'opération dans l'autre sens (l'année restera cependant à 1), ou directement faire le changement dans le listing.
  </p>
@endif

@if ($help == 'edit-listing')
  <p>
    Le listing du site web est le listing officiel de l'unité.
    Il est disponible en ligne à tous les membres <em>(*)</em>, et est utilisé pour mettre
    à jour le listing de la fédération.
    <strong>Il est donc primordial qu'il soit bien entretenu.</strong>
  </p>
  <p>Cette page permet de modifier le listing et de l'exporter.</p>
  <p><em>(*) Seuls les animateurs ont accès aux données confidentielles.</em></p>
  <h3>Modifier</h3>
  <p>Pour modifier une ligne du listing, il suffit de cliquer sur le bouton <strong><em>Modifier</em></strong> en face d'un membre.
     Un formulaire avec les données du scout apparait. Fais les modifications nécessaires et enregistre les changements.
  </p>
  <p>
    Il est possible de <strong>supprimer</strong> un scout du listing. Attention à ne pas commettre d'erreur,
    la suppression est immédiate et définitive.
  </p>
  <h3>Exporter le listing</h3>
  <p>
    Tu peux exporter le listing au format <strong>PDF</strong>, <strong>Excel</strong> ou <strong>CSV</strong>.
    Le listing simple contient uniquement quelques données, le listing complet contient toutes les données.
  </p>
  <p>
    Il est également possible de sortir les adresses imprimables sur des enveloppes.
  </p>
@endif

@if ($help == 'edit-page')
  <p>
    Cet outil te permet de modifier la page {{ $page_title ? "<strong>" . $page_title . "</strong>" : " d'accueil" }}.
  </p>
  <h3>Modification de la page</h3>
  <p>
    Pour modifier la page, il suffit de remplacer son contenu dans la zone d'édition. Tu peux utiliser les outils d'édition pour mettre en page ton texte.
  </p>
  <p>
    Termine en cliquant sur <strong><em>Enregistrer</em></strong>, les changements seront directement appliqués.
  </p>
  <h3>Images dans la page</h3>
  <p>
    Il est possible d'ajouter des photos ou des images dans la page.
  </p>
  <p>
    Cliquer sur <strong><em>Ajouter</em></strong> pour ajouter une image à la librairie d'images.<p>
  <p>
    Positionne ton curseur dans le texte à l'endroit où tu veux insérer l'image, puis clique sur l'icône de l'image pour l'insérer.
  </p>
  <h3>Pour les experts</h3>
  <p>Il est possible de modifier le code html de la page en cliquant sur <strong><em>source</em></strong>. L'ensemble des balises permises est cependant limité.</p>
@endif

@if ($help == 'sections')
  <h3>Ajouter une section</h3>
  <p>Lorsqu'une nouvelle section est créée, tu peux l'ajouter ici. Encode les informations suivantes&nbsp;:
    <ul>
      <li><strong>Nom</strong> : nom de la section (ex.: Waingunga, Louveteaux, Meute, Éclaireurs, Troupe, Pionniers...)</li>
      <li><strong>E-mail</strong> : l'adresse e-mail de la section (pour l'envoi de courriers électroniques et la page de contacts)</li>
      <li><strong>Sigle</strong> : le code donné par la fédération à la section, utilisé notamment pour déterminer le type d'onglet à afficher (ex.: B 1, L 2, E 3, P 1)</li>
      <li><strong>Couleur</strong> : la couleur utilisée pour le calendrier</li>
      <li><strong>"la section"</strong> : utilisé pour remplir certains textes du site (ex.: la meute Waingunga)</li>
      <li><strong>"de la section"</strong> : utlisé pour remplir certains textes du site (ex.: de la Waingunga)</li>
      <li><strong>Sous-groupes</strong> : le nom des petits groupes de la section, au singulier (ex.: Hutte, Patrouille, Sizaine)</li>
    </ul>
  </p>
  <h3>Modifier une section</h3>
  <p>Si tu as le droit de modifier une section, tu verras un bouton <strong>Modifier</strong> sur sa ligne. Tu peux changer toutes les informations de la section.</p>
  <h3>Supprimer une section</h3>
  <p>
    Tu peux supprimer une section si elle n'existe plus.
    Attention, cette opération ne peut être défaite.
    Il vaut parfois mieux garder la section et indiquer dans sa page d'accueil qu'elle n'existe plus.
  </p>
  <h3>Réordonner les sections</h3>
  <p>
    Tu peux changer l'ordre dans lequel les sections apparaissent dans les onglets et dans les listes.
    Il suffit de cliquer-déplacer la ligne d'une section dans la liste.
    Il est recommandé de classer les sections par ordre d'âge.
  </p>
@endif

@if ($help == 'edit-leaders')
  <p>Le listing des animateurs sert à deux choses&nbsp;:
    <ul>
      <li>Mettre des informations publiques sur la page dédiée aux animateurs.</li>
      <li>Il sert de listing officiel pour la fédération.</li>
    </ul>
  Il est donc important que les informations qui s'y trouvent soient correctes et complètes.
  <h3>Inscrire un nouvel animateur</h3>
  <p>Lorsqu'un nouvel animateur entre dans l'unité, il faut procéder aux étapes suivantes&nbsp;:
    <ol>
      <li>Le nouvel animateur doit s'inscrire dans l'unité via le <a href="{{ URL::route('registration_form') }}">formulaire d'inscription</a>.</li>
      <li>L'animateur d'unité ou le responsable des inscriptions doit <a href='{{ URL::route('manage_registration') }}'>valider son inscription</a>.</li>
    </ol>
  </p>
  <h3>Voir ou modifier les données d'un animateur</h3>
  <p>Clique sur le bouton en vis-à-vis d'un animateur pour voir toutes ses données, et éventuellement les modifier si tu en as le droit.</p>
  <p>Pour ajouter ou modifier la photo de l'animateur, utilise le champ approprié.
  <h3>Changer un scout en animateur</h3>
  <p>Sélectionne un scout dans la liste, le formulaire pour l'inscrire en tant qu'animateur apparaitra. Complète-le et termine en cliquant sur <strong><em>Enregistrer</em></strong></p>
  <p>Attention, cette opération ne peut pas être défaite.</p>
  <h3>Supprimer un animateur</h3>
  <p>Pour supprimer un animateur qui n'anime plus dans l'unité, il suffit de cliquer sur le bouton <strong><em>Supprimer</em></strong> devant son nom.
     L'animateur supprimé restera dans les archives (sauf s'il a été ajouté puis supprimé au cours d'une même année).
@endif

@if ($help == 'edit-privileges')
  <h3>Privilèges des animateurs</h3>
  <p>Chaque animateur a un certains nombre d'actions qu'il peut faire et qu'il ne peut pas faire sur le site.
     Il est possible d'octroyer ces privilèges un à un pour chacun des animateurs.</p>
  <p>
    Il est conseillé de donner les privilèges suivants aux animateurs&nbsp;:
    <ul>
      <li><strong>Animateur</strong>&nbsp;: Gestion de base + privilèges en fonction des besoins spécifique (photos, documents, comptes, etc.)</li>
      <li><strong>Animateur responsable ou webmaster de section</strong>&nbsp;: Gestion de base + Gestion avancée</li>
      <li><strong>Équipier d'unité</strong>&nbsp;: Gestion de base + Gestion avancée (+ Gestion de l'unité)</li>
      <li><strong>Animateur d'unité</strong>&nbsp;: Tous les privilèges</li>
    </ul>
  </p>
@endif

@if ($help == 'edit-links')
  <p>Tu peux modifier la liste des liens qui se trouvent sur la page de <a href="{{ URL::route('contacts') }}">contacts</a>.
     La liste de liens est commune à toute l'unité.
  </p>
  <p>Tu peux <strong>ajouter</strong> un nouveau lien, <strong>modifier</strong> un lien existant ou <strong>supprimer</strong> un lien.
  <p>Les champs à compléter sont:
     <ul>
       <li><strong>Nom du lien</strong>&nbsp;: titre du lien</li>
       <li><strong>URL de la page</strong>&nbsp;: l'adresse du site pointé par le lien</li>
       <li><strong>Description</strong>&nbsp;: une description plus longue que le titre</li>
     </ul>
  </p>
@endif

@if ($help == 'accounting')
  <p>
    Cet outil te permet de consulter et modifier les comptes financiers de ta section ou de l'unité.
  </p>
  <h3>Catégories</h3>
  <p>
    Les comptes sont divisés en catégories, qui correspondent aux diverses activités de l'année (journée spéciale, camp, vente de calendriers, achat de matériel, etc.).
    Pour chaque catégorie, un bilan total est calculé.
  </p>
  <p>
    Clique sur <strong><em>Ajouter une catégorie</em></strong> en bas de la page pour créer une nouvelle catégorie. Tu peux éditer le title de la catogérie en cliquant dessus.
  </p>
  <h3>Transactions</h3>
  <p>Chaque transaction correspond à un paiement ou un montant reçu.</p>
  <p>Pour ajouter une transaction, clique sur <strong><em>Ajouter une transaction</em></strong> sous la catégorie correspondante.</p>
  <p>Pour chaque transaction, voici les données à compléter</p>
    <ul>
      <li><strong>Date</strong> : la date de référence (au format JJ/MM/AAAA)</li>
      <li><strong>Motif</strong> : la raison de la transaction (ex.: achat nourriture camp)</li>
      <li><strong>Liquide et compte en banque</strong> : la valeur en euros de l'argent payé ou reçu. Veille à utiliser la bonne colonne, selon qu'il s'agit d'un paiement en liquide ou sur compte.</li>
      <li><strong>Commentaire</strong> : ajoute des informations supplémentaires si nécessaire
      <li><strong>Reçus</strong> : si tu utilises une numérotation des tickets de caisse, mets le(s) numéro(s) ici
    </ul>
  </p>
  <p>Tu peux supprimer une ligne en cliquant sur la <span class="glyphicon glyphicon-remove"></span>.</p>
  <h3>Complément d'information</h3>
  <p>Les changements sont enregistrés en temps réel. Attention, si deux personnes modifient les comptes en même temps, les changements de l'une d'entre elles ne seront pas enregistrés.</p>
  <p>La première entrée de la trésorerie est remplie automatiquement&nbsp;: il s'agit de l'héritage de l'année précédente, calculé automatiquement (et mis à jour si des modifications sont apportées aux comptes de l'année précédente). Il n'est pas possible de la modifier manuellement.</p>
@endif

@if ($help == 'parameters')
  <p>Cette page permet de paramétriser le site. Tu peux&nbsp;:
    <ul>
      <li>Modifier le prix des cotisations. Ces valeurs remplaceront automatiquement les occurences de
        "(PRIX UN ENFANT)", "(PRIX UN ANIMATEUR)", "(PRIX DEUX ENFANTS)", "(PRIX DEUX ANIMATEURS)", "(PRIX TROIS ENFANTS)" et "(PRIX TROIS ANIMATEURS)" dans
        le texte de la page d'inscription.
      </li>
      <li>
        Désactiver les inscriptions dans l'unité (n'oublie pas de les réactiver au moment opportun).
      </li>
      <li>
        Décider, pour chaque page du site, si elle est accessible ou non (si non, elle disparaitra du menu). <br />
        Le fait que le calendrier soit téléchargeable en PDF est également une option.
      </li>
      <li>
        Modifier la liste des catégories de documents à télécharger.
        Il y a toujours une catégorie "Divers".
        Si la catégorie s'appelle "Pour les scouts", le nom de la catégorie sera remplacée par "Pour les baladins",
        "Pour les louveteaux", "Pour les éclaireurs" ou "Pour les pionniers" selon la section.
      </li>
      <li>
        Modifier les données de l'unité&nbsp;:
        <ul>
          <li><strong>Nom de l'unité</strong>&nbsp;: Le nom complet de l'unité</li>
          <li><strong>Sigle de l'unité</strong>&nbsp;: Le nom court de l'unité (p.ex. SV001)</li>
          <li><strong>N° de compte</strong>&nbsp;: Le numéro de compte en banque de l'unité pour les paiements de cotisation</li>
          <li><strong>Logo du site</strong>&nbsp;: Le logo qui apparaitra en haut du site</li>
          <li><strong>Logo sur deux lignes</strong>&nbsp;: Le logo peut apparaitre en petit à côté du titre (NON), ou en plus grand sur les deux lignes du menu (OUI) </li>
        </ul>
      </li>
      <li>
        Modifier les paramètres des moteurs de recherche. Ces paramètres permettent d'optimiser l'apparition du site dans les résultats des moteurs de recherche&nbsp;:
        <ul>
          <li><strong>Description</strong>&nbsp;: Cette description apparait directement dans les résultats des moteurs de recherche</li>
          <li><strong>Mots-clés</strong>&nbsp;: Si ces mots-clés sont recherchés via un moteur de recherche, la chance que ce site apparaisse haut dans les résultats est plus élevée.
            Tu peux y mettre le nom de l'unité, les noms des groupes, le nom de la commune, du village et du quartier où es situe l'unité, les mots-clés propres au scoutisme, etc.
          </li>
        </ul>
      </li>
      <li>
        Pour les utilisateurs avertis, il est possible de rajouter du contenu html sur toutes les pages du sites, avant la fermeture de la balise &lt;head&gt;.
        Cela permet par exemple d'insérer le code de google analytics.
      </li>
      <li>
        Modifier l'adresse e-mail du webmaster et la configuration de l'envoi d'e-mails&nbsp;:
        <ul>
          <li><strong>Adresse e-mail du webmaster</strong></li>
          <li><strong>Adresse e-mail du site</strong>&nbsp;: Adresse e-mail depuis laquelle partiront les e-mails envoyés depuis le site
            (à l'exception des adresses vérifiées (voir plus bas))</li>
          <li><strong>Configuration SMTP</strong>&nbsp;: Paramètres SMTP de l'envoi des e-mails (compatible avec <a href="http://aws.amazon.com/fr/ses/">AWS SES</a>)</li>
          <li>
            <strong>Adresses e-mail vérifiées</strong>&nbsp;: Les e-mails envoyés depuis ces adresses garderont leur champ "from" original.
            Les autres e-mails seront envoyés depuis l'adresse e-mail du site, et auront leur expéditeur original dans le champ "reply-to".
            Il est conseillé de s'arranger pour que les adresses e-mail des sections et de l'unité soient vérifiées.
          </li>
        </ul>
      </li>
    </ul>
  </p>
@endif

@if ($help == 'user-list')
  <p>Cette page affiche tous les membres inscrits sur le site, avec leur statut et leur dernière date de visite.
  <p>Il est possible de supprimer un membre, mais pas d'en modifier les paramètres.
@endif

@if ($help == 'suggestions')
  <p>Les visiteurs peuvent laisser des suggestions pour le site ou la vie de l'unité.</p>
  <p>Cette page permet de&nbsp;:
    <ul>
      <li>Répondre à une suggestion (ou en changer la réponse)</li>
      <li>Supprimer une suggestion inadéquate</li>
    </ul>
  </p>
  <p>Attention&nbsp;: toutes les suggestions et leurs réponses sont publiques.</p>
@endif

@if ($help == 'guest-book')
  <p>
    Les visiteurs du site peuvent laisser des messages publics dans le livre d'or.
  </p>
  <p>
    Cette page permet de supprimer un message inadéquat.
  </p>
@endif

@if ($help == 'monitoring')
  <p>
    Cette page liste les tâches cron du site, avec leur statut.
  </p>
  <p>
    Si une tâche n'a pas été exécutée depuis trop longtemps, un message d'erreur est affiché et le problème doit être solutionné par le webmaster.
  </p>
@endif

@if ($help == 'logs')
  <p>
    Cette page montre toutes les opérations qui ont été effectuées sur le site&nbsp;:
    <ul>
      <li><strong>#</strong>&nbsp;: Le numéro du log, par ordre décroissant</li>
      <li><strong>Date</strong>&nbsp;: La date et l'heure à laquelle l'action s'est déroulée</li>
      <li><strong>Utilisateur</strong>&nbsp;: L'utilisateur qui a commis l'action ("Visiteur" is aucun utilisateur n'était connecté)</li>
      <li><strong>Catégorie</strong>&nbsp;: La catégorie de l'action</li>
      <li><strong>Action</strong>&nbsp;: La description de l'action</li>
      <li><strong>Section</strong>&nbsp;: La section qui était sélectionnée au moment de l'action</li>
    </ul>
  </p>
  <p>Clique sur une action pour en voir les <strong>détails</strong>.</p>
  <p>Utilise les <strong>filtres</strong> pour rechercher des actions précises.</p>
@endif

</div>
</div>