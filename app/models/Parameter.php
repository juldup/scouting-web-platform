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
  public static $REREGISTRATION_ACTIVE = "Reregistration active";
  public static $REGISTRATION_AUTOMATIC = "Registration automatic";
  public static $REGISTRATION_START_DATE = "Registration start date";
  public static $REGISTRATION_END_DATE = "Registration end date";
  public static $GROUPED_SECTION_MENU = "Grouped section menu";
  public static $SHOW_ABSENCES = "Show absences";
  public static $SHOW_HEALTH_CARDS = "Show health cards";
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
  public static $SHOW_SEARCH = "Show search";
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
  public static $SEND_REGISTRATIONS_TO_UNIT_EMAIL_ADDRESS = "Send registrations to unit e-mail address";
  public static $ANU_DENOMINATION = "AnU denomination";
  public static $ASU_DENOMINATION = "AsU denomination";
  public static $ALLOW_PERSONAL_CONTACT = "Allow personal contact";
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
  public static $CRON_CLEAN_UP_UNUSED_ACCOUNTS = "Cron clean up unused accounts";
  public static $CRON_UPDATE_ELASTICSEARCH = "Cron update elasticsearch";
  // Registration form help texts
  public static $REGISTRATION_FORM_HELP_INTRODUCTION = "Registration form help introduction";
  public static $REGISTRATION_FORM_HELP_FILL_IN_FORM = "Registration form help fill-in form";
  public static $REGISTRATION_FORM_HELP_IDENTITY = "Registration form help identity";
  public static $REGISTRATION_FORM_HELP_FIRST_NAME = "Registration form help first name";
  public static $REGISTRATION_FORM_HELP_LAST_NAME = "Registration form help last name";
  public static $REGISTRATION_FORM_HELP_BIRTH_DATE = "Registration form help birth date";
  public static $REGISTRATION_FORM_HELP_GENDER = "Registration form help gender";
  public static $REGISTRATION_FORM_HELP_NATIONALITY = "Registration form help nationality";
  public static $REGISTRATION_FORM_HELP_ADDRESS = "Registration form help address";
  public static $REGISTRATION_FORM_HELP_ADDRESS_STREET = "Registration form help address street";
  public static $REGISTRATION_FORM_HELP_POSTCODE = "Registration form help postcode";
  public static $REGISTRATION_FORM_HELP_CITY = "Registration form help city";
  public static $REGISTRATION_FORM_HELP_CONTACT = "Registration form help contact";
  public static $REGISTRATION_FORM_HELP_PHONE = "Registration form help phone";
  public static $REGISTRATION_FORM_HELP_PHONE_MEMBER = "Registration form help phone member";
  public static $REGISTRATION_FORM_HELP_EMAIL = "Registration form help email";
  public static $REGISTRATION_FORM_HELP_EMAIL_MEMBER = "Registration form help email member";
  public static $REGISTRATION_FORM_HELP_SECTION_HEADER = "Registration form help section header";
  public static $REGISTRATION_FORM_HELP_SECTION = "Registration form help section";
  public static $REGISTRATION_FORM_HELP_TOTEM = "Registration form help totem";
  public static $REGISTRATION_FORM_HELP_QUALI = "Registration form help quali";
  public static $REGISTRATION_FORM_HELP_LEADER = "Registration form help leader";
  public static $REGISTRATION_FORM_HELP_REMARKS = "Registration form help remarks";
  public static $REGISTRATION_FORM_HELP_HANDICAP = "Registration form help handicap";
  public static $REGISTRATION_FORM_HELP_COMMENTS = "Registration form help comments";
  public static $REGISTRATION_FORM_HELP_FAMILY = "Registration form help family";
  public static $REGISTRATION_FORM_HELP_FINISH = "Registration form help finish";
  // Automatic e-mail content
  public static $AUTOMATIC_EMAIL_CONTENT_REGISTRATION_FORM_FILLED = "Automatic e-mail content registration form filled";
  public static $AUTOMATIC_EMAIL_CONTENT_REGISTRATION_VALIDATED = "Automatic e-mail content registration validated";
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
  
  /**
   * Returns whether the registrations are currently active (automatically or manually)
   */
  public static function registrationIsActive() {
    if (self::get(self::$REGISTRATION_AUTOMATIC)) {
      $currentDate = date("m-d H:i");
      $startDate = self::get(self::$REGISTRATION_START_DATE);
      $endDate = self::get(self::$REGISTRATION_END_DATE);
      if ($startDate <= $endDate) {
        return ($currentDate >= $startDate && $currentDate < $endDate);
      } else {
        return ($currentDate < $endDate || $currentDate >= $startDate);
      }
    } else {
      return self::get(self::$REGISTRATION_ACTIVE);
    }
  }
  
  public static function adaptAnUDenomination($defaultDenomination) {
    switch (Parameter::get(Parameter::$ANU_DENOMINATION)) {
      case "anu": {
        switch ($defaultDenomination) {
          case "animateur d'unité": return "AnU";
          case "Animateur d'unité": return "AnU";
          case "Animateurs d'unité": return "AnUs";
          case "l'animateur d'unité": return "l'AnU";
          case "L'animateur d'unité": return "L'AnU";
          case "animatrice d'unité": return "AnU";
          case "Animatrice d'unité": return "AnU";
          case "Animatrices d'unité": return "AnUs";
        }
      }
      case "responsable d'unité": {
        switch ($defaultDenomination) {
          case "animateur d'unité": return "responsable d'unité";
          case "Animateur d'unité": return "Responsable d'unité";
          case "Animateurs d'unité": return "Responsables d'unité";
          case "l'animateur d'unité": return "le responsable d'unité";
          case "L'animateur d'unité": return "Le responsable d'unité";
          case "animatrice d'unité": return "responsable d'unité";
          case "Animatrice d'unité": return "Responsable d'unité";
          case "Animatrices d'unité": return "Responsables d'unité";
        }
      }
      case "animateur responsable d'unité": {
        switch ($defaultDenomination) {
          case "animateur d'unité": return "animateur responsable d'unité";
          case "Animateur d'unité": return "Animateur responsable d'unité";
          case "Animateurs d'unité": return "Animateurs responsables d'unité";
          case "l'animateur d'unité": return "l'animateur responsable d'unité";
          case "L'animateur d'unité": return "L'animateur responsable d'unité";
          case "animatrice d'unité": return "animatrice responsable d'unité";
          case "Animatrice d'unité": return "Animatrice responsable d'unité";
          case "Animatrices d'unité": return "Animatrices responsables d'unité";
        }
      }
      default: {// "animateur d'unité"
        switch ($defaultDenomination) {
          case "animateur d'unité": return "animateur d'unité";
          case "Animateur d'unité": return "Animateur d'unité";
          case "Animateurs d'unité": return "Animateurs d'unité";
          case "l'animateur d'unité": return "l'animateur d'unité";
          case "L'animateur d'unité": return "L'animateur d'unité";
          case "animatrice d'unité": return "animatrice d'unité";
          case "Animatrice d'unité": return "Animatrice d'unité";
          case "Animatrices d'unité": return "Animatrices d'unité";
        }
      }
    }
    return $defaultDenomination;
  }
  
  public static function adaptAsUDenomination($defaultDenomination) {
    switch (Parameter::get(Parameter::$ASU_DENOMINATION)) {
      case "asu": {
        switch ($defaultDenomination) {
          case "Équipier d'unité": return "AsU";
          case "Équipiers d'unité": return "AsUs";
          case "Équipière d'unité": return "AsU";
          case "Équipières d'unité": return "AsUs";
          case "équipier d'unité": return "AsU";
          case "équipière d'unité": return "AsU";
        }
      }
      case "animateur d'unité": {
        switch ($defaultDenomination) {
          case "Équipier d'unité": return "Animateur d'unité";
          case "Équipiers d'unité": return "Animateurs d'unité";
          case "Équipière d'unité": return "Animatrice d'unité";
          case "Équipières d'unité": return "Animatrices d'unité";
          case "équipier d'unité": return "animateur d'unité";
          case "équipière d'unité": return "animatrice d'unité";
        }
      }
      case "assistant d'unité": {
        switch ($defaultDenomination) {
          case "Équipier d'unité": return "Assistant d'unité";
          case "Équipiers d'unité": return "Assistants d'unité";
          case "Équipière d'unité": return "Assistante d'unité";
          case "Équipières d'unité": return "Assistantes d'unité";
          case "équipier d'unité": return "assistant d'unité";
          case "équipière d'unité": return "assistante d'unité";
        }
      }
      default: {// "équipier d'unité"
        switch ($defaultDenomination) {
          case "Équipier d'unité": return "Équipier d'unité";
          case "Équipiers d'unité": return "Équipiers d'unité";
          case "Équipière d'unité": return "Équipière d'unité";
          case "Équipières d'unité": return "Équipières d'unité";
          case "équipier d'unité": return "équipier d'unité";
          case "équipière d'unité": return "équipière d'unité";
        }
      }
    }
    return $defaultDenomination;
  }
  
}
