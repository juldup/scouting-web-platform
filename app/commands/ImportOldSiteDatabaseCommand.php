<?php

class ImportOldSiteDatabaseCommand extends \Illuminate\Console\Command {
  
  protected $name = "scouts:import-old";
  protected $description = "Imports database from the older version of the website";
  
  protected $sections = array();

  public function fire() {
    
    $pdo = new PDO('mysql:host=localhost;dbname=scouts-site', 'scouts-site', 'scouts', array());
    $pdo->query('set names "utf8"');
    
    // Parameters and pages
    $query = $pdo->prepare("SELECT * FROM parameters");
    $query->execute();
    foreach ($query->fetchAll() as $parameter) {
      if ($parameter['param'] == 'adressesUtiles') {
        Page::create(array(
          'type' => 'addresses',
          'section_id' => 1,
          'content_html' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'charte') {
        Page::create(array(
          'type' => 'unit_policy',
          'section_id' => 1,
          'content_html' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'descriptionSite') {
        // TODO
      }
      if ($parameter['param'] == 'docCategories') {
        // TODO
      }
      if ($parameter['param'] == 'emailDefaultFromAddress') {
        Parameter::create(array(
            'name' => Parameter::$DEFAULT_EMAIL_FROM_ADDRESS,
            'value' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'emailUnite') {
        Section::where('id', '=', 1)
                ->update(array('email' => $parameter['value']));
      }
      if ($parameter['param'] == 'headerSpecialContent') {
        // TODO
      }
      if ($parameter['param'] == 'lunite') {
        // TODO
      }
      if ($parameter['param'] == 'motsCles') {
        // TODO
      }
      if ($parameter['param'] == 'nomUniteTitre') {
        Parameter::create(array(
            'name' => Parameter::$UNIT_LONG_NAME,
            'value' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'numéro de compte unité') {
        Parameter::where('name', '=', Parameter::$UNIT_BANK_ACCOUNT)
                ->update(array('value' => $parameter['value']));
      }
      if ($parameter['param'] == 'pageAccueil') {
        Page::where('type', '=', 'home')
                ->where('section_id', '=', 1)
                ->update(array('content_html' => $parameter['value']));
      }
      if ($parameter['param'] == 'pageUnite') {
        Page::create(array(
          'type' => 'section_home',
          'section_id' => 1,
          'content_html' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'prix normal un enfant') {
        Parameter::where('name', '=', Parameter::$PRICE_1_CHILD)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'prix normal deux enfants') {
        Parameter::where('name', '=', Parameter::$PRICE_2_CHILDREN)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'prix normal trois enfants') {
        Parameter::where('name', '=', Parameter::$PRICE_3_CHILDREN)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'prix animateur un enfant') {
        Parameter::where('name', '=', Parameter::$PRICE_1_LEADER)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'prix animateur deux enfants') {
        Parameter::where('name', '=', Parameter::$PRICE_2_LEADERS)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'prix animateur trois enfants') {
        Parameter::where('name', '=', Parameter::$PRICE_3_LEADERS)
                ->update(array('value' =>$parameter['value']));
      }
      if ($parameter['param'] == 'sigleUnite') {
        Parameter::create(array(
            'name' => Parameter::$UNIT_SHORT_NAME,
            'value' => $parameter['value'],
        ));
      }
      if ($parameter['param'] == 'webmasterEmail') {
        Parameter::create(array(
            'name' => Parameter::$WEBMASTER_EMAIL,
            'value' => $parameter['value'],
        ));
      }
    }
    
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
      // TODO section pages
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