<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberRoles extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->string('role')->after('subgroup')->nullable();
    });
  }
  
  public function down() {
    try { 
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('role');
      });
    } catch (Exception $e) {}
  }
  
}
