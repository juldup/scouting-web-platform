<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdvancedRegistrationFields extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->datetime('registration_date')->after('in_waiting_list')->nullable();
      $table->string('registration_siblings')->after('registration_date')->nullable();
      $table->string('registration_former_leader_child')->after('registration_siblings')->nullable();
      $table->string('registration_section_category')->after('registration_former_leader_child')->nullable();
    });
  }
  
  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('registration_date');
        $table->dropColumn('registration_siblings');
        $table->dropColumn('registration_former_leader_child');
        $table->dropColumn('registration_section_category');
      });
    } catch (Exception $e) {}
  }
  
}
