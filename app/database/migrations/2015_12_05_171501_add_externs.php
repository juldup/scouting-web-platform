<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExterns extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('is_extern')->after('has_picture')->default(false);
    });
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { 
      Schema::table('page_images', function(Blueprint $table) {
        $table->dropColumn('is_extern');
      });
    } catch (Exception $e) {}
	}
  
}
