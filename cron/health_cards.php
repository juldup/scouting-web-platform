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

/**
 * Calls the cron job that sends reminders for the health cards and deletes them
 */

// Get base URL
$baseURL = trim(file_get_contents(dirname(__FILE__) . "/../storage/app/site_data/website-base-url.txt"));

// Call job
$contextOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
file_get_contents("$baseURL/cron/suppression-auto-fiches-sante", false, stream_context_create($contextOptions));
