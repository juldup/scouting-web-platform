<?php

use Illuminate\Database\Migrations\Migration;

class CreateDatabase extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    
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
      $table->text('content_markdown')->default("");
      $table->text('content_html')->default("");
      $table->timestamps();
      
      $table->index('type');
      $table->index('section_id');
    });
    DB::table('pages')->insert(array(
        'type' => 'home',
        'section_id' => 1,
        'content_html' => "<h1>Page d'accueil de l'unité</h1><p>Bienvenue.</p>",
        'content_markdown' => "# Page d'accueil de l'unité\n\nBienvenue."
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
      $table->datetime('last_visit')->default('0000-00-00 00:00:00');
      $table->datetime('current_visit')->default('0000-00-00 00:00:00');
      $table->string('verification_code')->nullable();
      $table->string('denial_code')->nullable();
      $table->boolean('verified')->default(false);
      $table->timestamps();
      
      $table->index('username');
      $table->index('email');
      $table->index('verification_code');
      $table->index('denial_code');
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
      $table->string('phone4')->nullable();
      $table->string('phone1_owner')->nullable();
      $table->string('phone2_owner')->nullable();
      $table->string('phone3_owner')->nullable();
      $table->string('phone4_owner')->nullable();
      $table->boolean('phone1_private')->nullable();
      $table->boolean('phone2_private')->nullable();
      $table->boolean('phone3_private')->nullable();
      $table->boolean('phone4_private')->nullable();
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
      $table->boolean('has_photo')->nullable();
      $table->timestamps();
      
      $table->index('section_id');
      $table->index('year_in_section');
      $table->index('subgroup');
      $table->index('email1');
      $table->index('email2');
      $table->index('email3');
      $table->index('email_member');
      $table->index('is_leader');
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
        'password' => '',
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
        'has_photo' => false,
    ));
    
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
    Schema::drop('privileges');
    Schema::drop('members');
    Schema::drop('users');
    Schema::drop('page_images');
		Schema::drop('pages');
    Schema::drop('sections');
	}
  
}