<?php

class ImportOldSiteDatabaseCommand extends \Illuminate\Console\Command {
  
  protected $name = "scouts:import-old";
  protected $description = "Imports database from the older version of the website";
  
  protected $sections = array();

  public function fire() {
    
    $pdo = new PDO('mysql:host=localhost;dbname=scouts-site', 'scouts-site', 'scouts', array());
    $pdo->query('set names "utf8"');
    
    // Sections
    $query = $pdo->prepare("SELECT * FROM sections, sectionsAll WHERE sections.id=sectionsAll.sectionId ORDER BY sectionId");
    $query->execute();
    foreach ($query->fetchAll() as $section) {
      $sectionObject = Section::create(array(
          'name' => $section['nom'],
          'slug' => Helper::slugify($section['nom']),
          'position' => $section['sectionId'] + 1,
          'section_type' => $section['symbolFede'],
          'section_type_number' => $section['numberFede'],
          'color' => $section['couleur'],
          'email' => $section['email'],
          'de_la_section' => $section['delasection'],
          'la_section' => $section['lasection'],
          'subgroup_name' => self::subgroupNameFor($section['symbolFede']),
      ));
      $this->sections[$section['nom']] = $sectionObject->id;
    }
    
    // Users
    $query = $pdo->prepare("SELECT * FROM members");
    $query->execute();
    foreach ($query->fetchAll() as $user) {
      User::create(array(
          'username' => $user['pseudo'],
          'password' => $user['password'],
          'email' => $user['email'],
          'is_webmaster' => $user['privilege'] == "webmaster",
          'last_visit' => $user['lastVisit'],
          'verified' => $user['verified'],
          'verification_code' => $user['verificationCode'],
      ));
    }
    
    // Members
    $query = $pdo->prepare("SELECT * FROM listing");
    $query->execute();
    foreach ($query->fetchAll() as $member) {
      $leader = null;
      if ($member['animateur']) {
        $queryLeader = $pdo->prepare("SELECT * FROM animateurs WHERE idInListing='" . $member['listingId'] . "'");
        $queryLeader->execute();
        $leader = $queryLeader->fetch();
      }
      $email = explode(',', $member['email']);
      if (count($email) > 3) echo "Warning: member " . $member['prenom'] . " " . $member['nom'] . " have more than 3 e-mail addresses\n";
      Member::create(array(
          'first_name' => $member['prenom'],
          'last_name' => $member['nom'],
          'birth_date' => $member['ddn'],
          'gender' => $member['sexe'],
          'nationality' => $member['nationalite'],
          'address' => $member['adresse'],
          'postcode' => $member['CP'],
          'city' => $member['ville'],
          'has_handicap' => $member['handicap'],
          'handicap_details' => $member['handicapDetails'],
          'comments' => $member['commentaires'],
          'leader_name' => $leader ? $member['nomDAnimateur'] : null,
          'leader_in_charge' => $leader ? $leader['responsable'] : false,
          'leader_description' => $leader ? $leader['description'] : null,
          'leader_role' => $leader ? $leader['role'] : null,
          'has_picture' => $leader ? $leader['pathPhoto'] != null : null,
          'section_id' => self::sectionToId($member['section']),
          'phone1' => $member['telephone'],
          'phone1_owner' => null,
          'phone1_private' => false,
          'phone2' => $member['gsm1'],
          'phone2_owner' => $member['gsmOwner1'],
          'phone2_private' => $member['gsm1Confidentiel'],
          'phone3' => $member['gsm2'],
          'phone3_owner' => $member['gsmOwner2'],
          'phone3_private' => $member['gsm2Confidentiel'],
          'phone_member' => $member['gsmScout'],
          'phone_member_private' => $member['gsmScoutConfidentiel'],
          'email1' => count($email) >= 1 ? trim($email[0]) : null,
          'email2' => count($email) >= 2 ? trim($email[1]) : null,
          'email3' => count($email) >= 3 ? trim($email[2]) : null,
          'email_member' => trim($member['emailScout']),
          'totem' => $member['totem'],
          'quali' => $member['quali'],
          'family_in_other_units' => $member['autreUnite'],
          'family_in_other_units_details' => $member['autreUniteDetails'],
          'is_leader' => $member['animateur'],
          'last_reregistration' => $member['derniereReinscription'],
          'subscription_paid' => $member['paye'],
          'subgroup' => $member['patrouille'],
          'year_in_section' => $member['annee'] ? $member['annee'] : 1,
          'validated' => true,
      ));
    }
    
  }
  
  protected function sectionToId($sectionName) {
    if ($sectionName == "Unité") return 1;
    return $this->sections[$sectionName];
  }
  
  public static function subgroupNameFor($symbol) {
    if ($symbol == 'B') return "Hutte";
    if ($symbol == 'L') return "Sizaine";
    if ($symbol == 'E') return "Patrouille";
    return null;
  }
  
}