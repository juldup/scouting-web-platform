<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWaitingListToRegistration extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('in_waiting_list')->after('validated')->default(false);
    });
  }
  
  public function down() {
    try { 
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('in_waiting_list');
      });
    } catch (Exception $e) {}
  }
  
}
