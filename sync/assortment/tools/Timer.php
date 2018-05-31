<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 05.12.2017
 * Time: 3:01
 */
namespace Dreamwhite\Assortment;
class Timer {
   var $name, $startTime;
   var $isRunning = false;
   
   function __construct($name) {
      $this->name = $name;
   }
   function start() {
      $this->startTime = microtime(true);
      $this->isRunning = true;
   }
   
   function stop() {
      if ($this->isRunning) {
         $time_elapsed_secs = microtime(true) - $this->startTime;
         $this->isRunning = false;
         return $time_elapsed_secs;
      }
      else {return -1;}
   }
   
   
}