<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvancedRegistrationStatus extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->string('registration_status')->after('registration_priority')->default("");
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('registration_status');
      });
    } catch (Exception $e) {}
  }
  
}
