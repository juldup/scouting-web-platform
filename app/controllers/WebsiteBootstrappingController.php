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
 * This controller manages all the step the initialize the website when a new instance
 * is created.
 */
class WebsiteBootstrappingController extends Controller {
  
  
  
  /**
   * [Route] Show the bootstrapping welcome page or a step page if the bootstrapping
   * has already been started
   */
  public function showPage() {
    // Get current step
    $step = self::getCurrentBootstrappingStep();
    if ($step) {
      // Bootstrapping has been started previously, go directly to current step
      return Redirect::route('bootstrapping-step', array("step" => $step, "db_safe" => Input::get('db_safe')));
    } else {
      // Show welcome page
      return $this->step0();
    }
  }
  
  /**
   * Returns the current bootstrapping step (if any, 0 otherwise)
   */
  private static function getCurrentBootstrappingStep() {
    $bootstrappingStep = __DIR__ . "/../storage/site_data/bootstrapping-step.txt";
    if (file_exists($bootstrappingStep)) {
      try {
        return file_get_contents($bootstrappingStep);
      } catch (Exception $e) {}
    }
    return 0;
  }
  
  /**
   * Saves the bootstrapping step to the filesystem
   */
  private static function setCurrentBootstrappingStep($step) {
    $bootstrappingStep = __DIR__ . "/../storage/site_data/bootstrapping-step.txt";
    try {
      file_put_contents($bootstrappingStep, $step);
    } catch (Exception $e) {}
  }
  
  /**
   * [Route] Shows the page of the given step
   */
  public function showStep($step) {
    // Save step
    self::setCurrentBootstrappingStep($step);
    // Call corresponding function
    return call_user_func(array($this, "step$step"));
  }
  
  /**
   * Step 0: welcome page
   */
  private function step0() {
    return View::make('pages.bootstrapping.step0');
  }
  
  /**
   * Step 1: Make sure we have write access to the filesystem
   */
  public function step1() {
    // Folder of the site_data
    $siteDataRoot = dirname(__DIR__) . "/storage";
    $success = false;
    try {
      // Make sure the root folder exists
      if (!file_exists($siteDataRoot)) {
        mkdir($siteDataRoot, 777, true);
      }
      // Check that the bootstrapping step file is writable and readable
      touch("$siteDataRoot/site_data/bootstrapping-step.txt");
      file_get_contents("$siteDataRoot/site_data/bootstrapping-step.txt");
      // Make sure the root folder is writable
      touch("$siteDataRoot/site_data/test");
      unlink("$siteDataRoot/site_data/test");
      // Make sure the other folders are writable
      foreach (array('site_data/database', 'site_data/documents', 'site_data/email_attachments',
          'site_data/leader_pictures', 'site_data/website_logo', 'site_data/photos', 'site_data/images',
          'site_data/images/pages', 'cache', 'logs', 'meta', 'sessions', 'views') as $folder) {
        if (!file_exists("$siteDataRoot/$folder")) mkdir("$siteDataRoot/$folder", 0777, true);
        touch("$siteDataRoot/$folder/test");
        file_get_contents("$siteDataRoot/$folder/test");
        unlink("$siteDataRoot/$folder/test");
      }
      // Make sure the log file is writable
      touch("$siteDataRoot/logs/laravel.log");
      file_get_contents("$siteDataRoot/logs/laravel.log");
      // Make sure the services.json file is readable
      touch("$siteDataRoot/meta/services.json");
      file_get_contents("$siteDataRoot/meta/services.json");
      // Check passed
      $success = true;
    } catch (Exception $e) {}
    // Make view
    return View::make('pages.bootstrapping.step1', array(
        'success' => $success,
        'directory_path' => $siteDataRoot,
    ));
  }
  
  /**
   * Step 2: Configure and initialize database
   */
  public function step2() {
    // Database configuration file path
    $databaseConfigFilePath = __DIR__ . "/../storage/site_data/database/database-config.txt";
    // Create folder to contain the database configuration files
    if (!file_exists(dirname($databaseConfigFilePath))) {
      mkdir(dirname($databaseConfigFilePath), 0777, true);
    }
    // Create sqlite file in case sqlite will be used
    if (!file_exists(__DIR__ . '/../storage/site_data/database/database.sqlite')) {
      touch(__DIR__ . '/../storage/site_data/database/database.sqlite');
    }
    // Save post data (if any)
    if (Request::isMethod('post')) {
      // Get input data
      $databaseData = array(
          'driver' => Input::get('driver'),
          'host' => Input::get('host'),
          'database' => Input::get('database'),
          'username' => Input::get('username'),
          'password' => Input::get('password')
      );
      // Save data to file in json format
      file_put_contents($databaseConfigFilePath, json_encode($databaseData));
      // Redirect to 'GET' route
      return Redirect::route('bootstrapping-step', array('step' => 2));
    }
    // Check if a database file already exists
    $databaseExists = false;
    $databaseConfig = array();
    $databaseConfigError = Input::get('db_safe') ? true : false;
    // Get existing database configuration
    if (file_exists($databaseConfigFilePath)) {
      // Get database configuration from config file
      $fileContent = file_get_contents($databaseConfigFilePath);
      $databaseConfig = json_decode($fileContent, true);
      // Test the database configuration to make sure it is correct
      if (!Input::get('db_safe')) { // In db_safe mode, the database is wrongly configured and no access to it will be made
        try {
          // Count tables, if there are more than 25 tables, the database has been configured successfully
          $tableCount = count(DB::select("SHOW TABLES"));
          $tablesCreated = $tableCount >= 25;
          if ($tablesCreated) {
            // The database is successfully configured
            $databaseExists = true;
          } else {
            // The table does not contain any/enough tables, try installing it
            CreateDatabase::dropAllTables();
            Artisan::call('migrate');
            // Check installation
            $tableCount = count(DB::select("SHOW TABLES"));
            $tablesCreated = $tableCount > 10;
            if ($tablesCreated) {
              $databaseExists = true;
            }
          }
        } catch (Exception $ex) {
          // In case of error
          $databaseExists = false;
          $databaseConfigError = true;
        }
      }
    }
    // Make view
    return View::make('pages.bootstrapping.step2', array(
        'database_exists' => $databaseExists && !Input::get('reset'),
        'database_config_error' => $databaseConfigError,
        'driver' => array_key_exists('driver', $databaseConfig) ? $databaseConfig['driver'] : 'mysql',
        'host' => array_key_exists('host', $databaseConfig) ? $databaseConfig['host'] : '',
        'database' => array_key_exists('database', $databaseConfig) ? $databaseConfig['database'] : '',
        'username' => array_key_exists('username', $databaseConfig) ? $databaseConfig['username'] : '',
        'password' => '',
    ));
  }
  
}
