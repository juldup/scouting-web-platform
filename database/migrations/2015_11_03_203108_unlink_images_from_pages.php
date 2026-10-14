<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UnlinkImagesFromPages extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    Schema::table('page_images', function(Blueprint $table) {
      $table->dropForeign('page_images_page_id_foreign');
      $table->dropColumn('page_id');
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
        $table->integer('page_id')->unsigned();
        $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
      });
    } catch (Exception $e) {}
	}
  
}
