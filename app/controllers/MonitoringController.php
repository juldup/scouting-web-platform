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
 * This controller provides a way to see the maintenance actions that
 * should be done be the webmaster or the leaders
 */
class MonitoringController extends BaseController {
  
  /**
   * [Route] Shows the monitoring page
   */
  public function showPage() {
    // Make sure the app
    if (!$this->user->isLeader()) {
      return Helper::forbiddenResponse();
    }
    // Get status
    $emailLastExecution = Parameter::get(Parameter::$CRON_EMAIL_LAST_EXECUTION);
    $healthCardsLastExecution = Parameter::get(Parameter::$CRON_HEALTH_CARDS_LAST_EXECUTION);
    $incrementYearInSectionLastExecution = Parameter::get(Parameter::$CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION);
    $cleanUpUnverifiedAccountsLastExecution = Parameter::get(Parameter::$CRON_CLEAN_UP_UNUSED_ACCOUNTS);
    $updateElasticsearchLastExecution = Parameter::get(Parameter::$CRON_UPDATE_ELASTICSEARCH);
    // Show page
    return View::make('pages.monitoring.monitoring', array(
        "emailLastExecution" => $emailLastExecution,
        "healthCardsLastExecution" => $healthCardsLastExecution,
        "incrementYearInSectionLastExecution" => $incrementYearInSectionLastExecution,
        "cleanUpUnverifiedAccountsLastExecution" => $cleanUpUnverifiedAccountsLastExecution,
        "updateElasticsearchLastExecution" => $updateElasticsearchLastExecution,
        "emailTimedOut" => self::emailTimedOut($emailLastExecution),
        "healthCardsTimedOut" => self::healthCardsTimedOut($healthCardsLastExecution),
        "incrementYearInSectionTimedOut" => self::incrementYearInSectionTimedOut($incrementYearInSectionLastExecution),
        "updateElasticsearchTimedOut" => self::updateElasticsearchTimedOut($updateElasticsearchLastExecution),
    ));
  }
  
  /**
   * Returns true if some cron task have timed out
   */
  public static function cronTaskTimedOut() {
    $emailLastExecution = Parameter::get(Parameter::$CRON_EMAIL_LAST_EXECUTION);
    $healthCardsLastExecution = Parameter::get(Parameter::$CRON_HEALTH_CARDS_LAST_EXECUTION);
    $incrementYearInSectionLastExecution = Parameter::get(Parameter::$CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION);
    $updateElasticsearchLastExecution = Parameter::get(Parameter::$CRON_UPDATE_ELASTICSEARCH);
    return self::emailTimedOut($emailLastExecution) ||
            self::healthCardsTimedOut($healthCardsLastExecution) ||
            self::incrementYearInSectionTimedOut($incrementYearInSectionLastExecution) ||
            self::updateElasticsearchTimedOut($updateElasticsearchLastExecution);
  }
  
  /**
   * Returns true if the email cron task has timed out
   */
  private static function emailTimedOut($emailLastExecution) {
    return !$emailLastExecution || (time() - $emailLastExecution > 3600 * 3); // More than 3 hours ago
  }
  
  /**
   * Returns true if the health cards cron task has timed out
   */
  private static function healthCardsTimedOut($healthCardsLastExecution) {
    return !$healthCardsLastExecution || (time() - $healthCardsLastExecution > 3600 * 24 * 2); // More than 2 days ago
  }
  
  /**
   * Returns true if the increment year in section cron task has timed out
   */
  private static function incrementYearInSectionTimedOut($incrementYearInSectionLastExecution) {
    $year = date('m') < 8 ? date('Y') - 1 : date('Y');
    $lastAugustFirst = strtotime($year . "-08-02");
    return !$incrementYearInSectionLastExecution || $incrementYearInSectionLastExecution < $lastAugustFirst;
  }
  
  /**
   * Returns true if the update elasticsearch cron task has timed out
   */
  private static function updateElasticsearchTimedOut($updateElasticsearchLastExecution) {
    // No timeout if search is disabled
    if (!Parameter::get(Parameter::$SHOW_SEARCH)) return false;
    // Check that the update has been done in the last 26 hours
    return !$updateElasticsearchLastExecution || $updateElasticsearchLastExecution < time() - 3600 * 26;
  }
  
}
