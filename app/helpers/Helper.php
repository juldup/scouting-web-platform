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
 * This class provides various helper functions
 */
class Helper {
  
  /**
   * Returns the string with normalized characters, including only a-z, A-Z, 0-9, '-', '_', '.', '(', ')' and ' '
   * @param type $string
   * @return string
   */
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
  
  /**
   * Returns the given string tranformed for use inside javascript quotes
   */
  public static function sanitizeForJavascript($string) {
    $string = addslashes($string);
    $string = str_replace("\r\n", "\\n", $string);
    $string = str_replace("\n", "\\n", $string);
    $string = str_replace("\r", "\\n", $string);
    $string = str_replace("/", "\\/", $string);
    return $string;
  }
  
  /**
   * Returns the given string transformed for safe use in html
   */
  public static function sanitizeForHTML($string) {
    $string = str_replace("<", "&lt;", $string);
    $string = str_replace(">", "&gt;", $string);
    return $string;
  }
  
  /**
   * Transforms a raw multiline text so that the line breaks will appear in HTML
   */
  public static function rawToHTML($string) {
    return str_replace("\n", "<br />", htmlspecialchars($string));
  }
  
  /**
   * Returns the 'YYYY-MM-DD' date of one year ago
   */
  public static function oneYearAgo() {
    return date('Y-m-d', time() - 365*24*3600);
  }
  
  /**
   * Returns the current 'YYYY-YYYY' scouting year (scouting year starts in August)
   */
  public static function thisYear() {
    $year = date('Y');
    if (date('m') < 8) $thisYear = ($year - 1) . "-" . $year;
    else $thisYear = $year . "-" . ($year + 1);
    return $thisYear;
  }
  
  /**
   * Returns the day of the month of a 'YYYY-MM-DD' sql date
   */
  public static function getDateDay($sqlDate) {
    return substr($sqlDate, 8, 2) + 0;
  }
  
  /**
   * Returns month of the year of a 'YYYY-MM-DD' sql date
   */
  public static function getDateMonth($sqlDate) {
    return substr($sqlDate, 5, 2) + 0;
  }
  
  /**
   * Returns the year 'YYYY-MM-DD' sql date
   */
  public static function getDateYear($sqlDate) {
    return substr($sqlDate, 0, 4);
  }
  
  /**
   * Checks if the given date is valid and returns it sql-formatted.
   * Returns false if invalid
   */
  public static function checkAndReturnDate($year, $month, $day) {
    $date = "$year-$month-$day";
    $timestamp = strtotime($date);
    if (date('Y', $timestamp) != $year || date('m', $timestamp) != $month || date('d', $timestamp) != $day) {
      return false;
    }
    return $date;
  }
  
  /**
   * Transforms a 'YYYY-MM-DD' sql date in D/M/YYYY' format
   */
  public static function dateToHuman($sqlDate) {
    if ($sqlDate == "0000-00-00" || $sqlDate == "0" || !$sqlDate) return "";
    return date('j/n/Y', strtotime($sqlDate));
  }
  
  /**
   * Transforms a "00:00" time in "00h00" format
   */
  public static function timeToHuman($time) {
    $parts = explode(":", $time);
    return $parts[0] . "h" . $parts[1];
  }
  
  /**
   * Returns the character at the given index in $string
   */
  public static function charAt($string, $index) {
    return substr($string, $index, 1);
  }
  
  /**
   * Returns whether $haystack starts with $needle
   */
  public static function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
  }
  
  /**
   * Returns whether the given string abides by the rules of capital letters
   * for first and last names (i.e. must contain a capital letter, and a letter
   * cannot be followed by a capital letter)
   * 
   * Some valid names: 'Jean', 'Marie-Cécile', 'Ruysman', 'De Pauw', 'Le blanc'
   * Some invalid names: 'jean', MArie', 'RuysMan', 'DePauw', 'le Blanc' (unless $firstLetterMustBeCapital is false)
   * 
   * @param string $string  The string (must be utf8-encoded)
   * @param type $firstLetterMustBeCapital  If true, the first letter must be a capital letter
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
   * Formats a phone number with pattern "0XXX XX XX XX", "0XX XX XX XX" or "0X XXX XX XX"
   * according to http://en.wikipedia.org/wiki/Telephone_numbers_in_Belgium.
   * If the phone number is international, it is returned unchanged.
   * 
   * Returns "" if the phone number is incorrect or empty.
   */
  public static function formatPhoneNumber($originalPhoneNumber) {
    // Trim phone number
    $phoneNumber = trim($originalPhoneNumber);
    if (!$phoneNumber) return "";
    // Check if the phone number is starting with a '+'
    $hasPlusSymbol = strpos(preg_replace('/[^0-9\+]+/', '', $phoneNumber), "+") === 0;
    // Keep only numbers
    $phoneNumber = preg_replace('/[^0-9]+/', '', $phoneNumber);
    // Check for international prefix
    if ($hasPlusSymbol && self::startsWith($phoneNumber, "32")) {
      // Belgian number, drop the international prefix
      $phoneNumber = substr($phoneNumber, 2);
      // Add a leading zero if there is none
      if (strpos($phoneNumber, "0") !== 0) $phoneNumber = "0" . $phoneNumber;
    } elseif (self::startsWith ($phoneNumber, "0032")) {
      // Belgian number, drop the international prefix
      $phoneNumber = substr($phoneNumber, 4);
      // Add a leading zero if there is none
      if (strpos($phoneNumber, "0") !== 0) $phoneNumber = "0" . $phoneNumber;
    } elseif ($hasPlusSymbol || self::startsWith($phoneNumber, "00")) {
      // International number, don't format it
      return $originalPhoneNumber;
    }
    // Add leading zero
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
    // Return phone number
    $phoneNumber = "$prefix $suffix1 $suffix2 $suffix3";
    return $phoneNumber;
  }
  
  /**
   * Returns a forbidden 'access denied' HTTP response
   */
  public static function forbiddenResponse() {
    return Illuminate\Http\Response::create(View::make('forbidden'), Illuminate\Http\Response::HTTP_FORBIDDEN);
  }
  
  /**
   * Returns a forbidden 'you are not a member' HTTP response
   */
  public static function forbiddenNotMemberResponse() {
    return Illuminate\Http\Response::create(View::make('forbiddenNotMember'), Illuminate\Http\Response::HTTP_FORBIDDEN);
  }
  
  /**
   * Creates a slug for the given string
   */
  public static function slugify($text) {
    // Based on: http://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string#2955878
    // Remove special characters
    $text = self::removeSpecialCharacters($text);
    // Replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
    // Trim
    $text = trim($text, '-');
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Lowercase
    $text = strtolower($text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    if (empty($text)) {
      throw new Exception("Could not slugify text");
    }
    return $text;
  }
  
  /**
   * Returns the given cash amount (float) in format '0,00'
   */
  public static function formatCashAmount($amount) {
    $amount = trim($amount);
    $amount = str_replace(",", ".", $amount);
    $amount = (float)$amount;
    $integerPart = (int)$amount;
    $decimalPart = (int)round(($amount - $integerPart) * 100);
    return $integerPart . "," . ($decimalPart >= 10 ? "" : "0") . $decimalPart;
  }
  
  /**
   * Returns whether the given e-mail address is in the listing
   */
  public static function emailIsInListing($email) {
    $member = Member::where(function($query) use ($email) {
      $query->where('email1', '=', $email);
      $query->orWhere('email2', '=', $email);
      $query->orWhere('email3', '=', $email);
      $query->orWhere('email_member', '=', $email);
    })->where('validated', '=', true)
            ->first();
    return $member != null;
  }
  
  /**
   * Renders an e-mail and returns an array with two elements: 'html' and 'txt'
   * for the html and the raw versions of the e-mail's body
   * 
   * @param type $templateName  The template's file name (should be in both views/emails/html and views/emails/txt} folders)
   * @param type $recipientEmail  The e-mail address of the recipient to generate ban code
   * @param array $parameters  The parameters that will be transfered to the templates
   */
  public static function renderEmail($templateName, $recipientEmail, $parameters) {
    $parameters['ban_email_code'] = BannedEmail::getCodeForEmail($recipientEmail);
    $parameters['email_is_in_listing'] = self::emailIsInListing($recipientEmail);
    return array(
        "html" => View::make("emails.html.$templateName", $parameters)->render(),
        "txt" => View::make("emails.txt.$templateName", $parameters)->render(),
    );
  }
  
  /**
   * Outputs a big file. Returns whether it was successful.
   */
  static function outputBigFile($path, $filename) {
    // Send header
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . filesize($path));
    ob_clean();
    flush();
    // Output file chunk by chunk
    $chunksize = 1*(1024*1024);
    $buffer = '';
    $handle = fopen($path, 'rb');
    if (!$handle) {
      return false;
    }
    while (!feof($handle)) {
      $buffer = fread($handle, $chunksize);
      echo $buffer;
      ob_flush();
      flush();
    }
    $status = fclose($handle);
    return $status;
  }
  
}
