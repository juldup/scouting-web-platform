<?php

class Parameter extends Eloquent {
  
  public static $CALENDAR_DOWNLOADABLE = "Calendar downloadable";
  
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
      return self::$parameters[$parameterName];
    } else {
      return null;
    }
  }
  
}