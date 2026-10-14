<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHealthCardQuestionList extends Migration {
  
  public function up() {
    Schema::table('health_cards', function(Blueprint $table) {
      $table->text('national_id')->after('signature_date')->nullable();
      $table->text('contact1_email')->after('contact1_relationship')->nullable();
      $table->text('contact1_comment')->after('contact1_email')->nullable();
      $table->text('contact2_email')->after('contact2_relationship')->nullable();
      $table->text('contact2_comment')->after('contact2_email')->nullable();
      $table->text('height')->after('doctor_phone')->nullable();
      $table->text('weight')->after('height')->nullable();
      $table->text('can_swim')->after('constrained_activities_details')->nullable();
      $table->boolean('covid_19_risk_group')->after('drugs_autonomy')->default(false);
      $table->boolean('covid_19_physician_agreement')->after('covid_19_risk_group')->default(false);
      $table->boolean('covid_19_physician_contact_information_given')->after('covid_19_physician_agreement')->default(false);
    });
  }
  
  public function down() {
    try {
      Schema::table('health_cards', function(Blueprint $table) {
        $table->dropColumn('national_id');
        $table->dropColumn('contact1_email');
        $table->dropColumn('contact1_comment');
        $table->dropColumn('contact2_email');
        $table->dropColumn('contact2_comment');
        $table->dropColumn('height');
        $table->dropColumn('weight');
        $table->dropColumn('can_swim');
        $table->dropColumn('covid_19_risk_group');
        $table->dropColumn('covid_19_physician_agreement');
        $table->dropColumn('covid_19_physician_contact_information_given');
      });
    } catch (Exception $e) {}
  }
  
}
