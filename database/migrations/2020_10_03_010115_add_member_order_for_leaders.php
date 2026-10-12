<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMemberOrderForLeaders extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->integer('list_order')->after('leader_in_charge')->default(0);
    });
  }
  
  public function down() {
    try { 
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('list_order');
      });
    } catch (Exception $e) {}
  }
  
}
