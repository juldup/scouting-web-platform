<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganizationNumberToMember extends Migration {

  public function up() {
    Schema::table('members', function(Blueprint $table) {
      $table->string('organization_number')->nullable()->after('id');
    });
  }

  public function down() {
    try {
      Schema::table('members', function(Blueprint $table) {
        $table->dropColumn('organization_number');
      });
    } catch (Exception $e) {}
  }

}
