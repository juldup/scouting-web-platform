<?php

class Parameter extends Eloquent {
  
  protected $guarded = array('id', 'created_at', 'updated_at');
  
  public static $LOGO_IMAGE_FOLDER = "site_data/website_logo/";
  
  public static $CALENDAR_DOWNLOADABLE = "Calendar downloadable";
  public static $SHOW_SECTIONS = "Show sections";
  public static $SHOW_ADDRESSES = "Show addresses";
  public static $SHOW_CONTACTS = "Show contacts";
  public static $SHOW_ANNUAL_FEAST = "Show annual feast";
  public static $SHOW_REGISTRATION = "Show registration";
  public static $REGISTRATION_ACTIVE = "Registration active";
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
  
  public static $UNIT_SHORT_NAME = "Unit short name";
  public static $UNIT_LONG_NAME = "Unit long name";
  public static $UNIT_BANK_ACCOUNT = "Unit bank account";
  public static $DOCUMENT_CATEGORIES = "Document categories";
  public static $WEBMASTER_EMAIL = "Webmaster e-mail address";
  public static $DEFAULT_EMAIL_FROM_ADDRESS = "Default e-mail from address";
  public static $LOGO_IMAGE = "Logo image";
  
  public static $PRICE_1_CHILD = "Price for one child";
  public static $PRICE_2_CHILDREN = "Price for two children";
  public static $PRICE_3_CHILDREN = "Price for three children";
  public static $PRICE_1_LEADER = "Price for one leader";
  public static $PRICE_2_LEADERS = "Price for two leaders";
  public static $PRICE_3_LEADERS = "Price for three leaders";
  
  public static $SMTP_HOST = "Smtp host";
  public static $SMTP_PORT = "Smtp port";
  public static $SMTP_USERNAME = "Smtp username";
  public static $SMTP_PASSWORD = "Smtp password";
  public static $SMTP_SECURITY  = "Smtp security";
  public static $VERIFIED_EMAIL_SENDERS = "Verified e-mail senders";
  
  private static $parameters = null;
  private static $verifiedEmailSenders = null;
  
  private static function fetchParameters() {
    $parameters = self::all();
    self::$parameters = array();
    foreach ($parameters as $parameter) {
      self::$parameters[$parameter->name] = $parameter->value;
    }
  }
  
  public static function get($parameterName) {
    if (self::$parameters == null) {
      self::fetchParameters();
    }
    if (array_key_exists($parameterName, self::$parameters)) {
      if (self::$parameters[$parameterName] == "false") return false;
      if (self::$parameters[$parameterName] == "true") return true;
      return self::$parameters[$parameterName];
    } else {
      return "";
    }
  }
  
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
  
  public static function isVerifiedSender($emailAddress) {
    if (!self::$verifiedEmailSenders) {
      self::$verifiedEmailSenders = explode(";", Parameter::get(Parameter::$VERIFIED_EMAIL_SENDERS));
    }
    return $emailAddress && in_array(strtolower($emailAddress), self::$verifiedEmailSenders);
  }
  
}