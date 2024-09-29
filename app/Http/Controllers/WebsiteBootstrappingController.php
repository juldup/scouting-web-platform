<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use App\Helpers\CalendarPDF;
use App\Helpers\DateHelper;
use App\Helpers\ElasticsearchHelper;
use App\Helpers\EnvelopsPDF;
use App\Helpers\Form;
use App\Helpers\HealthCardPDF;
use App\Helpers\Helper;
use App\Helpers\ListingComparison;
use App\Helpers\ListingPDF;
use App\Helpers\Resizer;
use App\Helpers\ScoutMailer;
use App\Models\Absence;
use App\Models\AccountingItem;
use App\Models\AccountingLock;
use App\Models\ArchivedLeader;
use App\Models\Attendance;
use App\Models\BannedEmail;
use App\Models\CalendarItem;
use App\Models\Comment;
use App\Models\DailyPhoto;
use App\Models\Document;
use App\Models\Email;
use App\Models\EmailAttachment;
use App\Models\GuestBookEntry;
use App\Models\HealthCard;
use App\Models\Link;
use App\Models\LogEntry;
use App\Models\Member;
use App\Models\MemberHistory;
use App\Models\News;
use App\Models\Page;
use App\Models\PageImage;
use App\Models\Parameter;
use App\Models\PasswordRecovery;
use App\Models\Payment;
use App\Models\PaymentEvent;
use App\Models\PendingEmail;
use App\Models\Photo;
use App\Models\PhotoAlbum;
use App\Models\Privilege;
use App\Models\Section;
use App\Models\Suggestion;
use App\Models\TemporaryRegistrationLink;
use App\Models\User;

/**
 * This controller manages all the step the initialize the website when a new instance
 * is created.
 */
class WebsiteBootstrappingController extends Controller {
  
  /**
   * [Route] Show the bootstrapping welcome page or a step page if the bootstrapping
   * has already been started
   */
  public function showPage(Request $request) {
    // Check access
    if (!self::accessToBootstrappingPagesAllowed()) {
      return redirect(URL::route('home'));
    }
    // Get current step
    $step = self::getCurrentBootstrappingStep();
    if ($step) {
      // Bootstrapping has been started previously, go directly to current step
      return redirect()->route('bootstrapping_step', array("step" => $step, "db_safe" => $request->input('db_safe')));
    } else {
      // Show welcome page
      return $this->step0();
    }
  }
  
  /**
   * [Route] Shows the page of the given step
   */
  public function showStep($step) {
    // Check access
    if (!self::accessToBootstrappingPagesAllowed()) {
      return redirect(URL::route('home'));
    }
    // Save step
    self::setCurrentBootstrappingStep($step);
    // Call corresponding function
    return call_user_func(array($this, "step$step"));
  }
  
  /**
   * Check whether accessing the bootstrapping pages is still allowed
   * (i.e. if the last step has not been reached yet)
   */
  private static function accessToBootstrappingPagesAllowed() {
    try {
      $bootstrappingDone = Parameter::get(Parameter::$BOOTSTRAPPING_DONE);
      return !$bootstrappingDone;
    } catch (Exception $ex) {}
    return true;
  }
  
  /**
   * Returns the current bootstrapping step (if any, 0 otherwise)
   */
  private static function getCurrentBootstrappingStep() {
    $bootstrappingStep = __DIR__ . "/../storage/app/site_data/bootstrapping-step.txt";
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
    $bootstrappingStep = __DIR__ . "/../storage/app/site_data/bootstrapping-step.txt";
    try {
      file_put_contents($bootstrappingStep, $step);
    } catch (Exception $e) {}
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
    $siteDataRoot = dirname(__DIR__) . "/storage/app";
    $success = false;
    try {
      // Make sure the root folder exists
      if (!file_exists($siteDataRoot)) {
        mkdir($siteDataRoot, 777, true);
      }
      // Check that the bootstrapping step file is writable and readable
      touch("$siteDataRoot/site_data/bootstrapping-step.txt");
      file_get_contents("$siteDataRoot/site_data/bootstrapping-step.txt");
      // Check that the website base url file is writable and readable
      touch("$siteDataRoot/site_data/website-base-url.txt");
      file_get_contents("$siteDataRoot/site_data/website-base-url.txt");
      // Make sure the root folder is writable
      touch("$siteDataRoot/site_data/test");
      unlink("$siteDataRoot/site_data/test");
      // Make sure the other folders are writable
      foreach (array('site_data/database', 'site_data/documents', 'site_data/email_attachments',
          'site_data/leader_pictures', 'site_data/website_logo', 'site_data/photos', 'site_data/images',
          'site_data/images/pages', 'cache', 'logs', 'meta', 'sessions', 'views', 'tmp') as $folder) {
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
  public function step2(Request $request) {
    // Database configuration file path
    $databaseConfigFilePath = __DIR__ . "/../storage/app/site_data/database/database-config.txt";
    // Create folder to contain the database configuration files
    if (!file_exists(dirname($databaseConfigFilePath))) {
      mkdir(dirname($databaseConfigFilePath), 0777, true);
    }
    // Create sqlite file in case sqlite will be used
    if (!file_exists(__DIR__ . '/../storage/app/site_data/database/database.sqlite')) {
      touch(__DIR__ . '/../storage/app/site_data/database/database.sqlite');
    }
    // Save post data (if any)
    if (Request::isMethod('post')) {
      // Get input data
      $databaseData = array(
          'driver' => $request->input('driver'),
          'host' => $request->input('host'),
          'database' => $request->input('database'),
          'username' => $request->input('username'),
          'password' => $request->input('password')
      );
      // Save data to file in json format
      file_put_contents($databaseConfigFilePath, json_encode($databaseData));
      // Redirect to 'GET' route
      return redirect()->route('bootstrapping_step', array('step' => 2));
    }
    // Check if a database file already exists
    $databaseExists = false;
    $databaseConfig = array();
    $databaseConfigError = $request->input('db_safe') ? true : false;
    // Get existing database configuration
    if (file_exists($databaseConfigFilePath)) {
      // Get database configuration from config file
      $fileContent = file_get_contents($databaseConfigFilePath);
      $databaseConfig = json_decode($fileContent, true);
      // Test the database configuration to make sure it is correct
      if (!$request->input('db_safe')) { // In db_safe mode, the database is wrongly configured and no access to it will be made
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
          Log::error($ex);
          // In case of error
          $databaseExists = false;
          $databaseConfigError = true;
        }
      }
    }
    // Make view
    return View::make('pages.bootstrapping.step2', array(
        'database_exists' => $databaseExists && !$request->input('reset'),
        'database_config_error' => $databaseConfigError,
        'driver' => array_key_exists('driver', $databaseConfig) ? $databaseConfig['driver'] : 'mysql',
        'host' => array_key_exists('host', $databaseConfig) ? $databaseConfig['host'] : '',
        'database' => array_key_exists('database', $databaseConfig) ? $databaseConfig['database'] : '',
        'username' => array_key_exists('username', $databaseConfig) ? $databaseConfig['username'] : '',
        'password' => '',
    ));
  }
  
  /**
   * Step 3: Creating cron jobs
   */
  public function step3() {
    // Save website URL to file
    $baseURL = URL::to('');
    file_put_contents(__DIR__ . "/../storage/app/site_data/website-base-url.txt", $baseURL);
    // Make view
    return View::make('pages.bootstrapping.step3', array(
        'cron_tasks_created' => false,
    ));
  }
  
  /**
   * Step 4: Create a user account for the webmaster
   */
  public function step4(Request $request) {
    $errorMessage = "";
    // Check if there is already a webmaster for the website
    $existingWebmaster = User::where('is_webmaster', '=', true)->first();
    if (!$existingWebmaster) {
      if (Request::isMethod('post')) {
        // Get input data
        $username = $request->input('username');
        $email = strtolower($request->input('email'));
        $password = $request->input('password');
        // Validate data
        $validator = Validator::make(
                array(
                    "username" => $username,
                    "email" => $email,
                    "password" => $password,
                ),
                array(
                    "username" => "required|unique:users,username",
                    "email" => "required|email",
                    "password" => "required|min:6",
                ),
                array(
                    "username.required" => "Veuillez entrer un nom d'utilisateur.",
                    "username.unique" => "Ce nom d'utilisateur est déjà utilisé. Choisissez-en un autre.",
                    "email.required" => "Veuillez entrer une adresse e-mail.",
                    "email.email" => "L'adresse e-mail n'est pas valide.",
                    "password.required" => "Veuillez entrer un mot de passe.",
                    "password.min" => "Le mot de passe doit compter au moins 6 caractères.",
                )
        );
        if ($validator->fails()) {
          return redirect(URL::current())
                  ->withInput()
                  ->withErrors($validator);
        }
        // Create user
        $user = User::createWith($username, $email, $password);
        $user->is_webmaster = true;
        $user->verified = true;
        $user->save();
        // Save webmaster e-mail
        Parameter::set(Parameter::$WEBMASTER_EMAIL, $email);
        // Log in as webmaster
        Session::put('user_id', $user->id);
        // Move to next step
        return redirect()->route('bootstrapping_step', array('step' => 5));
      }
      // Construct error message
      $errors = Session::get('errors');
      if ($errors) {
        foreach ($errors->getMessages() as $messageArray) {
          foreach ($messageArray as $message) {
            $errorMessage .= $message . " ";
          }
        }
      }
    }
    // Make view
    return View::make('pages.bootstrapping.step4', array(
        'error_message' => $errorMessage,
        'existing_webmaster' => $existingWebmaster,
    ));
  }
  
  /**
   * Step 5: E-mail sending configuration
   */
  public function step5(Request $request) {
    if (Request::isMethod('post')) {
      if ($request->input('action') == 'configuration') {
        // Posting configuration data
        // Save data
        $error = false;
        try {
          Parameter::set(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS, $request->input('default_email_from_address'));
          Parameter::set(Parameter::$SMTP_HOST, $request->input('smtp_host'));
          Parameter::set(Parameter::$SMTP_PORT, $request->input('smtp_port'));
          Parameter::set(Parameter::$SMTP_USERNAME, $request->input('smtp_username'));
          Parameter::set(Parameter::$SMTP_PASSWORD, $request->input('smtp_password'));
          Parameter::set(Parameter::$SMTP_SECURITY, $request->input('smtp_security'));
        } catch (Exception $e) {
          $error = true;
        }
        // Save verified e-mail sender list
        $verifiedSendersArray = $request->input('email_safe_list');
        $verifiedSenders = "";
        foreach ($verifiedSendersArray as $verifiedSender) {
          if ($verifiedSender && strpos($verifiedSenders, ";") === false) {
            if ($verifiedSenders) $verifiedSenders .= ";";
            $verifiedSenders .= strtolower($verifiedSender);
          }
        }
        try {
          Parameter::set(Parameter::$VERIFIED_EMAIL_SENDERS, $verifiedSenders);
        } catch (Exception $e) {
          $error = true;
        }
        // Redirect
        if (!$error) {
          return redirect()->route('bootstrapping_step', array('step' => 5, 'action' => 'testing'));
        } else {
          return redirect(URL::current())
                  ->with('error_message', "Une erreur est survenue. Les données n'ont pas pu être sauvées.");
        }
      } elseif ($request->input('action') == 'testing') {
        // Posting e-mail address for testing
        // Check e-mail address
        $email = $request->input('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
          return redirect(URL::route('bootstrapping_step', array('step' => 5, 'action' => 'testing')))
                  ->with('error_message', "Cette adresse e-mail n'est pas valide");
        }
        // Create e-mail
        $emailContent = Helper::renderEmail('personalEmail', $email, array(
            'message_body' => "Bravo ! Le service d'envoi d'e-mails a été configuré correctement.\n\nL'envoi des e-mails est tout à fait fonctionnel.",
            'header_text' => "Cet e-mail a été envoyé depuis le site " . URL::to(''),
        ));
        $pendingEmail = PendingEmail::create(array(
            'subject' => "E-mail de test " . date('Y-m-d H:i:s'),
            'raw_body' => $emailContent['txt'],
            'html_body' => $emailContent['html'],
            'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
            'sender_name' => "Site scout",
            'recipient' => $email,
            'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
        ));
        // Try sending e-mail
        try {
          $pendingEmail->send();
          // Remove e-mail from database
          $pendingEmail->delete();
          if ($pendingEmail->sent) {
            // Success
            return redirect(URL::route('bootstrapping_step', array('step' => 5, 'action' => 'testing')))
                    ->with('success_message', "L'e-mail a été envoyé avec succès. Vérifiez que vous l'avez bien reçu avant de passer à l'étape 6.");
          }
        } catch (Exception $e) {
          // Remove e-mail from database
          $pendingEmail->delete();
        }
        // Error
        return redirect(URL::route('bootstrapping_step', array('step' => 5, 'action' => 'testing')))
                    ->with('error_message', "L'e-mail n'a pas été envoyé. La configuration n'est pas correcte.");
      }
    }
    // Make view
    return View::make('pages.bootstrapping.step5', array(
        'configuration' => $request->input('action') != 'testing',
        'testing' => $request->input('action') == 'testing',
        'safe_emails' => explode(",", Parameter::get(Parameter::$VERIFIED_EMAIL_SENDERS)),
        'error_message' => Session::get('error_message'),
        'success_message' => Session::get('success_message'),
    ));
  }
  
  /**
   * Step 6: Configuring unit information
   */
  public function step6(Request $request) {
    $error = false;
    $success = false;
    // Save parameters from input
    if (Request::isMethod('post')) {
      // Save new prices
      try {
        Parameter::set(Parameter::$PRICE_1_CHILD, Helper::formatCashAmount($request->input('price_1_child')));
        Parameter::set(Parameter::$PRICE_1_LEADER, Helper::formatCashAmount($request->input('price_1_leader')));
        Parameter::set(Parameter::$PRICE_2_CHILDREN, Helper::formatCashAmount($request->input('price_2_children')));
        Parameter::set(Parameter::$PRICE_2_LEADERS, Helper::formatCashAmount($request->input('price_2_leaders')));
        Parameter::set(Parameter::$PRICE_3_CHILDREN, Helper::formatCashAmount($request->input('price_3_children')));
        Parameter::set(Parameter::$PRICE_3_LEADERS, Helper::formatCashAmount($request->input('price_3_leaders')));
      } catch (Exception $e) {
        $error = true;
      }
      // Save the unit parameters
      try {
        Parameter::set(Parameter::$UNIT_LONG_NAME, $request->input('unit_long_name'));
        Parameter::set(Parameter::$UNIT_SHORT_NAME, $request->input('unit_short_name'));
        Parameter::set(Parameter::$UNIT_BANK_ACCOUNT, $request->input('unit_bank_account'));
      } catch (Exception $e) {
        $error = true;
      }
      // Save the logo
      $logoFile = $request->file('logo');
      try {
        if ($logoFile) {
          $filename = $logoFile->getClientOriginalName();
          $logoFile->move(storage_path() . "/" . Parameter::$LOGO_IMAGE_FOLDER, $filename);
          Parameter::set(Parameter::$LOGO_IMAGE, $filename);
        }
      } catch (Exception $e) {
        $error = true;
      }
      // Save the logo on two lines option
      try {
        Parameter::set(Parameter::$LOGO_TWO_LINES, $request->input('logo_two_lines') ? true : false);
      } catch (Exception $ex) {
        $error = true;
      }
      // Save the search engine parameters
      try {
        Parameter::set(Parameter::$WEBSITE_META_DESCRIPTION, $request->input('website_meta_description'));
        Parameter::set(Parameter::$WEBSITE_META_KEYWORDS, $request->input('website_meta_keywords'));
      } catch (Exception $ex) {
        $error = true;
      }
      if (!$error) $success = true;
    }
    // Make view
    return View::make('pages.bootstrapping.step6', array(
        'success' => $success,
        'error' => $error,
    ));
  }
  
  /**
   * Step 7: create sections
   */
  public function step7(Request $request) {
    // Input data
    if (Request::isMethod('post')) {
      try {
        Section::where('id', '!=', 1)->delete();
        $sectionData = json_decode($request->input('data'), true);
        // Create each section
        foreach ($sectionData as $data) {
          $section = new Section();
          $section->name = $data['name'];
          $section->slug = Helper::slugify($data['name']); // TODO manage collisions
          $section->email = $data['email'];
          $section->section_category = $data['category'];
          $section->section_type = strlen($data['code'] >= 1) ? substr($data['code'], 0, 1) : '';
          $section->section_type_number = strlen($data['code'] >= 2) ? substr($data['code'], 1) : '';
          $section->color = $data['color'];
          $section->la_section = $data['la_section'];
          $section->de_la_section = $data['de_la_section'];
          $section->subgroup_name = $data['subgroup'];
          $section->save();
          $section->position = $section->id;
          $section->save();
        }
        // Success, go to next step
        return redirect(URL::route('bootstrapping_step', array('step' => 8)));
      } catch (Exception $e) {
        Log::error($e);
        // Error, show error message
        return redirect(URL::route('bootstrapping_step', array('step' => 7)))
              ->with('error_message', "Une erreur s'est produite");
      }
    }
    // Make view
    return View::make('pages.bootstrapping.step7', array(
        'error_message' => Session::get('error_message'),
    ));
  }
  
  /**
   * Step 8: Final step with instructions to go on
   */
  public function step8() {
    // Mark the website as operational
    Parameter::set(Parameter::$BOOTSTRAPPING_DONE, true);
    // Send information by e-mail to the webmaster
    $emailContent = Helper::renderEmail('pureHtmlEmail', Parameter::get(Parameter::$WEBMASTER_EMAIL), array(
        'html_body' => View::make('pages.bootstrapping.site-information')->render(),
    ));
    $email = PendingEmail::create(array(
        'subject' => "[Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME) . "] Informations sur la gestion du site",
        'html_body' => $emailContent['html'],
        'raw_body' => $emailContent['txt'],
        'sender_email' => Parameter::get(Parameter::$DEFAULT_EMAIL_FROM_ADDRESS),
        'sender_name' => "Site " . Parameter::get(Parameter::$UNIT_SHORT_NAME),
        'recipient' => Parameter::get(Parameter::$WEBMASTER_EMAIL),
        'priority' => PendingEmail::$PERSONAL_EMAIL_PRIORITY,
    ));
    try {
      $email->send();
    } catch (Exception $e) {}
    // Make view
    return View::make('pages.bootstrapping.step8', array(
        
    ));
  }
  
}
