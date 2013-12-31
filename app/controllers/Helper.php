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
  
}