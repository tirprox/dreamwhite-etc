<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 06.09.2017
 * Time: 3:38
 */

class Log {
   private static $isLoggingEnabled = false;
   public static function d($string) {
      if(self::$isLoggingEnabled){
         print($string."<br>");
      }
   }
   
   public static function i($string) {
         print($string);
   }
   public static function enable() {
      self::$isLoggingEnabled = true;
   }
   public static function disable() {
      self::$isLoggingEnabled = false;
   }
   
   public static function isLogging() {
      return self::$isLoggingEnabled;
   }
}