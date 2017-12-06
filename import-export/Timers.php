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
      print("\nTime spent on $name: " . $measuredTime . "<br>");
      
      unset(self::$timers[$name]) ;
   }
}