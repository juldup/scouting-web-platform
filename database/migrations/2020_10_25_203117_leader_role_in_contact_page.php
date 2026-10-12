<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LeaderRoleInContactPage extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('leader_role_in_contact_page')->after('leader_role')->default(0);
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('leader_role_in_contact_page');
      });
    } catch (Exception $e) {}
  }
  
}
