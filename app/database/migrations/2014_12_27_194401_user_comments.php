<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserComments extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Comments
    Schema::create('comments', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('user_id')->unsigned()->nullable();
      $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
      $table->integer('referent_id')->unsigned();
      $table->string('referent_type');
      $table->text('body');
      $table->timestamps();
      
      $table->index('referent_id');
      $table->index('referent_type');
    });
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { Schema::drop('comments'); } catch (Exception $e) {}
	}
  
}
