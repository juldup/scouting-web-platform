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
 * This Eloquent class represents a global parameter of the website.
 * It provides functions to access the parameters.
 * 
 * Columns:
 *   - name:  Name of the parameter
 *   - value: Value of the parameter
 */
class Parameter extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  
  // Folder (relative to storage folder) where the website logo is stored
  public static $LOGO_IMAGE_FOLDER = "site_data/website_logo/";
  public static $ICON_IMAGE_FOLDER = "site_data/website_icon/";
  
  /**
   *  Parameter names
   */
  // Page access parameters
  public static $CALENDAR_DOWNLOADABLE = "Calendar downloadable";
  public static $SHOW_SECTIONS = "Show sections";
  public static $SHOW_ADDRESSES = "Show addresses";
  public static $SHOW_CONTACTS = "Show contacts";
  public static $SHOW_ANNUAL_FEAST = "Show annual feast";
  public static $SHOW_REGISTRATION = "Show registration";
  public static $REGISTRATION_ACTIVE = "Registration active";
  public static $SHOW_HEALTH_CARDS = "Show health cards";
  public static $SHOW_PARENTAL_AUTHORIZATIONS = "Show parental authorizations";
  public static $SHOW_UNIT_POLICY = "Show unit policy";
  public static $SHOW_UNIFORMS = "Show uniforms";
  public static $SHOW_LINKS = "Show links";
  public static $SHOW_NEWS = "Show news";
  public static $SHOW_CALENDAR = "Show calendar";
  public static $SHOW_DOCUMENTS = "Show documents";
  public static $SHOW_EMAILS = "Show e-mails";
  public static $SHOW_PHOTOS = "Show photos";
  public static $SHOW_LEADERS = "Show leaders";
  public static $SHOW_LISTING = "Show listing";
  public static $SHOW_SUGGESTIONS = "Show suggestions";
  public static $SHOW_GUEST_BOOK = "Show guest book";
  public static $SHOW_HELP = "Show help";
  public static $SHOW_DAILY_PHOTOS = "Show daily photos";
  public static $PHOTOS_PUBLIC = "Photos public";
  // Unit specific parameters
  public static $UNIT_SHORT_NAME = "Unit short name";
  public static $UNIT_LONG_NAME = "Unit long name";
  public static $UNIT_BANK_ACCOUNT = "Unit bank account";
  public static $DOCUMENT_CATEGORIES = "Document categories";
  public static $WEBMASTER_EMAIL = "Webmaster e-mail address";
  public static $DEFAULT_EMAIL_FROM_ADDRESS = "Default e-mail from address";
  public static $LOGO_IMAGE = "Logo image";
  public static $LOGO_TWO_LINES = "Logo displayed on two lines";
  public static $ICON_IMAGE = "Icon image";
  // Website metadata
  public static $BOOTSTRAPPING_DONE = "Website bootstrapping done";
  public static $WEBSITE_META_DESCRIPTION = "Website meta description";
  public static $WEBSITE_META_KEYWORDS = "Website meta keywords";
  public static $ADDITIONAL_HEAD_HTML = "Additional head html";
  public static $FACEBOOK_APP_ID = "Facebook app id";
  public static $ADDITIONAL_CSS = "Additional CSS";
  public static $ADDITIONAL_CSS_BUFFER = "Additional CSS buffer";
  // Subscription fees
  public static $PRICE_1_CHILD = "Price for one child";
  public static $PRICE_2_CHILDREN = "Price for two children";
  public static $PRICE_3_CHILDREN = "Price for three children";
  public static $PRICE_1_LEADER = "Price for one leader";
  public static $PRICE_2_LEADERS = "Price for two leaders";
  public static $PRICE_3_LEADERS = "Price for three leaders";
  // SMTP parameters
  public static $SMTP_HOST = "Smtp host";
  public static $SMTP_PORT = "Smtp port";
  public static $SMTP_USERNAME = "Smtp username";
  public static $SMTP_PASSWORD = "Smtp password";
  public static $SMTP_SECURITY  = "Smtp security";
  public static $VERIFIED_EMAIL_SENDERS = "Verified e-mail senders";
  // Cron jobs status
  public static $CRON_EMAIL_LAST_EXECUTION = "Cron e-mail last execution";
  public static $CRON_HEALTH_CARDS_LAST_EXECUTION = "Cron health cards last execution";
  public static $CRON_INCREMENT_YEAR_IN_SECTION_LAST_EXECUTION = "Cron increment year in section last execution";
  
  // List of parameters, stored to avoid multiple accesses to database
  private static $parameters = null;
  
  // List of verified e-mail senders
  private static $verifiedEmailSenders = null;
  
  /**
   * Fetches the parameters in the database and stores them in the $parameters variable
   */
  private static function fetchParameters() {
    $parameters = self::all();
    self::$parameters = array();
    foreach ($parameters as $parameter) {
      self::$parameters[$parameter->name] = $parameter->value;
    }
  }
  
  /**
   * Returns the value of the given parameter.
   * If the parameter is not set, returns "".
   */
  public static function get($parameterName, $formatBoolean = true) {
    if (self::$parameters == null) {
      self::fetchParameters();
    }
    if (array_key_exists($parameterName, self::$parameters)) {
      if ($formatBoolean) {
        if (self::$parameters[$parameterName] == "false") return false;
        if (self::$parameters[$parameterName] == "true") return true;
      }
      return self::$parameters[$parameterName];
    } else {
      return "";
    }
  }
  
  /**
   * Updates the value of a parameter in the database
   */
  public static function set($parameterName, $value) {
    // Get parameter
    $parameter = Parameter::where('name', '=', $parameterName)->first();
    if (!$parameter) {
      // Parameter does not exist, create one
      $parameter = new Parameter();
      $parameter->name = $parameterName;
    }
    // Update parameter value
    $parameter->value = $value;
    $parameter->save();
    // Update fetched parameters
    if (self::$parameters != null) {
      self::$parameters[$parameterName] = $value;
    }
  }
  
  /**
   * Returns whether the given e-mail address in the verified sender list
   */
  public static function isVerifiedSender($emailAddress) {
    if (!self::$verifiedEmailSenders) {
      self::$verifiedEmailSenders = explode(";", Parameter::get(Parameter::$VERIFIED_EMAIL_SENDERS));
    }
    return $emailAddress && in_array(strtolower($emailAddress), self::$verifiedEmailSenders);
  }
  
}
