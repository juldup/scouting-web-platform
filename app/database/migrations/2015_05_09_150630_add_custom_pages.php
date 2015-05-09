<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomPages extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    Schema::table('pages', function(Blueprint $table) {
      $table->string('title')->nullable();
      $table->string('slug')->nullable();
      $table->integer('position')->nullable();
    });
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { 
      Schema::table('pages', function(Blueprint $table) {
        $table->dropColumn('position');
        $table->dropColumn('slug');
        $table->dropColumn('title');
      });
    } catch (Exception $e) {}
	}
  
}
