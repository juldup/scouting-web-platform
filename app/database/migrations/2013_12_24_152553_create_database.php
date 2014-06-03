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
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_1_CHILD, 'value' => '40,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_2_CHILDREN, 'value' => '32,25'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_3_CHILDREN, 'value' => '26,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_1_LEADER, 'value' => '40,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_2_LEADERS, 'value' => '32,25'));
    DB::table('parameters')->insert(array('name' => Parameter::$PRICE_3_LEADERS, 'value' => '26,75'));
    DB::table('parameters')->insert(array('name' => Parameter::$UNIT_BANK_ACCOUNT, 'value' => 'BE00-0000-0000-0000'));
    DB::table('parameters')->insert(array('name' => Parameter::$DOCUMENT_CATEGORIES, 'value' => 'Convocations;Documents administratifs;Informations générales;Pour les scouts'));
    
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
      $table->string('subgroup_name')->default('Équipe');
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
    ));
    
    // Pages
		Schema::create('pages', function($table) {
      $table->increments('id');
      $table->string('type');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->text('body_html')->default("");
      $table->timestamps();
      
      $table->index('type');
      $table->index('section_id');
    });
    // Default pages
    DB::table('pages')->insert(array(
        'type' => 'home',
        'section_id' => 1,
        'body_html' => "<p>Bienvenue.</p>",
    ));
    DB::table('pages')->insert(array(
        'type' => 'registration',
        'section_id' => 1,
        'body_html' => '<p><span style="font-size:20px"><strong>Comment s&#39;inscrire ?</strong></span></p><p>Pour <strong>inscrire</strong> un enfant ou un ado ne faisant pas encore partie de l&#39;unit&eacute; :</p><ul><li>Premi&egrave;rement, nous vous invitons &agrave; prendre connaissance de notre (ACCES CHARTE).</li><li>Deuxi&egrave;mement, vous devez prendre (ACCES CONTACT) avec l&#39;animateur d&#39;unit&eacute;.</li><li>Troisi&egrave;mement, vous devez remplir le (ACCES FORMULAIRE).</li><li>Finalement, vous devez verser le montant de la cotisation sur le compte de l&#39;unit&eacute; (BEXX-XXXX-XXXX-XXXX).</li></ul><p>Pour <strong>r&eacute;inscrire</strong> un scout, connectez-vous au site avec un compte valide et revenez sur cette page.<br />&nbsp;</p><p><span style="font-size:20px"><strong>Cotisation et prix</strong></span></p><p>Le scoutisme est un groupement o&ugrave; les animateurs sont b&eacute;n&eacute;voles. Malgr&eacute; cela, il vous est demand&eacute; de payer une cotisation qui couvre :</p><ul><li>L&#39;inscription dans l&#39;unit&eacute; (achat de mat&eacute;riel, financement des locaux, organisation d&#39;activit&eacute;s, etc.)</li><li>L&#39;inscription au sein de la f&eacute;d&eacute;ration scoute (revues, outils, formation des animateurs, promotion du scoutisme dans les pays d&eacute;favoris&eacute;s, etc.)</li><li>Une <a href="http://www.lesscouts.be/organiser/assurances/deux-assurances-de-base/">assurance</a> en responsabilit&eacute; civile et couvrant les accidents corporels pouvant survenir pendant nos activit&eacute;s</li></ul><p><strong>Combien dois-je payer ?</strong></p><ul><li>Le montant s&#39;&eacute;l&egrave;ve &agrave; <strong>(PRIX UN ENFANT) euros</strong> pour un enfant ((PRIX UN ANIMATEUR) euros s&#39;il est animateur).</li><li>Si vous avez deux enfants dans l&#39;unit&eacute;, vous payerez <strong>(PRIX DEUX ENFANTS) euros</strong> par enfant ((PRIX DEUX ANIMATEURS) euros par animateur).</li><li>Si vous avez trois enfants ou plus dans l&#39;unit&eacute;, le prix est de <strong>(PRIX TROIS ENFANTS) euros</strong> par enfant ((PRIX TROIS ANIMATEURS) euros par animateur).</li><li>&Agrave; ces frais s&#39;ajouteront les frais des activit&eacute;s sp&eacute;ciales, week-ends et grand camp, qui vous seront demand&eacute;s au cours de l&#39;ann&eacute;e.</li><li>Le prix ne doit jamais &ecirc;tre un frein &agrave; la participation. Si vous avez des difficult&eacute;s financi&egrave;res, n&#39;h&eacute;sitez pas &agrave; nous en parler, nous trouverons une solution ensemble.</li></ul><p><strong>Comment dois-je payer ?</strong></p><ul><li>Par virement bancaire sur le compte de l&#39;unit&eacute; : <strong>BEXX-XXXX-XXXX-XXXX</strong></li><li>Avec la mention &quot;Cotisation : NOM PR&Eacute;NOM(S)&quot;</li></ul>',
    ));
    DB::table('pages')->insert(array(
        'type' => 'help',
        'section_id' => 1,
        'body_html' => '<p><span style="font-size:18px"><strong>Naviguer sur le site</strong></span></p><p>Ce site contient deux menus compl&eacute;mentaires qui vous permettront d&#39;acc&eacute;der &agrave; l&#39;enti&egrave;ret&eacute; des pages :</p><ul><li><strong>Le menu principal :</strong>&nbsp;menu classique, permettant d&#39;acc&eacute;der &agrave; toutes les pages.</li><li><strong>Le choix de la section :</strong>&nbsp;il vous permet d&#39;adapater pratiquement toutes les pages du site &agrave; une section choisie. Gardez toujours ceci &agrave; l&#39;esprit en visitant le site, sinon vous risquez de vous sentir perdu.</li></ul><p>Toutes les pages sont toujours accessibles en deux clics via ces menus.</p><p>&nbsp;</p><p>Les sections&nbsp;<strong>Accueil</strong>&nbsp;et&nbsp;<strong>G&eacute;n&eacute;ral</strong>&nbsp;contiennent toutes les informations pratiques concernant l&#39;unit&eacute; en g&eacute;n&eacute;ral.</p><p>La section&nbsp;<strong>Animation</strong>&nbsp;contient les informations sp&eacute;cifiques &agrave; l&#39;animation dans les sections.</p><p>N&#39;oubliez pas de nous laisser vos commentaires sur le site et sur l&#39;unit&eacute; dans le livre d&#39;or. Si vous avez des suggestions, n&#39;h&eacute;sitez pas &agrave; les poster, car c&#39;est tr&egrave;s utile pour nous de savoir ce que vous attendez du site ou de l&#39;unit&eacute;.</p><p>&nbsp;</p><p><strong><span style="font-size:18px">Connexion sur le site</span></strong></p><p>Il est possible de visiter le site en tant que visiteur, sans vous connecter.</p><p>Pour avoir acc&egrave;s aux donn&eacute;es confidentielles (convocations, documents, e-mails, photos, listing, fiches sant&eacute;), il faut que vous vous connectiez avec un compte dont l&#39;adresse e-mail est dans notre listing.</p><p>Une fois que votre compte d&#39;utilisateur est cr&eacute;&eacute;, cochez la case&nbsp;<strong>m&eacute;moriser</strong>&nbsp;dans le menu de connexion en vous connectant. Ainsi, votre compte sera automatiquement charg&eacute; &agrave; chaque visite du site, sans que vous ayez &agrave; vous en occuper.</p><p>&nbsp;</p><p><span style="font-size:18px"><strong>Le vocabulaire scout</strong></span></p><p><strong>Animateur : </strong>Membre d&#39;un staff qui s&#39;occupe de l&#39;animation d&#39;une section.</p><p><strong>Animateur&nbsp;d&#39;unit&eacute; : </strong>Animateur reponsable de l&#39;unit&eacute;, member du staff d&#39;unit&eacute;.&nbsp;<em>On utilise souvent ce terme aussi pour les &eacute;quipiers d&#39;unit&eacute;.</em></p><p><strong>Animateur responsable : </strong>Animateur d&#39;un staff qui repr&eacute;sente la section. Il en est le responsable officiel aupr&egrave;s des parents, de l&#39;unit&eacute; et de la f&eacute;d&eacute;ration scoute.</p><p><strong>Anim&eacute; : </strong>(voir <strong>&laquo;&nbsp;Scout &raquo;</strong>)</p><p><strong>Assistant d&#39;unit&eacute; : </strong>(voir <strong>&laquo; &Eacute;quipier d&#39;unit&eacute; &raquo;</strong>)</p><p><strong>Baden-Powell : </strong>Fondateur du mouvement scout. Plus d&#39;informations sur&nbsp;<a href="http://fr.wikipedia.org/wiki/Robert_Baden-Powell">Wikip&eacute;dia</a>.</p><p><strong>Baladin : </strong>Scout de 6 &agrave; 8 ans inscrit dans l&#39;unit&eacute;. Les baladins forment ensemble la Ribambelle.</p><p><strong>Chef : </strong>(voir <strong>&laquo; Animateur &raquo;</strong>)</p><p><strong>CP : </strong>Capitaine de patrouille <em>ou chef de patrouille</em>. C&#39;est un des ain&eacute;s de la patrouille, il la dirige et en est reponsable. Il sert de lien entre sa patrouille et les animateurs.</p><p><strong>&Eacute;claireur : </strong>Scout de 12 &agrave; 17 ans, faisant partie d&#39;une troupe.</p><p><strong>&Eacute;quipier d&#39;unit&eacute; :&nbsp;</strong>Membre du staff d&#39;unit&eacute;, autre que l&#39;animateur d&#39;unit&eacute;.&nbsp;<em>Parfois appel&eacute; assistant d&#39;unit&eacute;.</em></p><p><strong>F&eacute;d&eacute;ration scoute : </strong>Association dont l&#39;unit&eacute; fait partie. La f&eacute;d&eacute;ration scoute est g&eacute;r&eacute;e par des professionnels, et nous fournit une panoplie d&#39;outils et d&#39;avantages. Notre f&eacute;d&eacute;ration s&#39;appelle &quot;<a href="http://www.lesscouts.be/">Les Scouts</a>&quot;.</p><p><strong>Loup rouge : </strong>Dans la meute, un loup rouge est un louveteau de premi&egrave;re ann&eacute;e.</p><p><strong>Loup fauve : </strong>Dans la meute, un loup fauve (ou orange) est un louveteau de deuxi&egrave;me ann&eacute;e.</p><p><strong>Loup jaune : </strong>Dans la meute, un loup jaune est un louveteau de troisi&egrave;me ann&eacute;e.</p><p><strong>Loup blanc : </strong>Dans la meute, un loup blanc est un louveteau de derni&egrave;re ann&eacute;e.</p><p><strong>Louveteau : </strong>Scout de 8 &agrave; 12 ans, faisant partie d&#39;une meute.</p><p><strong>Meute : </strong>Ensemble des louveteaux et leurs animateurs.</p><p><strong>Pionnier : </strong>Scout de 16 &agrave; 18 ans, membre du poste.</p><p><strong>Patrouille : </strong>Groupe d&#39;&eacute;claireurs formant ensemble une famille dans la troupe. Une patrouille compte en g&eacute;n&eacute;ral entre 6 et 10 &eacute;claireurs, et est men&eacute;e par le CP.</p><p><strong>Poste : </strong>Ensemble des pionniers et leurs animateurs.</p><p><strong>Ribambelle : </strong>Ensemble des baladins et leurs animateurs.</p><p><strong>Scout : </strong>Enfant, adolescent ou jeune inscrit dans une section de l&#39;unit&eacute;, &agrave; savoir : un baladin, un louveteau, un &eacute;claireur ou un pionnier.&nbsp;<em>Ce mot est encore souvent utilis&eacute; pour r&eacute;f&eacute;rer aux &eacute;claireurs. On utilise parfois aussi le terme &laquo; anim&eacute; &raquo;.</em></p><p><strong>Second : </strong>Louveteau qui aide le sizenier dans sa t&acirc;che de gestion de la sizaine. Il y a en g&eacute;n&eacute;ral un second par sizaine.</p><p><strong>Section : </strong>Une des subdivisions de l&#39;unit&eacute;. Les sections de l&#39;unit&eacute; sont la ribambelle, la meute, la troupe et le poste.</p><p><strong>Sizaine : </strong>Groupe de six ou sept louveteaux au sein de la meute, qui forment ensemble une famille. Chaque louveteau fait partie d&#39;une sizaine, et les sizaines sont form&eacute;es pour un an. Une sizaine comporte en outre un sizenier et un second.</p><p><strong>Sizenier : </strong>Repr&eacute;sentant d&#39;une sizaine chez les louveteau. Le sizenier est en g&eacute;n&eacute;ral un louveteau de derni&egrave;re ann&eacute;e, qui g&egrave;re sa sizaine.</p><p><strong>Staff : </strong>Ensemble des animateurs d&#39;une section, ou de toute l&#39;unit&eacute;.</p><p><strong>Totem : </strong>Nom d&#39;animal donn&eacute; &agrave; un &eacute;claireur lors de son premier camp. C&#39;est par ce nom qu&#39;on l&#39;appellera ensuite dans la troupe.</p><p><strong>Troupe : </strong>Ensemble d&#39;&eacute;claireurs et leurs animateurs.</p>',
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
      $table->text('body');
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
      $table->string('category');
      $table->string('filename');
      $table->boolean('public')->default(false);
      $table->boolean('archived')->default(false);
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('doc_date');
      $table->index('archived');
      $table->index('category');
    });
    
    // Links
    Schema::create('links', function($table) {
      $table->increments('id');
      $table->string('title');
      $table->string('url');
      $table->text('description');
      $table->timestamps();
    });
    
    // Health cards
    Schema::create('health_cards', function($table) {
      $table->increments('id');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->integer('signatory_id')->unsigned()->nullable();
      $table->foreign('signatory_id')->references('id')->on('users');
      $table->string('signatory_email')->nullable();
      $table->boolean('reminder_sent')->default(false);
      $table->date('signature_date');
      // Health information
      $table->string('contact1_name')->nullable();
      $table->string('contact1_address')->nullable();
      $table->string('contact1_phone')->nullable();
      $table->string('contact1_relationship')->nullable();
      $table->string('contact2_name')->nullable();
      $table->string('contact2_address')->nullable();
      $table->string('contact2_phone')->nullable();
      $table->string('contact2_relationship')->nullable();
      $table->string('doctor_name')->nullable();
      $table->string('doctor_address')->nullable();
      $table->string('doctor_phone')->nullable();
      $table->boolean('has_no_constrained_activities')->default(false);
      $table->text('constrained_activities_details')->nullable();
      $table->text('medical_data')->nullable();
      $table->text('medical_history')->nullable();
      $table->boolean('has_tetanus_vaccine')->default(false);
      $table->text('tetanus_vaccine_details')->nullable();
      $table->boolean('has_allergy')->default(false);
      $table->text('allergy_details')->nullable();
      $table->text('allergy_consequences')->nullable();
      $table->boolean('has_special_diet')->default(false);
      $table->text('special_diet_details')->nullable();
      $table->text('other_important_information')->nullable();
      $table->boolean('has_drugs')->default(false);
      $table->text('drugs_details')->nullable();
      $table->text('drugs_autonomy')->nullable();
      $table->text('comments')->nullable();
      $table->timestamps();
      
      $table->index('member_id');
    });
    
    // Photo albums
    Schema::create('photo_albums', function($table) {
      $table->increments('id');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('name');
      $table->integer('photo_count')->default(0);
      $table->integer('position')->default(0);
      $table->date('date')->default('0000-00-00');
      $table->boolean('archived')->default(false);
      $table->date('last_update')->default('0000-00-00');
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('photo_count');
      $table->index('archived');
      $table->index('last_update');
    });
    
    // Photos
    Schema::create('photos', function($table) {
      $table->increments('id');
      $table->integer('album_id')->unsigned();
      $table->foreign('album_id')->references('id')->on('photo_albums')->onDelete('cascade');
      $table->string('filename');
      $table->text('caption')->nullable();
      $table->integer('position')->default(0);
      $table->timestamps();
      
      $table->index('album_id');
      $table->index('position');
    });
    
    // E-mails
    Schema::create('emails', function($table) {
      $table->increments('id');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->date('date');
      $table->time('time');
      $table->string('subject');
      $table->text('body_html');
      $table->text('recipient_list');
      $table->string('sender_name')->nullable();
      $table->string('sender_email');
      $table->boolean('archived')->default(false);
      $table->boolean('deleted')->default(false);
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('date');
      $table->index('archived');
    });
    
    // E-mail attachments
    Schema::create('email_attachments', function($table) {
      $table->increments('id');
      $table->integer('email_id')->unsigned()->nullable();
      $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->string('filename');
      $table->timestamps();
      
      $table->index('email_id');
    });
    
    // Pending e-mails
    Schema::create('pending_emails', function($table) {
      $table->increments('id');
      $table->integer('section_email_id')->unsigned()->nullable();
      $table->foreign('section_email_id')->references('id')->on('emails')->onDelete('cascade');
      $table->text('raw_body')->nullable();
      $table->text('html_body')->nullable();
      $table->string('subject');
      $table->string('sender_email');
      $table->string('sender_name');
      $table->string('recipient');
      $table->integer('priority');
      $table->integer('attached_document_id')->nullable();
      $table->boolean('sent')->default(false);
      $table->integer('last_retry')->default(0);
      $table->timestamps();
      
      $table->index('created_at');
      $table->index('priority');
      $table->index('last_retry');
      $table->index('sent');
    });
    
    // Suggestions
    Schema::create('suggestions', function($table) {
      $table->increments('id');
      $table->text('body');
      $table->text('response')->nullable();
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
      $table->timestamps();
    });
    
    // Guest book
    Schema::create('guest_book_entries', function($table) {
      $table->increments('id');
      $table->text('body');
      $table->string('author');
      $table->timestamps();
    });
    
    // Accounting
    Schema::create('accounting_items', function($table) {
      $table->increments('id');
      $table->string('year');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('category_name');
      $table->date('date');
      $table->string('object');
      $table->integer('cashin_cents');
      $table->integer('cashout_cents');
      $table->integer('bankin_cents');
      $table->integer('bankout_cents');
      $table->string('comment');
      $table->string('receipt');
      $table->integer('position')->default(10000);
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('year');
      $table->index('position');
    });
    
    // Banned e-mails
    Schema::create('banned_emails', function($table) {
      $table->increments('id');
      $table->string('email');
      $table->string('ban_code');
      $table->boolean('banned')->default(false);
      $table->timestamps();
      
      $table->index('email');
      $table->index('banned');
    });
    
    // Archived leaders
    Schema::create('archived_leaders', function($table) {
      $table->increments('id');
      // Archive information
      $table->integer('member_id')->unsigned()->nullable();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
      $table->string('year');
      // Identity
      $table->string('first_name');
      $table->string('last_name');
      $table->string('gender');
      // Scout-related details
      $table->string('totem')->nullable();
      $table->string('quali')->nullable();
      $table->integer('section_id')->unsigned()->nullable();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
      // Contact
      $table->string('phone_member')->nullable();
      $table->boolean('phone_member_private')->nullable();
      $table->string('email_member')->nullable();
      // Leader stuff
      $table->boolean('leader_in_charge')->default(false);
      $table->string('leader_name')->nullable();
      $table->text('leader_description')->nullable();
      $table->string('leader_role')->nullable();
      $table->boolean('has_picture')->nullable();
      $table->string('picture_filename')->nullable();
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('year');
    });
    
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
    Schema::drop('archived_leaders');
    Schema::drop('banned_emails');
    Schema::drop('accounting_items');
    Schema::drop('guest_book_entries');
    Schema::drop('suggestions');
    Schema::drop('pending_emails');
    Schema::drop('email_attachments');
    Schema::drop('emails');
    Schema::drop('photos');
    Schema::drop('photo_albums');
    Schema::drop('health_cards');
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