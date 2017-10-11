<?php
include("ObjectGenerator.php");
include("Timer.php");
include("Log.php");

if (isset($_POST['run-import'])) {
   Timer::start();
   $generator = new ObjectGenerator();
   $generator->generateObjects();
//$generator->importViaAPI();
   $generator->createCSVReport();
   Timer::stop();
}
