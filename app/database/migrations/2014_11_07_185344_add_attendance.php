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

class AddAttendance extends Migration {
  
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Attendance
    Schema::create('attendances', function(Blueprint $table) {
      $table->increments('id');
      $table->integer('member_id')->unsigned();
      $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
      $table->integer('event_id')->unsigned();
      $table->foreign('event_id')->references('id')->on('calendar_items')->onDelete('cascade');
      $table->boolean('attended')->default(false);
      $table->timestamps();
      
      $table->index('member_id');
      $table->index('event_id');
    });
    
    // Add attendance_monitored in calendar_items table
    Schema::table('calendar_items', function(Blueprint $table) {
      $table->boolean('attendance_monitored')->default(false);
    });
	}
  
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		try {
      Schema::table('calendar_items', function(Blueprint $table) {
        $table->dropColumn('attendance_monitored');
      });
    } catch (Exception $e) {}
    try { Schema::drop('attendances'); } catch (Exception $e) {}
	}
  
}
