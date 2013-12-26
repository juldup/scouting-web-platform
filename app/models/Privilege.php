<?php

class Privilege extends Eloquent {
  
  public static $UPDATE_OWN_LISTING_ENTRY = array(
      'id' => 'Update own listing entry',
      'text' => 'Modifier ses données personnelles dans le listing',
      'unit' => true,
      'section' => false,
  );
  
  public static $VIEW_PRIVATE_SUGGESTIONS = array(
      'id' => 'View private suggestions',
      'text' => 'Voir les suggestions privées',
      'unit' => true,
      'section' => false,
  );
  
  public static $VIEW_MEMBER_LIST = array(
      'id' => 'View member list',
      'text' => 'Voir la liste des membres',
      'unit' => true,
      'section' => false,
  );
  
  // TODO ...
  
  public static $EDIT_PAGES = array(
      'id' => 'Edit pages',
      'text' => 'Modifier les pages #delasection',
      'unit' => true,
      'section' => true,
  );
  
  
  
     /*     
  $operationsBase = Array(
    "Privilèges de base des animateurs" =>
      Array(
        "U:Accéder au coin des animateurs",
        "U:",
        "U:",
        "U:")
    );
  $operations = Array(
    "Voir les données" =>
      Array(
        "S:Voir le listing complet #delasection",
        "S:Consulter les fiches santé #delasection",
        "S:Voir l'état des comptes #delasection",
        "S:Voir les changements du site concernant #lasection"),
    "Calendrier" =>
      Array(
        "S:Modifier les entrées du calendrier #delasection"),
    "Photos" =>
      Array(
        "S:Ajouter/supprimer des photos pour #lasection"),
    "Documents" =>
      Array(
        "S:Modifier les documents #delasection"),
    "Covoiturage" =>
      Array(
        "S:Gérer le covoiturage #delasection"),
    "Infos de la section" =>
      Array(
        "S:Modifier les pages #delasection",
        "S:Changer l'adresse e-mail #delasection",
        "S:Poster des nouvelles pour #lasection"),
    "E-mails" =>
      Array(
        "S:Envoyer des e-mails aux membres #delasection"),
    "Trésorerie" =>
      Array(
        "S:Gérer les comptes #delasection"),
    "Animateurs" =>
      Array(
        "S:Modifier les animateurs #delasection",
        "S:Changer les privilèges des animateurs #delasection"),
    "Fiches santé" =>
      Array(
        "S:Supprimer des fiches santé #delasection"),
    "Listing" =>
      Array(
        "S:Modifier certaines données du listing #delasection",
        "S:Modifier les données sensibles du listing #delasection",
        "S:Changer des scouts de ou vers #lasection",
        "S:Augmenter l'année des scouts #delasection",
        "S:Valider les inscriptions #delasection",
        "S:Changer un scout en animateur #delasection")
    );
  
  $operationsUnite = Array(
    "Privilèges de base des animateurs" =>
      Array(
        "U:Voir le listing complet #delasection",
        "U:Voir les documents partagés entre animateurs #delasection",
        "U:Consulter les fiches santé #delasection",
        "U:Voir l'état des comptes #delasection",
        "U:Voir les changements du site concernant #lasection"),
    "Calendrier" =>
      Array(
        "U:Modifier les entrées du calendrier #delasection"),
    "Photos" =>
      Array(
        "U:Ajouter/supprimer des photos pour #lasection"),
    "Documents" =>
      Array(
        "U:Modifier les documents #delasection"),
    "Covoiturage" =>
      Array(
        "U:Gérer le covoiturage #delasection"),
    "Infos du site" =>
      Array(
        "U:Modifier les pages #delasection",
        "U:Changer l'adresse e-mail #delasection",
        "U:Poster des nouvelles pour #lasection"),
    "E-mails" =>
      Array(
        "U:Envoyer des e-mails aux membres #delasection"),
    "Trésorerie" =>
      Array(
        "U:Gérer les comptes #delasection",
        "U:Modifier le statut de paiement"),
    "Animateurs" =>
      Array(
        "U:Modifier les animateurs #delasection",
        "U:Changer les privilèges des animateurs #delasection"),
    "Fiches santé" =>
      Array(
        "U:Supprimer des fiches santé #delasection"),
    "Listing" =>
      Array(
        "U:Modifier certaines données du listing #delasection",
        "U:Modifier les données sensibles du listing #delasection",
        "U:Changer des scouts de ou vers #lasection",
        "U:Augmenter l'année des scouts #delasection",
        "U:Valider les inscriptions #delasection",
        "U:Changer un scout en animateur #delasection",
        "U:Exporter le listing d'unité pour la fédération"),
    "Fête d'unité" =>
      Array(
        "U:Valider les inscriptions pour la fête d'unité"),
    "Opérations avancées" =>
      Array(
        "U:Supprimer des documents partagés entre animateurs #delasection",
        "U:Supprimer des suggestions",
        "U:Répondre aux suggestions",
        "U:Changer les paramètres du site",
        "U:Créer et renommer des sections",
        "U:Changer les codes des sections"),
    "Gestion du site avancée (Webmaster)" =>
      Array(
        "U:Modifier les couleurs des sections",
        "U:Supprimer des comptes d'utilisateurs",
        "U:Supprimer des entrées du livre d'or",
        "U:Voir les logs")
    );
*/
  
}