<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriorityToRegistrations extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('registration_priority')->after('registration_section_category')->default(false);
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('registration_priority');
      });
    } catch (Exception $e) {}
  }
  
}
