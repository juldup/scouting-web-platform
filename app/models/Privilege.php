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

/**
 * This Eloquent class represents a privilege of a leader
 * 
 * Columns:
 *   - operation: A string representing which operation this privilege allows
 *   - scope:     'S' if the privilege is limited to the leader's section, 'U' for unit if they can use this privilege on any section
 *   - member_id: The leader affected by this privilege
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
  
  // Following are all the privileges with
  //   - id: the string representing the privilege
  //   - text: a textual description of the privilege
  //   - section: true if this privilege can be applied to only a section, false if it is a global privilige
  //   - predefined: in which predefined classes it will be applied
  
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
  
  public static $MANAGE_ATTENDANCE = array(
      'id' => "Manage attendance",
      'text' => 'Cocher les présences aux activités #delasection',
      'section' => true,
      'predefined' => "ARSU"
  );
  
  public static $MANAGE_EVENT_PAYMENTS = array(
      'id' => "Manage event payments",
      'text' => 'Cocher le paiement des membres pour les activités #delasection',
      'section' => true,
      'predefined' => "ARSU"
  );
  
  public static $EDIT_NEWS = array(
      'id' => "Edit news",
      'text' => 'Poster des nouvelles (actualités) pour #lasection',
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
  
  public static $EDIT_STYLE = array(
      'id' => "Edit style",
      'text' => "Modifier le style du site",
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
  
  /**
   * Returns the list of privileges
   */
  public static function getPrivilegeList() {
    return array(
        self::$UPDATE_OWN_LISTING_ENTRY,
        self::$EDIT_CALENDAR,
        self::$MANAGE_ATTENDANCE,
        self::$MANAGE_EVENT_PAYMENTS,
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
        self::$EDIT_STYLE,
        self::$MANAGE_SECTIONS,
        self::$DELETE_GUEST_BOOK_ENTRIES,
        self::$DELETE_USERS,
    );
  }
  
  /**
   * Returns the list of privileges sorted in 4 categories
   * 
   * @param boolean $forSection  Whether this is for a section leader or a unit leader
   */
  public static function getPrivilegeArrayByCategory($forSection = false) {
    // The four categories
    $basicPrivileges = array();
    $leaderInChargePrivileges = array();
    $unitTeamPrivileges = array();
    $unitLeaderPrivileges = array();
    // Sort privileges into categories
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
    // Return list of categories
    return array(
        "Gestion de base" => $basicPrivileges,
        "Gestion avancée" => $leaderInChargePrivileges,
        "Gestion de l'unité" => $unitTeamPrivileges,
        "Gestion avancée de l'unité" => $unitLeaderPrivileges,
    );
  }
  
  /**
   * Sets and saves all the privileges from the base category to the given leader
   */
  public static function addBasePrivilegesForLeader($leader) {
    foreach (self::getPrivilegeList() as $privilege) {
      if (strpos($privilege['predefined'], "A") !== false) {
        try {
          Privilege::create(array(
              'operation' => $privilege['id'],
              'scope' => $privilege['section'] ? 'S' : 'U',
              'member_id' => $leader->id,
          ));
        } catch (Exception $e) {
          Log::error($e);
        }
      }
    }
  }
  
  /**
   * Sets ($state = true) or unsets ($state = false) and saves a privilege for a leader in a given scope
   */
  public static function set($operation, $scope, $leaderId, $state) {
    // Find existing privilege
    $privilege = Privilege::where('operation', '=', $operation)
            ->where('scope', '=', $scope)
            ->where('member_id', '=', $leaderId)
            ->first();
    if ($privilege && !$state) {
      // Privilege exists and should be removed
      $privilege->delete();
    } elseif (!$privilege && $state) {
      // Privilege does not exist and should be created
      Privilege::create(array(
          'operation' => $operation,
          'scope' => $scope,
          'member_id' => $leaderId,
      ));
    }
  }
  
}