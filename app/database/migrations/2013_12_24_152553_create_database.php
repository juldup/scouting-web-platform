<?php

use Illuminate\Database\Migrations\Migration;

class CreateDatabase extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    
    // Parameters
    Schema::create('parameters', function($table) {
      $table->increments('id');
      $table->string('name');
      $table->text('value');
      $table->timestamps();
      
      $table->unique('name');
    });
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
    
    // Sections
    Schema::create('sections', function($table) {
      $table->increments('id');
      $table->string('name');
      $table->string('slug')->unique();
      $table->string('position');
      $table->string('section_type')->nullable();
      $table->string('section_type_number')->nullable();
      $table->string('color')->default("#000000");
      $table->string('email')->nullable();
      $table->string('de_la_section');
      $table->string('la_section');
      $table->text('last_email_content')->default("");
      $table->timestamps();
      
      $table->index('slug');
    });
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
        'last_email_content' => "",
    ));
    
    // Pages
		Schema::create('pages', function($table) {
      $table->increments('id');
      $table->string('type');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->text('content_html')->default("");
      $table->timestamps();
      
      $table->index('type');
      $table->index('section_id');
    });
    DB::table('pages')->insert(array(
        'type' => 'home',
        'section_id' => 1,
        'content_html' => "<h1>Page d'accueil de l'unité</h1><p>Bienvenue.</p>",
    ));
    DB::table('pages')->insert(array(
        'type' => 'registration',
        'section_id' => 1,
        'content_html' => '<p><span style="font-size:20px"><strong>Comment s&#39;inscrire ?</strong></span></p><p>Pour <strong>inscrire</strong> un enfant ou un ado ne faisant pas encore partie de l&#39;unit&eacute; :</p><ul><li>Premi&egrave;rement, nous vous invitons &agrave; prendre connaissance de notre <a href="/charte">charte d&#39;unit&eacute;</a>.</li><li>Deuxi&egrave;mement, vous devez prendre <a href="/contacts">contact</a> avec l&#39;animateur d&#39;unit&eacute;.</li><li>Troisi&egrave;mement, vous devez remplir le <a href="/inscription/formulaire">formulaire d&#39;inscription</a>.</li><li>Finalement, vous devez verser le montant de la cotisation sur le compte de l&#39;unit&eacute; (BEXX-XXXX-XXXX-XXXX).</li></ul><p>Pour <strong>r&eacute;inscrire</strong> un scout, connectez-vous au site avec un compte valide et rendez-vous sur cette même page.<br />&nbsp;</p><p><span style="font-size:20px"><strong>Cotisation et prix</strong></span></p><p>Le scoutisme est un groupement o&ugrave; les animateurs sont b&eacute;n&eacute;voles. Malgr&eacute; cela, il vous est demand&eacute; de payer une cotisation qui couvre :</p><ul><li>L&#39;inscription dans l&#39;unit&eacute; (achat de mat&eacute;riel, financement des locaux, organisation d&#39;activit&eacute;s, etc.)</li><li>L&#39;inscription au sein de la f&eacute;d&eacute;ration scoute (revues, outils, formation des animateurs, promotion du scoutisme dans les pays d&eacute;favoris&eacute;s, etc.)</li><li>Une <a href="http://www.lesscouts.be/organiser/assurances/deux-assurances-de-base/">assurance</a> en responsabilit&eacute; civile et couvrant les accidents corporels pouvant survenir pendant nos activit&eacute;s</li></ul><p><strong>Combien dois-je payer ?</strong></p><ul><li>Le montant s&#39;&eacute;l&egrave;ve &agrave; <strong>XX euros</strong> pour un enfant (XX euros s&#39;il est animateur).</li><li>Si vous avez deux enfants dans l&#39;unit&eacute;, vous payerez <strong>XX euros</strong> par enfant (XX euros par animateur).</li><li>Si vous avez trois enfants ou plus dans l&#39;unit&eacute;, le prix est de <strong>XX euros</strong> par enfant (XX euros par animateur).</li><li>&Agrave; ces frais s&#39;ajouteront les frais des activit&eacute;s sp&eacute;ciales, week-ends et grand camp, qui vous seront demand&eacute;s au cours de l&#39;ann&eacute;e.</li><li>Le prix ne doit jamais &ecirc;tre un frein &agrave; la participation. Si vous avez des difficult&eacute;s financi&egrave;res, n&#39;h&eacute;sitez pas &agrave; nous en parler, nous trouverons une solution ensemble.</li></ul><p><strong>Comment dois-je payer ?</strong></p><ul><li>Par virement bancaire sur le compte de l&#39;unit&eacute; : <strong>BEXX-XXXX-XXXX-XXXX</strong></li><li>Avec la mention &quot;Cotisation : NOM PR&Eacute;NOM(S)&quot;</li></ul>',
    ));
    
    // Page images
    Schema::create('page_images', function($table) {
      $table->increments('id');
      $table->string('original_name');
      $table->integer('page_id')->unsigned();
      $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
      
      
      $table->timestamps();
    });     
    
    // Users
    Schema::create('users', function($table) {
      $table->increments('id');
      $table->string('password');
      $table->string('username')->nullable();
      $table->string('email');
      $table->integer('default_section')->unsigned()->nullable();
      $table->boolean('is_webmaster')->default(false);
      $table->integer('last_visit')->default('0');
      $table->integer('current_visit')->default('0');
      $table->string('verification_code')->nullable();
      $table->boolean('verified')->default(false);
      $table->timestamps();
      
      $table->index('username');
      $table->index('email');
      $table->index('verification_code');
    });
    
    // Password recoveries
    Schema::create('password_recoveries', function($table) {
      $table->increments('id');
      $table->integer('user_id')->unsigned();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      $table->string('code');
      $table->integer('timestamp');
      $table->timestamps();
      
      $table->index('code');
    });
    
    // Members
    Schema::create('members', function($table) {
      $table->increments('id');
      // Identity
      $table->string('first_name');
      $table->string('last_name');
      $table->date('birth_date');
      $table->string('gender');
      $table->string('nationality');
      $table->boolean('has_handicap')->default(false);
      $table->text('handicap_details')->nullable();
      // Scout-related details
      $table->string('totem')->nullable();
      $table->string('quali')->nullable();
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections');
      $table->integer('year_in_section')->default(1);
      $table->string('subgroup')->nullable();
      $table->integer('family_in_other_units')->default(0);
      $table->text('family_in_other_units_details')->nullable();
      $table->text('comments')->nullable();
      // Contact
      $table->string('address')->nullable();
      $table->string('postcode')->nullable();
      $table->string('city')->nullable();
      $table->string('phone1')->nullable();
      $table->string('phone2')->nullable();
      $table->string('phone3')->nullable();
      $table->string('phone_member')->nullable();
      $table->string('phone1_owner')->nullable();
      $table->string('phone2_owner')->nullable();
      $table->string('phone3_owner')->nullable();
      $table->boolean('phone1_private')->nullable();
      $table->boolean('phone2_private')->nullable();
      $table->boolean('phone3_private')->nullable();
      $table->boolean('phone_member_private')->nullable();
      $table->string('email1')->nullable();
      $table->string('email2')->nullable();
      $table->string('email3')->nullable();
      $table->string('email_member')->nullable();
      // Registration
      $table->boolean('subscription_paid')->default(false);
      $table->string('last_reregistration')->nullable();
      // Leader stuff
      $table->boolean('is_leader')->default(false);
      $table->boolean('leader_in_charge')->default(false);
      $table->string('leader_name')->nullable();
      $table->text('leader_description')->nullable();
      $table->string('leader_role')->nullable();
      $table->boolean('has_picture')->nullable();
      $table->boolean('validated')->default(false);
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('year_in_section');
      $table->index('subgroup');
      $table->index('email1');
      $table->index('email2');
      $table->index('email3');
      $table->index('email_member');
      $table->index('is_leader');
      $table->index('validated');
    });
    
    // Privileges
    Schema::create('privileges', function($table) {
      $table->increments('id');
      $table->string('operation');
      $table->string('scope');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->timestamps();
      
      $table->index('member_id');
      $table->unique(array('operation', 'scope', 'member_id'));
    });
    
    // Calendar
    Schema::create('calendar_items', function($table) {
      $table->increments('id');
      $table->date('start_date');
      $table->date('end_date');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('event');
      $table->text('description');
      $table->string('type');
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('start_date');
      $table->index('end_date');
    });
    
    // News
    Schema::create('news', function($table) {
      $table->increments('id');
      $table->date('news_date');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('title');
      $table->text('content');
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('news_date');
    });
    
    // Documents
    Schema::create('documents', function($table) {
      $table->increments('id');
      $table->date('doc_date');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('title');
      $table->text('description')->default("");
      $table->string('filename');
      $table->boolean('public')->default(false);
      $table->string('archive')->default('');
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('doc_date');
    });
    
    // Links
    Schema::create('links', function($table) {
      $table->increments('id');
      $table->string('title');
      $table->string('url');
      $table->text('description');
      $table->timestamps();
    });
    
    // Test data
    DB::table('sections')->insert(array(
        'id' => 2,
        'name' => 'Louveteaux',
        'slug' => 'louveteaux',
        'position' => 2,
        'section_type' => 'L',
        'section_type_number' => 1,
        'color' => "#FF0000",
        'email' => '',
        'de_la_section' => "de la meute",
        'la_section' => "la meute",
        'last_email_content' => "",
    ));
    DB::table('users')->insert(array(
        'id' => 1,
        'password' => '963f0ec339ffa5b7dbe86993f3b2f7b3296ab046663724b30cf77964b4338102895297f5b4b',
        'username' => 'Julien',
        'email' => 'julien.dupuis@gmail.com',
        'is_webmaster' => true,
        'verified' => true,
    ));
    DB::table('members')->insert(array(
        'first_name' => "Jos",
        'last_name' => "Vandervelde",
        'birth_date' => "1980-10-10",
        'gender' => "M",
        'nationality' => "BE",
        'section_id' => 1,
        'phone1' => "123456798",
        'email_member' => "jos@vandervelde.com",
        'is_leader' => true,
        'leader_in_charge' => true,
        'leader_name' => "Koala",
        'leader_description' => "Je suis l'animateur d'unité",
        'leader_role' => "Responsable",
        'has_picture' => false,
    ));
    
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
    Schema::drop('links');
    Schema::drop('documents');
    Schema::drop('news');
    Schema::drop('calendar_items');
    Schema::drop('privileges');
    Schema::drop('members');
    Schema::drop('password_recoveries');
    Schema::drop('users');
    Schema::drop('page_images');
		Schema::drop('pages');
    Schema::drop('sections');
    Schema::drop('parameters');
	}
  
}