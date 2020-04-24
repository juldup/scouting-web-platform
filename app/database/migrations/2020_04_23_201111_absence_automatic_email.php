<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AbsenceAutomaticEmail extends Migration {
  
  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->boolean('receive_absence_emails')->after('has_picture')->default(false);
    });
  }
  
  public function down() {
    try { 
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('receive_absence_emails');
      });
    } catch (Exception $e) {}
  }
  
}
