<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGuestStatusToMember extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('is_guest')->after('is_leader')->default(false);
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('is_guest');
      });
    } catch (Exception $e) {}
  }
  
}
