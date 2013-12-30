<?php

class Parameter extends Eloquent {
  
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
  
  private static $parameters = null;
  
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
  
}