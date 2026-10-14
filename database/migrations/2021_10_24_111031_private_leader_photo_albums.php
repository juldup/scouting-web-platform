<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivateLeaderPhotoAlbums extends Migration {
  
  public function up() {
    Schema::table('photo_albums', function(Blueprint $table) {
      $table->boolean('leaders_only')->after('date')->default(false);
    });
  }
  
  public function down() {
    try {
      Schema::table('photo_albums', function(Blueprint $table) {
        $table->dropColumn('leaders_only');
      });
    } catch (Exception $e) {}
  }
  
}
