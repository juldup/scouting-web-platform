<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartAgeInSection extends Migration {
  
  public function up() {
    Schema::table('sections', function(Blueprint $table) {
      $table->integer('start_age')->after('subgroup_name')->nullable();
    });
  }
  
  public function down() {
    try {
      Schema::table('sections', function(Blueprint $table) {
        $table->dropColumn('start_age');
      });
    } catch (Exception $e) {}
  }
  
}
