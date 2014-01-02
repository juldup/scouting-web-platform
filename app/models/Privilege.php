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
  
  public static $EDIT_PAGES = array(
      'id' => 'Edit pages',
      'text' => 'Modifier les pages #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_SECTION_EMAIL_ADDRESS = array(
      'id' => "Edit section e-mail address",
      'text' => "Changer l'adresse e-mail #delasection",
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_CALENDAR = array(
      'id' => "Edit calendar",
      'text' => 'Modifier les entrées du calendrier #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_NEWS = array(
      'id' => "Edit news",
      'text' => 'Poster des nouvelles pour #lasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_DOCUMENTS = array(
      'id' => "Edit documents",
      'text' => 'Modifier les documents #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $SEND_EMAILS = array(
      'id' => "Send e-mails",
      'text' => 'Envoyer des e-mails aux membres #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $POST_PHOTOS = array(
      'id' => "Post photos",
      'text' => 'Ajouter/supprimer des photos pour #lasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $MANAGE_ACCOUNTS = array(
      'id' => "Manage accounts",
      'text' => 'Gérer les comptes #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_LISTING_LIMITED = array(
      'id' => "Edit listing limited",
      'text' => 'Modifier certaines données du listing #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_LISTING_ALL = array(
      'id' => "Edit listing all",
      'text' => 'Modifier les données sensibles du listing #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $VIEW_HEALTH_CARDS = array(
      'id' => "View health cards",
      'text' => 'Consulter les fiches santé #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $DELETE_HEALTH_CARDS = array(
      'id' => "Delete health cards",
      'text' => 'Supprimer des fiches santé #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_LEADER_PRIVILEGES = array(
      'id' => "Edit leader privileges",
      'text' => 'Changer les privilèges des animateurs #delasection',
      'unit' => true,
      'section' => true,
  );
  
  public static $UPDATE_PAYMENT_STATUS = array(
      'id' => "Update payment status",
      'text' => 'Modifier le statut de paiement',
      'unit' => true,
      'section' => true,
  );
  
  public static $MANAGE_ANNUAL_FEAST_REGISTRATION = array(
      'id' => "Manage annual feast registration",
      'text' => "Valider les inscriptions pour la fête d'unité",
      'unit' => true,
      'section' => true,
  );
  
  public static $MANAGE_SUGGESIONS = array(
      'id' => "Manage suggestions",
      'text' => "Répondre aux suggestions et les supprimer",
      'unit' => true,
      'section' => true,
  );
  
  public static $EDIT_GLOBAL_PARAMETERS = array(
      'id' => "Edit global parameters",
      'text' => "Changer les paramètres du site",
      'unit' => true,
      'section' => true,
  );
  
  public static $MANAGE_SECTIONS = array(
      'id' => "Manage sections",
      'text' => "Créer, modifier et supprimer les sections",
      'unit' => true,
      'section' => true,
  );
  
  public static $DELETE_USERS = array(
      'id' => "Delete users",
      'text' => "Supprimer des comptes d'utilisateurs",
      'unit' => true,
      'section' => true,
  );
  
  public static $DELETE_GUEST_BOOK_ENTRIES = array(
      'id' => "Delete guest book entries",
      'text' => "Supprimer des entrées du livre d'or",
      'unit' => true,
      'section' => true,
  );


  /*
        "U:Accéder au coin des animateurs", => everybody
        "S:Voir le listing complet #delasection", => everybody
        "S:Voir l'état des comptes #delasection", => everybody
        "S:Voir les changements du site concernant #lasection"), => everybody
        "S:Changer des scouts de ou vers #lasection", => listing_all
        "S:Augmenter l'année des scouts #delasection", => listing_limited
        "S:Valider les inscriptions #delasection", => listing_all
        "S:Changer un scout en animateur #delasection") => listing_all
        "U:Voir les documents partagés entre animateurs #delasection", => feature no longer supported
        "U:Gérer le covoiturage #delasection"), => feature no longer supported
        "U:Exporter le listing d'unité pour la fédération"), => feature maybe no longer supported
        "U:Changer les codes des sections"), => manage sections
        "U:Modifier les couleurs des sections", => manage sections
        "U:Voir les logs") => feature no longer supported
    );
*/
  
}