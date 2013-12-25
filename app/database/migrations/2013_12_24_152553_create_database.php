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
      $table->string('section_type');
      $table->string('section_type_number');
      $table->string('color');
      $table->string('email');
      $table->string('de_la_section');
      $table->string('la_section');
      $table->text('last_email_content');
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
      $table->text('content');
    });
    DB::table('pages')->insert(array(
        'type' => 'accueil',
        'section_id' => 1,
        'content' => "<h1>Page d'accueil de l'unité</h1><p>Bienvenue.</p>",
    ));
    
    // Users
    Schema::create('users', function($table) {
      $table->increments('id');
      $table->string('password');
      $table->string('username');
      $table->string('email');
      $table->integer('default_section')->unsigned();
      $table->boolean('is_webmaster');
      $table->datetime('last_visit');
      $table->datetime('current_visit');
      $table->string('verification_code');
      $table->string('denial_code');
      $table->boolean('verified');
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
    
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
    Schema::drop('users');
		Schema::drop('pages');
    Schema::drop('sections');
	}
  
}