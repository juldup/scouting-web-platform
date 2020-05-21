<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAbsences extends Migration {
  
  public function up() {
    Schema::create('absences', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->integer('event_id')->unsigned()->nullable();
      $table->foreign('event_id')->references('id')->on('calendar_items')->onDelete('cascade');
      $table->text('other_event')->nullable();
      $table->text('explanation');
      $table->nullableTimestamps();
      
      $table->index('member_id');
      $table->index('event_id');
    });
  }
  
  public function down() {
    try { Schema::drop('absences'); } catch (Exception $e) {}
  }
  
}
