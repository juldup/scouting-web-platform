<?php

/**
 * 
 * 
 * Note : predefined privileges are privileges that can be automatically applied to classes
 * of members :
 *   A: Simple leader
 *   R: Leader in charge / Section webmaster
 *   S: Unit co-leader
 *   U: Unit main leader
 */
class Privilege extends Eloquent {
  
  public static $UPDATE_OWN_LISTING_ENTRY = array(
      'id' => 'Update own listing entry',
      'text' => 'Modifier ses données personnelles dans le listing',
      'section' => false,
      'predefined' => "ARSU"
  );
  
  public static $VIEW_PRIVATE_SUGGESTIONS = array(
      'id' => 'View private suggestions',
      'text' => 'Voir les suggestions privées',
      'section' => false,
      'predefined' => "ARSU"
  );
  
  public static $VIEW_MEMBER_LIST = array(
      'id' => 'View member list',
      'text' => 'Voir la liste des membres',
      'section' => false,
      'predefined' => "ARSU"
  );
  
  public static $EDIT_PAGES = array(
      'id' => 'Edit pages',
      'text' => 'Modifier les pages #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_SECTION_EMAIL_ADDRESS = array(
      'id' => "Edit section e-mail address",
      'text' => "Changer l'adresse e-mail #delasection",
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_CALENDAR = array(
      'id' => "Edit calendar",
      'text' => 'Modifier les entrées du calendrier #delasection',
      'section' => true,
      'predefined' => "ARSU"
  );
  
  public static $EDIT_NEWS = array(
      'id' => "Edit news",
      'text' => 'Poster des nouvelles pour #lasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_DOCUMENTS = array(
      'id' => "Edit documents",
      'text' => 'Modifier les documents #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $SEND_EMAILS = array(
      'id' => "Send e-mails",
      'text' => 'Envoyer des e-mails aux membres #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $POST_PHOTOS = array(
      'id' => "Post photos",
      'text' => 'Ajouter/supprimer des photos pour #lasection',
      'section' => true,
      'predefined' => "ARSU"
  );
  
  public static $MANAGE_ACCOUNTS = array(
      'id' => "Manage accounts",
      'text' => 'Gérer les comptes #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_LISTING_LIMITED = array(
      'id' => "Edit listing limited",
      'text' => 'Modifier certaines données du listing #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_LISTING_ALL = array(
      'id' => "Edit listing all",
      'text' => 'Modifier les données sensibles du listing #delasection',
      'section' => true,
      'predefined' => "U"
  );
  
  public static $VIEW_HEALTH_CARDS = array(
      'id' => "View health cards",
      'text' => 'Consulter les fiches santé #delasection',
      'section' => true,
      'predefined' => "ARSU"
  );
  
  public static $EDIT_LEADER_PRIVILEGES = array(
      'id' => "Edit leader privileges",
      'text' => 'Changer les privilèges des animateurs #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $UPDATE_PAYMENT_STATUS = array(
      'id' => "Update payment status",
      'text' => 'Modifier le statut de paiement',
      'section' => false,
      'predefined' => "SU"
  );
  
  public static $MANAGE_ANNUAL_FEAST_REGISTRATION = array(
      'id' => "Manage annual feast registration",
      'text' => "Valider les inscriptions pour la fête d'unité",
      'section' => false,
      'predefined' => "SU"
  );
  
  public static $MANAGE_SUGGESIONS = array(
      'id' => "Manage suggestions",
      'text' => "Répondre aux suggestions et les supprimer",
      'section' => false,
      'predefined' => "SU"
  );
  
  public static $EDIT_GLOBAL_PARAMETERS = array(
      'id' => "Edit global parameters",
      'text' => "Changer les paramètres du site",
      'section' => false,
      'predefined' => "U"
  );
  
  public static $MANAGE_SECTIONS = array(
      'id' => "Manage sections",
      'text' => "Créer, modifier et supprimer les sections",
      'section' => false,
      'predefined' => "U"
  );
  
  public static $DELETE_USERS = array(
      'id' => "Delete users",
      'text' => "Supprimer des comptes d'utilisateurs",
      'section' => false,
      'predefined' => "U"
  );
  
  public static $DELETE_GUEST_BOOK_ENTRIES = array(
      'id' => "Delete guest book entries",
      'text' => "Supprimer des entrées du livre d'or",
      'section' => false,
      'predefined' => "U"
  );
  
  public static function getPrivilegeList() {
    return array(
        self::$UPDATE_OWN_LISTING_ENTRY,
        self::$EDIT_CALENDAR,
        self::$POST_PHOTOS,
        self::$VIEW_HEALTH_CARDS,
        self::$VIEW_MEMBER_LIST,
        self::$VIEW_PRIVATE_SUGGESTIONS,
        
        self::$EDIT_PAGES,
        self::$EDIT_DOCUMENTS,
        self::$EDIT_NEWS,
        self::$SEND_EMAILS,
        self::$EDIT_SECTION_EMAIL_ADDRESS,
        self::$MANAGE_ACCOUNTS,
        self::$EDIT_LISTING_LIMITED,
        self::$EDIT_LEADER_PRIVILEGES,
        
        self::$MANAGE_ANNUAL_FEAST_REGISTRATION,
        self::$MANAGE_SUGGESIONS,
        self::$UPDATE_PAYMENT_STATUS,
        
        self::$EDIT_LISTING_ALL,
        self::$EDIT_GLOBAL_PARAMETERS,
        self::$MANAGE_SECTIONS,
        self::$DELETE_GUEST_BOOK_ENTRIES,
        self::$DELETE_USERS,
    );
  }
  
  
  public static function getPrivilegeArrayByCategory($forSection = false) {
    $basicPrivileges = array();
    $leaderInChargePrivileges = array();
    $unitTeamPrivileges = array();
    $unitLeaderPrivileges = array();
    foreach (self::getPrivilegeList() as $privilege) {
      if (strpos($privilege['predefined'], "A") !== false) {
        $basicPrivileges[] = $privilege;
        if ($forSection && $privilege['section']) $unitTeamPrivileges[] = $privilege;
      } elseif (strpos($privilege['predefined'], "R") !== false) {
        $leaderInChargePrivileges[] = $privilege;
        if ($forSection && $privilege['section']) $unitTeamPrivileges[] = $privilege;
      } elseif (strpos($privilege['predefined'], "S") !== false) {
        $unitTeamPrivileges[] = $privilege;
        if ($forSection && $privilege['section']) $basicPrivileges[] = $privilege;
      } elseif (strpos($privilege['predefined'], "U") !== false) {
        $unitLeaderPrivileges[] = $privilege;
        if ($forSection && $privilege['section']) $leaderInChargePrivileges[] = $privilege;
      }
    }
    return array(
        "S" => array(
          "Gestion de base" => $basicPrivileges,
          "Gestion avancée" => $leaderInChargePrivileges,
        ),
        "U" => array(
          "Gestion de l'unité" => $unitTeamPrivileges,
          "Gestion avancée de l'unité" => $unitLeaderPrivileges,
        ),
    );
  }


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