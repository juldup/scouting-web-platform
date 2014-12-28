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
  
}
