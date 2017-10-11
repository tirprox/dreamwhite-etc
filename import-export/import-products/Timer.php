<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 16.09.2017
 * Time: 2:38
 */

class Timer {
   static private $start;
   static private $isRunning=false;
   static function start() {
      self::$start = microtime(true);
      self::$isRunning = true;
   }
   static function stop() {
      if (self::$isRunning){
         $time_elapsed_secs = microtime(true) - self::$start;
         Log::i("\nTime spent: " . $time_elapsed_secs);
         self::$isRunning = false;
      }
      else {
         Log::d("Timer is not running so could not be stopped");
      }
      
   }
   
}