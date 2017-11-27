<?php
include("ObjectGenerator.php");
include("Timer.php");
include("Log.php");


Log::enable();
ini_set("memory_limit", "2048M");
Timer::start();
$generator = new ObjectGenerator();
$generator->generateObjects();
//$generator->importViaAPI();
$generator->createCSVReport();
$generator->createXMLReport();

Timer::stop();
