<?php
/**
 * Belgian Scouting Web Platform
 * Copyright (C) 2014-2023 Julien Dupuis
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

namespace App\Helpers;

class Form {
  
  private static $currentModel = null;
  
  private static function attributesToText($attributes = []) {
    $attributesText = "";
    foreach ($attributes as $key => $value) {
      if (is_int($key)) {
        $attributesText .= "$value ";
      } else {
        $attributesText .= "$key='" . Helper::rawToHTML($value) . "' ";
      }
    }
    return $attributesText;
  }
  
  private static function valueFromCurrentModel($name, $value) {
    if (!$value && self::$currentModel) {
      if (isset(self::$currentModel->$name)) {
        return self::$currentModel->$name;
      }
    }
    return $value;
  }
  
  public static function label($for, $text, $attributes = []) {
    $attributesText = self::attributesToText($attributes);
    return "<label for='$for' $attributesText>$text</label>";
  }
  
  public static function select($name, $options, $selectedValue = '', $attributes = []) {
    $selectedValue = self::valueFromCurrentModel($name, $selectedValue);
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $selectedValue = $oldInput[$name];
    }
    $attributesText = self::attributesToText($attributes);
    $selectText = "<select id='$name' name='$name' $attributesText/>";
    foreach ($options as $key => $value) {
      $selected = ($key == $selectedValue ? "selected" : "");
      $selectText .= "<option value='$key' $selected>$value</option>";
    }
    $selectText .= "</select>";
    return $selectText;
  }
  
  public static function text($name, $value = '', $attributes = []) {
    $value = self::valueFromCurrentModel($name, $value);
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $value = $oldInput[$name];
    }
    $attributesText = self::attributesToText($attributes);
    return "<input type='text' id='$name' name='$name' value='$value' $attributesText/>";
  }
  
  public static function password($name, $attributes = []) {
    $value = '';
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $value = $oldInput[$name];
    }
    $attributesText = self::attributesToText($attributes);
    return "<input type='password' id='$name' name='$name' value='$value' $attributesText/>";
  }
  
  public static function checkbox($name, $value = 1, $checked = false, $attributes = []) {
    $checked = self::valueFromCurrentModel($name, $checked);
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $checked = $oldInput[$name];
    }
    $attributesText = self::attributesToText($attributes);
    return "<input type='checkbox' id='$name' name='$name' value='$value' " . 
            ($checked ? "checked " : "") . "$attributesText/>";
  }
  
  public static function file($name, $attributes = []) {
    $attributesText = self::attributesToText($attributes);
    return "<input type='file' id='$name' name='$name' $attributesText/>";
  }
  
  public static function textarea($name, $value, $attributes = []) {
    $value = self::valueFromCurrentModel($name, $value);
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $value = $oldInput[$name];
    }
    $attributesText = self::attributesToText($attributes);
    return "<textarea id='$name' name='$name' $attributesText/>$value</textarea>";
  }
  
  public static function submit($text, $attributes = []) {
    $attributesText = self::attributesToText($attributes);
    return "<input type='submit' value='$text' $attributesText/>";
  }
  
  public static function open($parameters) {
    $newParameters = [];
    $action = "action=''";
    $enctype = "";
    foreach ($parameters as $key => $value) {
      // files
      if ($key == "files") {
        if ($value) {
          $enctype = 'enctype="multipart/form-data"';
        }
      // url/route
      } elseif ($key == "url") {
        $action = "action='" . $parameters['url'] . "'";
      } elseif ($key == "route") {
        $url = route($value);
        $action = "action='$url'";
      } else {
        $newParameters[$key] = $value;
      }
    }
    $attributesText = self::attributesToText($newParameters);
    return "<form method='post' $action $enctype $attributesText>\n"
            . "<input type='hidden' name='_token' value='" . csrf_token() . "' autocomplete='off' />";
  }
  
  public static function close() {
    return "</form>";
  }
  
  public static function hidden($name, $value = '') {
    $value = self::valueFromCurrentModel($name, $value);
    $oldInput = session("_old_input");
    if ($oldInput and array_key_exists($name, $oldInput)) {
      $value = $oldInput[$name];
    }
    return "<input type='hidden' id='$name' name='$name' " .
            ($value ? "value='$value' " : "") . "/>";
  }
  
  public static function model($model, $parameters = []) {
    self::$currentModel = $model;
    return self::open($parameters);
  }
  
  public static function button($text, $parameters = []) {
    $attributesText = self::attributesToText($parameters);
    return "<button $attributesText>$text</button>";
  }
  
}
