<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CalendarShortname extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    Schema::table('sections', function(Blueprint $table) {
      $table->string('calendar_shortname')->default('');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { 
      Schema::table('sections', function(Blueprint $table) {
        $table->dropColumn('calendar_shortname');
      });
    } catch (Exception $e) {}
	}

}
