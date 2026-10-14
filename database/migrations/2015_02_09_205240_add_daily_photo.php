<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDailyPhoto extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('daily_photos', function(Blueprint $table) {
      $table->increments('id');
      $table->date('date');
      $table->integer('photo_id')->unsigned()->nullable();
      $table->foreign('photo_id')->references('id')->on('photos')->onDelete('set null');
      $table->nullableTimestamps();
      
      $table->index('date');
    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { Schema::drop('daily_photos'); } catch (Exception $e) {}
	}

}
