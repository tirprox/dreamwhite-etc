<?php
include("ObjectGenerator.php");
include("Timer.php");
include("Log.php");

class Importer {
   function import($isLogging) {
      if ($isLogging) {
         Log::enable();
      }
   
      Timer::start();
      $generator = new ObjectGenerator();
      $generator->generateObjects();
      //$generator->importViaAPI();
      $generator->createCSVReport();
      Timer::stop();
   }
}