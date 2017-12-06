<?php
include("ObjectGenerator.php");
include("Log.php");

include(dirname(__DIR__)."/Timers.php");

Log::enable();
ini_set("memory_limit", "2048M");
Timers::start("overall");
$generator = new ObjectGenerator();
$generator->generateObjects();
$generator->createCSVReport();
$generator->createXMLReport();
Timers::stop("overall");


