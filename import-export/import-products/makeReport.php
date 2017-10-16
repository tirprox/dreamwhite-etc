<?php
include("ObjectGenerator.php");
include("Timer.php");
include("Log.php");


Log::enable();

Timer::start();
$generator = new ObjectGenerator();
$generator->generateObjects();
//$generator->importViaAPI();
$generator->createCSVReport();
$generator->createXMLReport();

Timer::stop();
