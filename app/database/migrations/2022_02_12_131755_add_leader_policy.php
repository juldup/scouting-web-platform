<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeaderPolicy extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('leader_policy_signed')->after('is_leader')->default(false);
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('loader_policy_signed');
      });
    } catch (Exception $e) {}
  }
  
}
