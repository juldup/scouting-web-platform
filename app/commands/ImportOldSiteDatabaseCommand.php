<?php

class ImportOldSiteDatabaseCommand extends \Illuminate\Console\Command {
  
  protected $name = "scouts:import-old-data";
  protected $description = "Imports database from the older version of the website";
  
  protected $sections = array();
  protected $calendarTypes = array(
      'reunion' => 'normal',
      'special' => 'special',
      'conge' => 'break',
      'animateurs' => 'leaders',
      'we' => 'weekend',
      'camp' => 'camp',
      'barpi' => 'bar',
      'toilettes' => 'cleaning',
  );
  
  protected $rootFolder = "/home/julien/Websites/scouts-site";
  protected $newSiteRootURL = "http://localhost/scouts-laravel/public/";
  
  public function fire() {
    
    $pdo = new PDO('mysql:host=localhost;dbname=scouts-site', 'scouts-site', 'scouts', array());
    $pdo->query('set names "utf8"');
    
    $this->resetDatabase();
    DB::table('pages')->delete();
    
    // Parameters and pages
    $query = $pdo->prepare("SELECT * FROM parameters");
    $query->execute();
    foreach ($query->fetchAll() as $parameter) {
      if ($parameter['param'] == 'adressesUtiles') {
        $this->createPage('addresses', 1, $parameter['value']);
      }
      if ($parameter['param'] == 'charte') {
        $this->createPage('unit_policy', 1, $parameter['value']);
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
        $this->createPage('home', 1, $parameter['value']);
      }
      if ($parameter['param'] == 'pageUnite') {
        $this->createPage('section_home', 1, $parameter['value']);
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
      
      if ($section['page']) {
        $this->createPage("section_home", $sectionObject->id, $section['page']);
      }
      if ($section['pageUniforme']) {
        $this->createPage("section_uniform", $sectionObject->id, $section['pageUniforme']);
      }
      
    }
    
    // Users
    $query = $pdo->prepare("SELECT * FROM members");
    $query->execute();
    foreach ($query->fetchAll() as $user) {
      $newUser = User::create(array(
          'username' => $user['pseudo'],
          'password' => $user['password'],
          'email' => $user['email'],
          'default_section' => $user['ongletFixe'] ? $this->sectionToId($user['onglet']) : 1,
      ));
      
      $newUser->is_webmaster = $user['privilege'] == "webmaster";
      $newUser->last_visit = $user['lastVisit'];
      $newUser->verification_code = $user['verificationCode'];
      $newUser->current_visit = $user['currentVisit'];
      $newUser->verified = $user['verified'];
      $newUser->save();
    }
    
    // Members
    $query = $pdo->prepare("SELECT * FROM listing");
    $query->execute();
    foreach ($query->fetchAll() as $member) {
      $leader = null;
      $hasPicture = false;
      if ($member['animateur']) {
        $queryLeader = $pdo->prepare("SELECT * FROM animateurs WHERE idInListing='" . $member['listingId'] . "'");
        $queryLeader->execute();
        $leader = $queryLeader->fetch();
        $picturePath = $this->rootFolder . "/" . $leader['pathPhoto'];
        $hasPicture = $leader['pathPhoto'] && (file_exists($picturePath));
      }
      $email = explode(',', $member['email']);
      if (count($email) > 3) echo "Warning: member " . $member['prenom'] . " " . $member['nom'] . " have more than 3 e-mail addresses\n";
      $newMember = Member::create(array(
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
          'has_picture' => $hasPicture,
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
      if ($hasPicture) {
        copy($picturePath, $newMember->getPicturePath());
      }
    }
    
    // TODO Privileges
    
    // Calendar
    $query = $pdo->prepare("SELECT * FROM calendrier");
    $query->execute();
    foreach ($query->fetchAll() as $calendarItem) {
      CalendarItem::create(array(
          'start_date' => $calendarItem['startDate'],
          'end_date' => $calendarItem['endDate'],
          'section_id' => $this->sectionToId($calendarItem['section']),
          'event' => $calendarItem['event'],
          'description' => $calendarItem['description'],
          'type' => $this->calendarTypes[$calendarItem['type']],
      ));
    }
  }
  
  protected function resetDatabase() {
    
    
    DB::table('pending_emails')->delete();
    DB::table('email_attachments')->delete();
    DB::table('emails')->delete();
    Schema::table('photo_albums', function($table) {
      $table->dropForeign("photo_albums_cover_picture_id_foreign");
    });
    DB::table('photos')->delete();
    DB::table('photo_albums')->delete();
    DB::table('health_cards')->delete();
    DB::table('links')->delete();
    DB::table('documents')->delete();
    DB::table('news')->delete();
    DB::table('calendar_items')->delete();
    DB::table('privileges')->delete();
    DB::table('members')->delete();
    DB::table('password_recoveries')->delete();
    DB::table('users')->delete();
    DB::table('page_images')->delete();
		DB::table('pages')->delete();
    DB::table('sections')->delete();
    DB::table('parameters')->delete();
    
    DB::table('parameters')->insert(array('name' => Parameter::$CALENDAR_DOWNLOADABLE, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$REGISTRATION_ACTIVE, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_ADDRESSES, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_ANNUAL_FEAST, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_CALENDAR, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_CONTACTS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_DOCUMENTS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_EMAILS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_GUEST_BOOK, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_HEALTH_CARDS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_HELP, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_LEADERS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_LINKS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_LISTING, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_NEWS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_PHOTOS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_REGISTRATION, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_SECTIONS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_SUGGESTIONS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_UNIFORMS, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$SHOW_UNIT_POLICY, 'value' => 'true'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_1_CHILD, 'value' => '40,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_2_CHILDREN, 'value' => '32,25'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_3_CHILDREN, 'value' => '26,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_1_LEADER, 'value' => '40,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_2_LEADERS, 'value' => '32,25'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_3_LEADERS, 'value' => '26,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$UNIT_BANK_ACCOUNT, 'value' => 'BE00-0000-0000-0000'));
    DB::table('sections')->insert(array(
        'id' => 1,
        'name' => 'Unité',
        'slug' => 'unite',
        'position' => 1,
        'section_type' => 'U',
        'section_type_number' => 0,
        'color' => "#000000",
        'email' => '',
        'de_la_section' => "de l'unité",
        'la_section' => "l'unité",
    ));
    DB::table('pages')->insert(array(
        'type' => 'home',
        'section_id' => 1,
        'content_html' => "<h1>Page d'accueil de l'unité</h1><p>Bienvenue.</p>",
    ));
    DB::table('pages')->insert(array(
        'type' => 'registration',
        'section_id' => 1,
        'content_html' => '<p><span style="font-size:20px"><strong>Comment s&#39;inscrire ?</strong></span></p><p>Pour <strong>inscrire</strong> un enfant ou un ado ne faisant pas encore partie de l&#39;unit&eacute; :</p><ul><li>Premi&egrave;rement, nous vous invitons &agrave; prendre connaissance de notre (ACCES CHARTE).</li><li>Deuxi&egrave;mement, vous devez prendre (ACCES CONTACT) avec l&#39;animateur d&#39;unit&eacute;.</li><li>Troisi&egrave;mement, vous devez remplir le (ACCES FORMULAIRE).</li><li>Finalement, vous devez verser le montant de la cotisation sur le compte de l&#39;unit&eacute; (BEXX-XXXX-XXXX-XXXX).</li></ul><p>Pour <strong>r&eacute;inscrire</strong> un scout, connectez-vous au site avec un compte valide et rendez-vous sur cette même page.<br />&nbsp;</p><p><span style="font-size:20px"><strong>Cotisation et prix</strong></span></p><p>Le scoutisme est un groupement o&ugrave; les animateurs sont b&eacute;n&eacute;voles. Malgr&eacute; cela, il vous est demand&eacute; de payer une cotisation qui couvre :</p><ul><li>L&#39;inscription dans l&#39;unit&eacute; (achat de mat&eacute;riel, financement des locaux, organisation d&#39;activit&eacute;s, etc.)</li><li>L&#39;inscription au sein de la f&eacute;d&eacute;ration scoute (revues, outils, formation des animateurs, promotion du scoutisme dans les pays d&eacute;favoris&eacute;s, etc.)</li><li>Une <a href="http://www.lesscouts.be/organiser/assurances/deux-assurances-de-base/">assurance</a> en responsabilit&eacute; civile et couvrant les accidents corporels pouvant survenir pendant nos activit&eacute;s</li></ul><p><strong>Combien dois-je payer ?</strong></p><ul><li>Le montant s&#39;&eacute;l&egrave;ve &agrave; <strong>(PRIX UN ENFANT) euros</strong> pour un enfant ((PRIX UN ANIMATEUR) euros s&#39;il est animateur).</li><li>Si vous avez deux enfants dans l&#39;unit&eacute;, vous payerez <strong>(PRIX DEUX ENFANTS) euros</strong> par enfant ((PRIX DEUX ANIMATEURS) euros par animateur).</li><li>Si vous avez trois enfants ou plus dans l&#39;unit&eacute;, le prix est de <strong>(PRIX TROIS ENFANTS) euros</strong> par enfant ((PRIX TROIS ANIMATEURS) euros par animateur).</li><li>&Agrave; ces frais s&#39;ajouteront les frais des activit&eacute;s sp&eacute;ciales, week-ends et grand camp, qui vous seront demand&eacute;s au cours de l&#39;ann&eacute;e.</li><li>Le prix ne doit jamais &ecirc;tre un frein &agrave; la participation. Si vous avez des difficult&eacute;s financi&egrave;res, n&#39;h&eacute;sitez pas &agrave; nous en parler, nous trouverons une solution ensemble.</li></ul><p><strong>Comment dois-je payer ?</strong></p><ul><li>Par virement bancaire sur le compte de l&#39;unit&eacute; : <strong>BEXX-XXXX-XXXX-XXXX</strong></li><li>Avec la mention &quot;Cotisation : NOM PR&Eacute;NOM(S)&quot;</li></ul>',
    ));
    
    Schema::table('photo_albums', function($table) {
      $table->foreign('cover_picture_id')->references('id')->on('photos')->onDelete('set null');
    });
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
  
  protected function createPage($type, $sectionId, $contentHTML) {
    $page = Page::create(array(
      'type' => $type,
      'section_id' => $sectionId,
      'content_html' => $contentHTML,
    ));
    
    $matches = array();
    preg_match_all("/src\=[\"\'].*[\"\']/", $contentHTML, $matches);
    foreach ($matches[0] as $match) {
      $src = substr($match, 5, strlen($match) - 6);
      $srcPath = $this->rootFolder . "/" . $src;
      if (file_exists($srcPath)) {
        $basename = pathinfo($src, PATHINFO_FILENAME);
        $extension = pathinfo($src, PATHINFO_EXTENSION);
        $filename = $basename . ($extension ? "." . $extension : "");
        $image = PageImage::create(array(
            'page_id' => $page->id,
            'original_name' => $filename,
        ));
        copy($srcPath, $image->getPath());
        $url = str_replace(URL::to('/') . "/", $this->newSiteRootURL, $image->getURL());
        $contentHTML = str_replace($match, 'src="' . $url . '"', $contentHTML);
        $page->content_html = $contentHTML;
        $page->save();
      } else {
        echo "Warning: file $srcPath does not exist.\n";
      }
    }
  }
  
}