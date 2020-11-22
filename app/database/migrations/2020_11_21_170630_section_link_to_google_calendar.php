<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SectionLinkToGoogleCalendar extends Migration {
  
  public function up() {
    Schema::table('sections', function(Blueprint $table) {
      $table->string('google_calendar_link')->after('start_age')->nullable()->default("");
    });
  }
  
  public function down() {
    try {
      Schema::table('sections', function(Blueprint $table) {
        $table->dropColumn('google_calendar_link');
      });
    } catch (Exception $e) {}
  }
  
}
