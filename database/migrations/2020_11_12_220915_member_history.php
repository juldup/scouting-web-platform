<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014  Julien Dupuis
 * 
 * This code is licensed under the GNU General Public License.
 * 
 * This is free software, and you are welcome to redistribute it
 * under under the terms of the GNU General Public License.
 * 
 * It is distributed without any warranty; without even the
 * implied warranty of merchantability or fitness for a particular
 * purpose. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MemberHistory extends Migration {
  
  public function up() {
    Schema::create('member_histories', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->integer('section_id')->unsigned()->nullable();
      $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
      $table->string('section_name_backup')->default('');
      $table->string('year')->default('');
      $table->string('subgroup')->default('');
      $table->string('role')->default('');
      $table->nullableTimestamps();
      
      $table->index('member_id');
    });
  }
  
  public function down() {
    try { Schema::drop('member_histories'); } catch (Exception $e) {}
  }
  
}
