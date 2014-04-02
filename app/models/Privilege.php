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
  
  var $guarded = array('id', 'created_at', 'updated_at');
  
  public static $UPDATE_OWN_LISTING_ENTRY = array(
      'id' => 'Update own listing entry',
      'text' => 'Modifier ses données personnelles dans le listing',
      'section' => false,
      'predefined' => "ARSU"
  );
  
  public static $EDIT_PAGES = array(
      'id' => 'Edit pages',
      'text' => 'Modifier les pages #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $EDIT_SECTION_EMAIL_AND_SUBGROUP = array(
      'id' => "Edit section e-mail address and subgroup name",
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
  
  public static $MANAGE_ACCOUNTING = array(
      'id' => "Manage accounting",
      'text' => 'Gérer les comptes #delasection',
      'section' => true,
      'predefined' => "RSU"
  );
  
  public static $SECTION_TRANSFER = array(
      'id' => "Section transfer",
      'text' => "Changer les scouts de section",
      'section' => false,
      'predefined' => "SU",
  );
  
  public static $EDIT_LISTING_LIMITED = array(
      'id' => "Edit listing limited",
      'text' => 'Modifier les données non sensibles du listing #delasection',
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
        
        self::$EDIT_PAGES,
        self::$EDIT_DOCUMENTS,
        self::$EDIT_NEWS,
        self::$SEND_EMAILS,
        self::$EDIT_SECTION_EMAIL_AND_SUBGROUP,
        self::$MANAGE_ACCOUNTING,
        self::$SECTION_TRANSFER,
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
        if ($forSection) {
          if ($privilege['section']) {
            $basicPrivileges[] = array('privilege' => $privilege, 'scope' => 'S');
            $unitTeamPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
          } else {
            $basicPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
          }
        } else {
          if ($privilege['section'])
            $leaderInChargePrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
          else
            $basicPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
        }
      } elseif (strpos($privilege['predefined'], "R") !== false) {
        if ($forSection) {
          if ($privilege['section']) {
            $leaderInChargePrivileges[] = array('privilege' => $privilege, 'scope' => 'S');
            $unitTeamPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
          } else {
            $leaderInChargePrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
          }
        } else {
          $leaderInChargePrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
        }
      } elseif (strpos($privilege['predefined'], "S") !== false) {
        $unitTeamPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
      } elseif (strpos($privilege['predefined'], "U") !== false) {
        $unitLeaderPrivileges[] = array('privilege' => $privilege, 'scope' => 'U');
      }
    }
    return array(
        "Gestion de base" => $basicPrivileges,
        "Gestion avancée" => $leaderInChargePrivileges,
        "Gestion de l'unité" => $unitTeamPrivileges,
        "Gestion avancée de l'unité" => $unitLeaderPrivileges,
    );
  }
  
  public static function addBasePrivilegesForLeader($leader) {
    foreach (self::getPrivilegeList() as $privilege) {
      if (strpos($privilege['predefined'], "A") !== false) {
        try {
          Privilege::create(array(
              'operation' => $privilege['id'],
              'scope' => $privilege['section'] ? 'S' : 'U',
              'member_id' => $leader->id,
          ));
        } catch (Exception $e) {}
      }
    }
  }
  
  // Sets a privilege for a leader
  public static function set($operation, $scope, $leaderId, $state) {
    $privilege = Privilege::where('operation', '=', $operation)
            ->where('scope', '=', $scope)
            ->where('member_id', '=', $leaderId)
            ->first();
    if ($privilege && !$state) {
      $privilege->delete();
    } elseif (!$privilege && $state) {
      Privilege::create(array(
          'operation' => $operation,
          'scope' => $scope,
          'member_id' => $leaderId,
      ));
    }
  }
  
}