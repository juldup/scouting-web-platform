<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrivatePages extends Migration {
  
  public function up() {
    Schema::table('pages', function(Blueprint $table) {
      $table->boolean('leaders_only')->after('body_html')->default(false);
    });
  }
  
  public function down() {
    try { 
      Schema::table('pages', function(Blueprint $table) {
        $table->dropColumn('leaders_only');
      });
    } catch (Exception $e) {}
  }
  
}
