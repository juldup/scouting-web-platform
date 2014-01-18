<?php

class ImportOldSiteDatabaseCommand extends \Illuminate\Console\Command {
  
  protected $name = "scouts:import-old-data";
  protected $description = "Imports database from the older version of the website";
  
  protected $sections = array();
  protected $members = array();
  protected $users = array();
  protected $newAlbums = array();
  protected $oldAlbums = array();
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
      $this->users[$user['pseudo']] = $newUser;
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
      $this->members[$member['listingId']] = $newMember->id;
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
    
    // News
    $query = $pdo->prepare("SELECT * FROM news WHERE deleted='0'");
    $query->execute();
    foreach ($query->fetchAll() as $news) {
      News::create(array(
          'news_date' => substr($news['date'], 0, 10),
          'section_id' => $this->sectionToId($news['section']),
          'title' => $news['titre'],
          'body' => $news['text'],
      ));
    }
    
    // Documents
    $query = $pdo->prepare("SELECT * FROM documents");
    $query->execute();
    foreach ($query->fetchAll() as $document) {
      $path = $this->rootFolder . "/" . $document['path'];
      if (file_exists($path)) {
        $newDocument = Document::create(array(
            'doc_date' => $document['date'],
            'title' => $document['nom'],
            'description' => $document['description'],
            'public' => $document['public'] ? true : false,
            'filename' => $document['filename'],
            'category' => $document['categorie'],
            'section_id' => $this->sectionToId($document['section']),
        ));
        copy($path, $newDocument->getPath());
      } else {
        echo "Warning: document file $path does not exist.\n";
      }
    }
    
    // Links
    $query = $pdo->prepare("SELECT * FROM liens");
    $query->execute();
    foreach ($query->fetchAll() as $link) {
      Link::create(array(
          'title' => $link['nom'],
          'url' => $link['adresse'],
          'description' => $link['description'],
      ));
    }
    
    // Health cards
    $query = $pdo->prepare("SELECT * FROM fichemedicale");
    $query->execute();
    foreach ($query->fetchAll() as $healthCard) {
      $memberId = $this->members[$healthCard['idInListing']];
      $user = $this->users[$healthCard['user']];
      if ($memberId && $user) {
        $newHealthCard = HealthCard::create(array(
            'member_id' => $memberId,
            'contact1_name' => $healthCard['contact1Nom'],
            'contact1_address' => $healthCard['contact1Adresse'],
            'contact1_phone' => $healthCard['contact1Tel'],
            'contact1_relationship' => $healthCard['contact1LienParente'],
            'contact2_name' => $healthCard['contact2Nom'],
            'contact2_address' => $healthCard['contact2Adresse'],
            'contact2_phone' => $healthCard['contact2Tel'],
            'contact2_relationship' => $healthCard['contact2LienParente'],
            'doctor_name' => $healthCard['medecinNom'],
            'doctor_address' => $healthCard['medecinAdresse'],
            'doctor_phone' => $healthCard['medecinTel'],
            'has_no_constrained_activities' => $healthCard['participer'] ? true : false,
            'constrained_activities_details' => $healthCard['participerDetails'],
            'medical_data' => $healthCard['donneesMedicales'],
            'medical_history' => $healthCard['maladiesSubies'],
            'has_tetanus_vaccine' => $healthCard['vaccinTetanos'] ? true : false,
            'tetanus_vaccine_details' => $healthCard['vaccinTetanosDetails'],
            'has_allergy' => $healthCard['allergique'] ? true : false,
            'allergy_details' => $healthCard['allergieDetails'],
            'allergy_consequences' => $healthCard['allergieConsequences'],
            'has_special_diet' => $healthCard['regimeAlimentaire'] ? true : false,
            'special_diet_details' => $healthCard['regimeAlimentaireDetails'],
            'other_important_information' => $healthCard['autresRenseignements'],
            'has_drugs' => $healthCard['medicaments'] ? true : false,
            'drugs_details' => $healthCard['medicamentsDetails'],
            'drugs_autonomy' => $healthCard['medicamentsAutonome'],
            'comments' => $healthCard['infosCommentaires'],
        ));
        $newHealthCard->signatory_id = $user->id;
        $newHealthCard->signatory_email = $user->email;
        $newHealthCard->reminder_sent = $healthCard['rappelEnvoye'];
        $newHealthCard->signature_date = $healthCard['date'];
        $newHealthCard->save();
      } else {
        echo "Warning: missing member/user for health card: " . $healthCard['idInListing'];
      }
    }
    
    // Photos and albums
    $query = $pdo->prepare("SELECT * FROM repertoires");
    $query->execute();
    foreach ($query->fetchAll() as $album) {
      $newAlbum = PhotoAlbum::create(array(
          'section_id' => $this->sectionToId($album['section']),
          'name' => $album['nom'],
          'photo_count' => 0,
          'position' => $album['ordre'] - 100000,
          'archived' => $album['archive'] ? true : false,
          'last_update' => $album['lastUpdate'],
          'date' => $album['lastUpdate'],
      ));
      $this->newAlbums[$album['id']] = $newAlbum;
      $this->oldAlbums[$album['id']] = $album;
    }
    $query = $pdo->prepare("SELECT * FROM photos ORDER BY file");
    $query->execute();
    foreach ($query->fetchAll() as $photo) {
      $oldAlbumId = $photo['repertoireId'];
      if ($this->newAlbums[$oldAlbumId] && $this->oldAlbums[$oldAlbumId]) {
        $filePath = $this->rootFolder . "/photos/" . $this->oldAlbums[$oldAlbumId]['path'] . $photo['file'];
        if (file_exists($filePath)) {
          // Create photo
          $newAlbum = $this->newAlbums[$oldAlbumId];
          $newPhoto = Photo::create(array(
              'album_id' => $newAlbum->id,
              'filename' => $photo['file'],
              'caption' => $photo['comment'],
          ));
          $newPhoto->position = $newPhoto->id;
          $newPhoto->save();
          // Copy file
          copy($filePath, $newPhoto->getPhotoPath(Photo::$FORMAT_ORIGINAL));
          $newPhoto->createThumbnailPicture();
          $newPhoto->createPreviewPicture();
          // Update album
          $newAlbum->photo_count += 1;
          $newAlbum->save();
        } else {
          echo "Warning: photo $filePath does not exist.\n";
        }
      } else {
        echo "Warning: old album $oldAlbumId does not exist.\n";
      }
    }
    
    // TODO E-mails (+ attachments)
    
    
    
    // TODO Archived leaders, accounts, annual feast, listing snapshots, guest book, suggestions, userLog
    
  }
  
  protected function resetDatabase() {
    
    
    DB::table('pending_emails')->delete();
    DB::table('email_attachments')->delete();
    DB::table('emails')->delete();
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
        'type' => 'registration',
        'section_id' => 1,
        'body_html' => '<p><span style="font-size:20px"><strong>Comment s&#39;inscrire ?</strong></span></p><p>Pour <strong>inscrire</strong> un enfant ou un ado ne faisant pas encore partie de l&#39;unit&eacute; :</p><ul><li>Premi&egrave;rement, nous vous invitons &agrave; prendre connaissance de notre (ACCES CHARTE).</li><li>Deuxi&egrave;mement, vous devez prendre (ACCES CONTACT) avec l&#39;animateur d&#39;unit&eacute;.</li><li>Troisi&egrave;mement, vous devez remplir le (ACCES FORMULAIRE).</li><li>Finalement, vous devez verser le montant de la cotisation sur le compte de l&#39;unit&eacute; (BEXX-XXXX-XXXX-XXXX).</li></ul><p>Pour <strong>r&eacute;inscrire</strong> un scout, connectez-vous au site avec un compte valide et rendez-vous sur cette même page.<br />&nbsp;</p><p><span style="font-size:20px"><strong>Cotisation et prix</strong></span></p><p>Le scoutisme est un groupement o&ugrave; les animateurs sont b&eacute;n&eacute;voles. Malgr&eacute; cela, il vous est demand&eacute; de payer une cotisation qui couvre :</p><ul><li>L&#39;inscription dans l&#39;unit&eacute; (achat de mat&eacute;riel, financement des locaux, organisation d&#39;activit&eacute;s, etc.)</li><li>L&#39;inscription au sein de la f&eacute;d&eacute;ration scoute (revues, outils, formation des animateurs, promotion du scoutisme dans les pays d&eacute;favoris&eacute;s, etc.)</li><li>Une <a href="http://www.lesscouts.be/organiser/assurances/deux-assurances-de-base/">assurance</a> en responsabilit&eacute; civile et couvrant les accidents corporels pouvant survenir pendant nos activit&eacute;s</li></ul><p><strong>Combien dois-je payer ?</strong></p><ul><li>Le montant s&#39;&eacute;l&egrave;ve &agrave; <strong>(PRIX UN ENFANT) euros</strong> pour un enfant ((PRIX UN ANIMATEUR) euros s&#39;il est animateur).</li><li>Si vous avez deux enfants dans l&#39;unit&eacute;, vous payerez <strong>(PRIX DEUX ENFANTS) euros</strong> par enfant ((PRIX DEUX ANIMATEURS) euros par animateur).</li><li>Si vous avez trois enfants ou plus dans l&#39;unit&eacute;, le prix est de <strong>(PRIX TROIS ENFANTS) euros</strong> par enfant ((PRIX TROIS ANIMATEURS) euros par animateur).</li><li>&Agrave; ces frais s&#39;ajouteront les frais des activit&eacute;s sp&eacute;ciales, week-ends et grand camp, qui vous seront demand&eacute;s au cours de l&#39;ann&eacute;e.</li><li>Le prix ne doit jamais &ecirc;tre un frein &agrave; la participation. Si vous avez des difficult&eacute;s financi&egrave;res, n&#39;h&eacute;sitez pas &agrave; nous en parler, nous trouverons une solution ensemble.</li></ul><p><strong>Comment dois-je payer ?</strong></p><ul><li>Par virement bancaire sur le compte de l&#39;unit&eacute; : <strong>BEXX-XXXX-XXXX-XXXX</strong></li><li>Avec la mention &quot;Cotisation : NOM PR&Eacute;NOM(S)&quot;</li></ul>',
    ));
    DB::table('pages')->insert(array(
        'type' => 'help',
        'section_id' => 1,
        'body_html' => '<p><span style="font-size:18px"><strong>Naviguer sur le site</strong></span></p><p>Ce site contient deux menus compl&eacute;mentaires qui vous permettront d&#39;acc&eacute;der &agrave; l&#39;enti&egrave;ret&eacute; des pages :</p><ul><li><strong>Le menu principal :</strong>&nbsp;menu classique, permettant d&#39;acc&eacute;der &agrave; toutes les pages.</li><li><strong>Le choix de la section :</strong>&nbsp;il vous permet d&#39;adapater pratiquement toutes les pages du site &agrave; une section choisie. Gardez toujours ceci &agrave; l&#39;esprit en visitant le site, sinon vous risquez de vous sentir perdu.</li></ul><p>Toutes les pages sont toujours accessibles en deux clics via ces menus.</p><p>&nbsp;</p><p>Les sections&nbsp;<strong>Accueil</strong>&nbsp;et&nbsp;<strong>G&eacute;n&eacute;ral</strong>&nbsp;contiennent toutes les informations pratiques concernant l&#39;unit&eacute; en g&eacute;n&eacute;ral.</p><p>La section&nbsp;<strong>Animation</strong>&nbsp;contient les informations sp&eacute;cifiques &agrave; l&#39;animation dans les sections.</p><p>N&#39;oubliez pas de nous laisser vos commentaires sur le site et sur l&#39;unit&eacute; dans le livre d&#39;or. Si vous avez des suggestions, n&#39;h&eacute;sitez pas &agrave; les poster, car c&#39;est tr&egrave;s utile pour nous de savoir ce que vous attendez du site ou de l&#39;unit&eacute;.</p><p>&nbsp;</p><p><strong><span style="font-size:18px">Connexion sur le site</span></strong></p><p>Il est possible de visiter le site en tant que visiteur, sans vous connecter.</p><p>Pour avoir acc&egrave;s aux donn&eacute;es confidentielles (convocations, documents, e-mails, photos, listing, fiches sant&eacute;), il faut que vous vous connectiez avec un compte dont l&#39;adresse e-mail est dans notre listing.</p><p>Une fois que votre compte d&#39;utilisateur est cr&eacute;&eacute;, cochez la case&nbsp;<strong>m&eacute;moriser</strong>&nbsp;dans le menu de connexion en vous connectant. Ainsi, votre compte sera automatiquement charg&eacute; &agrave; chaque visite du site, sans que vous ayez &agrave; vous en occuper.</p><p>&nbsp;</p><p><span style="font-size:18px"><strong>Le vocabulaire scout</strong></span></p><p><strong>Animateur : </strong>Membre d&#39;un staff qui s&#39;occupe de l&#39;animation d&#39;une section.</p><p><strong>Animateur&nbsp;d&#39;unit&eacute; : </strong>Animateur reponsable de l&#39;unit&eacute;, member du staff d&#39;unit&eacute;.&nbsp;<em>On utilise souvent ce terme aussi pour les &eacute;quipiers d&#39;unit&eacute;.</em></p><p><strong>Animateur responsable : </strong>Animateur d&#39;un staff qui repr&eacute;sente la section. Il en est le responsable officiel aupr&egrave;s des parents, de l&#39;unit&eacute; et de la f&eacute;d&eacute;ration scoute.</p><p><strong>Anim&eacute; : </strong>(voir <strong>&laquo;&nbsp;Scout &raquo;</strong>)</p><p><strong>Assistant d&#39;unit&eacute; : </strong>(voir <strong>&laquo; &Eacute;quipier d&#39;unit&eacute; &raquo;</strong>)</p><p><strong>Baden-Powell : </strong>Fondateur du mouvement scout. Plus d&#39;informations sur&nbsp;<a href="http://fr.wikipedia.org/wiki/Robert_Baden-Powell">Wikip&eacute;dia</a>.</p><p><strong>Baladin : </strong>Scout de 6 &agrave; 8 ans inscrit dans l&#39;unit&eacute;. Les baladins forment ensemble la Ribambelle.</p><p><strong>Chef : </strong>(voir <strong>&laquo; Animateur &raquo;</strong>)</p><p><strong>CP : </strong>Capitaine de patrouille <em>ou chef de patrouille</em>. C&#39;est un des ain&eacute;s de la patrouille, il la dirige et en est reponsable. Il sert de lien entre sa patrouille et les animateurs.</p><p><strong>&Eacute;claireur : </strong>Scout de 12 &agrave; 17 ans, faisant partie d&#39;une troupe.</p><p><strong>&Eacute;quipier d&#39;unit&eacute; :&nbsp;</strong>Membre du staff d&#39;unit&eacute;, autre que l&#39;animateur d&#39;unit&eacute;.&nbsp;<em>Parfois appel&eacute; assistant d&#39;unit&eacute;.</em></p><p><strong>F&eacute;d&eacute;ration scoute : </strong>Association dont l&#39;unit&eacute; fait partie. La f&eacute;d&eacute;ration scoute est g&eacute;r&eacute;e par des professionnels, et nous fournit une panoplie d&#39;outils et d&#39;avantages. Notre f&eacute;d&eacute;ration s&#39;appelle &quot;<a href="http://www.lesscouts.be/">Les Scouts</a>&quot;.</p><p><strong>Loup rouge : </strong>Dans la meute, un loup rouge est un louveteau de premi&egrave;re ann&eacute;e.</p><p><strong>Loup fauve : </strong>Dans la meute, un loup fauve (ou orange) est un louveteau de deuxi&egrave;me ann&eacute;e.</p><p><strong>Loup jaune : </strong>Dans la meute, un loup jaune est un louveteau de troisi&egrave;me ann&eacute;e.</p><p><strong>Loup blanc : </strong>Dans la meute, un loup blanc est un louveteau de derni&egrave;re ann&eacute;e.</p><p><strong>Louveteau : </strong>Scout de 8 &agrave; 12 ans, faisant partie d&#39;une meute.</p><p><strong>Meute : </strong>Ensemble des louveteaux et leurs animateurs.</p><p><strong>Pionnier : </strong>Scout de 16 &agrave; 18 ans, membre du poste.</p><p><strong>Patrouille : </strong>Groupe d&#39;&eacute;claireurs formant ensemble une famille dans la troupe. Une patrouille compte en g&eacute;n&eacute;ral entre 6 et 10 &eacute;claireurs, et est men&eacute;e par le CP.</p><p><strong>Poste : </strong>Ensemble des pionniers et leurs animateurs.</p><p><strong>Ribambelle : </strong>Ensemble des baladins et leurs animateurs.</p><p><strong>Scout : </strong>Enfant, adolescent ou jeune inscrit dans une section de l&#39;unit&eacute;, &agrave; savoir : un baladin, un louveteau, un &eacute;claireur ou un pionnier.&nbsp;<em>Ce mot est encore souvent utilis&eacute; pour r&eacute;f&eacute;rer aux &eacute;claireurs. On utilise parfois aussi le terme &laquo; anim&eacute; &raquo;.</em></p><p><strong>Second : </strong>Louveteau qui aide le sizenier dans sa t&acirc;che de gestion de la sizaine. Il y a en g&eacute;n&eacute;ral un second par sizaine.</p><p><strong>Section : </strong>Une des subdivisions de l&#39;unit&eacute;. Les sections de l&#39;unit&eacute; sont la ribambelle, la meute, la troupe et le poste.</p><p><strong>Sizaine : </strong>Groupe de six ou sept louveteaux au sein de la meute, qui forment ensemble une famille. Chaque louveteau fait partie d&#39;une sizaine, et les sizaines sont form&eacute;es pour un an. Une sizaine comporte en outre un sizenier et un second.</p><p><strong>Sizenier : </strong>Repr&eacute;sentant d&#39;une sizaine chez les louveteau. Le sizenier est en g&eacute;n&eacute;ral un louveteau de derni&egrave;re ann&eacute;e, qui g&egrave;re sa sizaine.</p><p><strong>Staff : </strong>Ensemble des animateurs d&#39;une section, ou de toute l&#39;unit&eacute;.</p><p><strong>Totem : </strong>Nom d&#39;animal donn&eacute; &agrave; un &eacute;claireur lors de son premier camp. C&#39;est par ce nom qu&#39;on l&#39;appellera ensuite dans la troupe.</p><p><strong>Troupe : </strong>Ensemble d&#39;&eacute;claireurs et leurs animateurs.</p>',
    ));
    DB::table('parameters')->insert(array(
        'name' => Parameter::$SMTP_HOST,
        'value' => 'email-smtp.us-east-1.amazonaws.com'
    ));
    DB::table('parameters')->insert(array(
        'name' => Parameter::$SMTP_PORT,
        'value' => '587'
    ));
    DB::table('parameters')->insert(array(
        'name' => Parameter::$SMTP_SECURITY,
        'value' => 'tls'
    ));
    DB::table('parameters')->insert(array(
        'name' => Parameter::$SMTP_USERNAME,
        'value' => 'AKIAJT46KXQ3MMV5OBHQ'
    ));
    DB::table('parameters')->insert(array(
        'name' => Parameter::$SMTP_PASSWORD,
        'value' => 'AtE++d6p4vK0fdmIMngTTC/wAlSSz8C95i6EkajewPJ+'
    ));
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
  
  protected function createPage($type, $sectionId, $bodyHTML) {
    $page = Page::create(array(
      'type' => $type,
      'section_id' => $sectionId,
      'body_html' => $bodyHTML,
    ));
    
    $matches = array();
    preg_match_all("/src\=[\"\'].*[\"\']/", $bodyHTML, $matches);
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
        $bodyHTML = str_replace($match, 'src="' . $url . '"', $bodyHTML);
        $page->body_html = $bodyHTML;
        $page->save();
      } else {
        echo "Warning: file $srcPath does not exist.\n";
      }
    }
  }
  
}