<?php

class DateHelper {
  
  /**
   * Returns the distance between the $fromTime and $toTime (or now) in words in French
   */
  static function distanceofTimeInWords($fromTime, $toTime = null, $includeSeconds = false) {
    $toTime = $toTime ? $toTime : time();
    $distanceInMinutes = floor(abs($toTime - $fromTime) / 60);
    $distanceInSeconds = floor(abs($toTime - $fromTime));
    
    $string = "";
    
    if ($distanceInMinutes <= 1) {
      if (!$includeSeconds) {
        $string = $distanceInSeconds <= 44 ? "moins d'une minute" : "1 minute";
      } else {
        if ($distanceInSeconds <= 5) {
          $string = "5 secondes";
        } else if ($distanceInSeconds >= 6 && $distanceInSeconds <= 10) {
          $string = "10 secondes";
        } else if ($distanceInSeconds >= 11 && $distanceInSeconds <= 20) {
          $string = "20 secondes";
        } else if ($distanceInSeconds >= 21 && $distanceInSeconds <= 40) {
          $string = "30 secondes";
        } else if ($distanceInSeconds >= 41 && $distanceInSeconds <= 59) {
          $string = "moins d'une minute";
        } else {
          $string = '1 minute';
        }
      }
    } else if ($distanceInMinutes >= 2 && $distanceInMinutes <= 44) {
      $string = $distanceInMinutes . " minutes";
    } else if ($distanceInMinutes >= 45 && $distanceInMinutes <= 89) {
      $string = '1 heure';
    } else if ($distanceInMinutes >= 90 && $distanceInMinutes <= 1439) {
      $string = round($distanceInMinutes / 60) . " heures";
    } else if ($distanceInMinutes >= 1440 && $distanceInMinutes <= 2879) {
      $string = '1 jour'; 
    } else if ($distanceInMinutes >= 2880 && $distanceInMinutes <= 43199) {
      $string = round($distanceInMinutes / 1440) . " jours";
    } else if ($distanceInMinutes >= 43200 && $distanceInMinutes <= 86399) {
      $string = '1 mois';
    } else if ($distanceInMinutes >= 86400 && $distanceInMinutes <= 525959) {
      $string = round($distanceInMinutes / 43200) . " mois";
    } else if ($distanceInMinutes >= 525960 && $distanceInMinutes <= 1051919) {
      $string = '1 an';
    } else {
      $string = floor($distanceInMinutes / 525960) . " ans";
    }
    
    return "Il y a " . $string;
  }
  
  public static function checkMMDDHHMMFormat($date) {
    if (strlen($date) != 11) return false;
    if (substr($date,2,1) != "-") return false;
    if (substr($date,5,1) != " ") return false;
    if (substr($date,8,1) != ":") return false;
    if (!is_numeric(substr($date, 0,2))) return false;
    if (!is_numeric(substr($date, 3,2))) return false;
    if (!is_numeric(substr($date, 6,2))) return false;
    if (!is_numeric(substr($date, 9,2))) return false;
    if (intval(substr($date, 0,2)) < 1 || intval(substr($date, 0,2)) > 12) return false;
    if (intval(substr($date, 3,2)) < 1 || intval(substr($date, 3,2)) > 31) return false;
    if (intval(substr($date, 6,2)) < 0 || intval(substr($date, 6,2)) > 23) return false;
    if (intval(substr($date, 9,2)) < 0 || intval(substr($date, 9,2)) > 59) return false;
    return true;
  }
  
  /**
   * Checks whether a string matches a valid mysql datetime (YYYY-MM-DD hh:mm:ss)
   */
  public static function verifyMysqlDatetime($datetime) { 
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $datetime, $matches)) {
      if (checkdate($matches[2], $matches[3], $matches[1])) {
        return true;
      }
    }
    return false;
  }
  
  /**
   * Returns whether the given time is between startDate end endDate.
   * All three times must be in "MM-DD hh:mm" format.
   */
  public static function isWithinDateRange($date, $startDate, $endDate) {
    if ($startDate <= $endDate) {
      return ($date >= $startDate && $date < $endDate);
    } else {
      return ($date < $endDate || $date >= $startDate);
    }
  }
  
  /**
   * Returns the 'YYYY-YYYY' string representation of the previous scouting year.
   * The next year is considered as starting on July 1st.
   */
  public static function getLastYearForArchiving() {
    $month = date('m');
    $startYear = date('Y') - 1;
    if ($month <= 6) $startYear--;
    return $startYear . "-" . ($startYear + 1);
  }
  
}
