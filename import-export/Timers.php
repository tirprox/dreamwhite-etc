<?php

include "Timer.php";

class Timers {
   static private $timers = [];
   
   static function start($name) {
      self::$timers[$name] = new Timer($name);
      self::$timers[$name]->start();
   }
   
   static function stop($name) {
      $measuredTime = self::$timers[$name]->stop();
      Log::d("Time spent on $name: $measuredTime" , "timer");
      
      unset(self::$timers[$name]) ;
   }
}