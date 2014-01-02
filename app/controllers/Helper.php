<?php

class Helper {
  
  public static function removeSpecialCharacters($string) {
    $string = preg_split('//u',$string, -1, PREG_SPLIT_NO_EMPTY);
    $newString = "";
    for ($i = 0; $i < count($string); $i++) {
      if (($string[$i] >= 'a' && $string[$i] <= 'z') ||
          ($string[$i] >= 'A' && $string[$i] <= 'Z') ||
          ($string[$i] >= '0' && $string[$i] <= '9') ||
          ($string[$i] == '-') ||
          ($string[$i] == '_') ||
          ($string[$i] == '.') ||
          ($string[$i] == '(') ||
          ($string[$i] == ')') ||
          ($string[$i] == ' ')) {
        $newString .= $string[$i];
      } else if ($string[$i] == 'é' || $string[$i] == 'è' || $string[$i] == 'ê' || $string[$i] == 'ë') $newString .= "e";
      else if ($string[$i] == 'î' || $string[$i] == 'ï') $newString .= "i";
      else if ($string[$i] == 'ô' || $string[$i] == 'ö') $newString .= "o";
      else if ($string[$i] == 'ü' || $string[$i] == 'ù' || $string[$i] == 'û') $newString .= "u";
      else if ($string[$i] == 'à' || $string[$i] == 'â') $newString .= "a";
      else if ($string[$i] == 'ç') $newString .= "c";
      else if ($string[$i] == 'É' || $string[$i] == 'È' || $string[$i] == 'Ê' || $string[$i] == 'Ë') $newString .= "E";
      else if ($string[$i] == 'Î' || $string[$i] == 'Ï') $newString .= "I";
      else if ($string[$i] == 'Ô' || $string[$i] == 'Ö') $newString .= "O";
      else if ($string[$i] == 'Ü' || $string[$i] == 'Ù' || $string[$i] == 'Û') $newString .= "U";
      else if ($string[$i] == 'À' || $string[$i] == 'Â') $newString .= "A";
      else if ($string[$i] == 'Ç') $newString .= "C";
    }
    return $newString;
  }
  
  public static function sanitizeForJavascript($string) {
    $string = addslashes($string);
    $string = str_replace("\r\n", "\\n", $string);
    $string = str_replace("\n", "\\n", $string);
    $string = str_replace("\r", "\\n", $string);
    return $string;
  }
  
  public static function rawToHTML($string) {
    return str_replace("\n", "<br />", htmlspecialchars($string));
  }
  
  public static function oneYearAgo() {
    return date('Y-m-d', time() - 365*24*3600);
  }
  
  public static function thisYear() {
    $year = date('Y');
    if (date('m') < 8) $thisYear = ($year - 1) . "-" . $year;
    else $thisYear = $year . "-" . ($year + 1);
  }
  
  public static function getDateDay($sqlDate) {
    return substr($sqlDate, 8, 2) + 0;
  }
  
  public static function getDateMonth($sqlDate) {
    return substr($sqlDate, 5, 2) + 0;
  }
  
  public static function getDateYear($sqlDate) {
    return substr($sqlDate, 0, 4);
  }
  
  // Checks if date exists and return it sql-formatted
  public static function checkAndReturnDate($year, $month, $day) {
    $date = "$year-$month-$day";
    $timestamp = strtotime($date);
    if (date('Y', $timestamp) != $year || date('m', $timestamp) != $month || date('d', $timestamp) != $day) {
      return false;
    }
    return $date;
  }
  
  public static function charAt($string, $index) {
    return substr($string, $index, 1);
  }
  
  public static function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
  }
  
  /**
   * Returns whether the given string abides by the rules of capital letters
   * if first and last names (i.e. must contain a capital letter, and a letter
   * cannot be followed by a capital letter)
   * 
   * @param type $string Must be utf8-encoded
   * @return boolean
   */
  public static function hasCorrectCapitals($string, $firstLetterMustBeCapital = true) {
    if (!$string) return;
    // Separate string in characters
    $stringArray = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    // Check if first character is a capital letter
    $firstChar = $stringArray[0];
    $hasCapital = $firstChar != mb_strtolower($firstChar);
    if ($firstLetterMustBeCapital && !$hasCapital) return false;
    $previousChar = $firstChar;
    // Iterate over characters
    for ($i = 1; $i < count($stringArray); $i++) {
      $char = $stringArray[$i];
      // Determine whether it is a capital letter
      $isCapital = $char != mb_strtolower($char);
      // Check if a capital letter is not following a letter
      if ($isCapital && !in_array($previousChar, array(" ", "-", "'", "/"))) {
        return false;
      }
      if (!$hasCapital) $hasCapital = $isCapital;
      $previousChar = $char;
    }
    return $hasCapital;
  }
  
  /**
   * Formats a phone number with pattern "0XXX/XX.XX.XX", "0XX/XX.XX.XX" or "0X/XXX.XX.XX"
   * according to http://en.wikipedia.org/wiki/Telephone_numbers_in_Belgium.
   * 
   * Returns "" if the phone number is incorrect or empty.
   */
  public static function formatPhoneNumber($originalPhoneNumber) {
    
    $phoneNumber = trim($originalPhoneNumber);
    if (!$phoneNumber) return null;
    
    // Check if the phone number is starting with a '+'
    $hasPlusSymbol = substr($phoneNumber, 0, 1) == '+';
    
    // Keep only numbers
    $phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
    
    // Check for international prefix
    if ($hasPlusSymbol && self::startsWith($phoneNumber, "32")) {
      // Belgian number, drop the international prefix
      $phoneNumber = "0" . substr($phoneNumber, 2);
    } elseif (self::startsWith ($phoneNumber, "0032")) {
      // Belgian number, drop the international prefix
      $phoneNumber = "0" . substr($phoneNumber, 4);
    } elseif ($hasPlusSymbol || self::startsWith($phoneNumber, "00")) {
      // International number, don't format it
      return $originalPhoneNumber;
    }
    
    if (!self::startsWith($phoneNumber, "0")) {
      $phoneNumber = "0" . $phoneNumber;
    }
    
    // Determine prefix length
    if (self::startsWith($phoneNumber, "0468") ||
        self::startsWith($phoneNumber, "047") ||
        self::startsWith($phoneNumber, "048") ||
        self::startsWith($phoneNumber, "049")) {
      // Mobile number
      $zonePrefixLength = 4;
      $suffixLength = 6;
    } elseif (self::startsWith($phoneNumber, "02") ||
        self::startsWith($phoneNumber, "03") ||
        self::startsWith($phoneNumber, "04") ||
        self::startsWith($phoneNumber, "09")) {
      // Big city number
      $zonePrefixLength = 2;
      $suffixLength = 7;
    } else {
      // Small city number
      $zonePrefixLength = 3;
      $suffixLength = 6;
    }
    
    // Extract prefix and suffix
    $prefix = substr($phoneNumber, 0, $zonePrefixLength);
    $suffix = substr($phoneNumber, $zonePrefixLength);
    // Suffix must be at least 6 characters
    if (strlen($suffix) != $suffixLength) return "";
    // Separate suffix in parts
    $suffix1 = substr($suffix, 0, strlen($suffix) - 4);
    $suffix2 = substr($suffix, strlen($suffix) - 4, 2);
    $suffix3 = substr($suffix, strlen($suffix) - 2, 2);
    
    $phoneNumber = "$prefix/$suffix1.$suffix2.$suffix3";
    
    return $phoneNumber;
  }
  
}