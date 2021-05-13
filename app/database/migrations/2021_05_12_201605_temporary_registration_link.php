<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TemporaryRegistrationLink extends Migration {
  
  public function up() {
    Schema::create('temporary_registration_links', function(Blueprint $table) {
      $table->increments('id');
      $table->string('code')->default('');
      $table->datetime('expiration')->nullable()->default(null);
      $table->nullableTimestamps();
      
      $table->index('code');
    });
  }
  
  public function down() {
    try { Schema::drop('temporary_registration_links'); } catch (Exception $e) {}
  }
  
}
