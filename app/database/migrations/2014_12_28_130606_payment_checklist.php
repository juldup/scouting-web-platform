<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PaymentChecklist extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    Schema::create('payment_events', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('section_id')->unsigned();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
      $table->string('year');
      $table->string('name');
      $table->timestamps();
      
      $table->index('year');
      $table->index('section_id');
    });
    
		Schema::create('payments', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('payment_events')->onDelete('cascade');
      $table->boolean('paid')->default(false);
      $table->timestamps();
      
      $table->index('event_id');
      $table->index('member_id');
    });
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try { Schema::drop('payments'); } catch (Exception $e) {}
		try { Schema::drop('payment_events'); } catch (Exception $e) {}
	}
  
}
