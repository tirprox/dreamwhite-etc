<?php
include("ObjectGenerator.php");
include("Log.php");
include(dirname(__DIR__) . "/Timers.php");

Log::enable();
ini_set("memory_limit", "2048M");

import();

if (Settings::get("fromServer")) {
	makeHeader();
	Log::writeSections();
	makeFooter();
}

function import() {
   Timers::start("overall");
   $generator = new ObjectGenerator();
   $generator->generateObjects();
   $generator->createCSVReport();
   $generator->createXMLReport();
   Timers::stop("overall");
   
}

function makeHeader() {
   ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Импорт</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
    <style>.nav-tabs{border-bottom:none}</style>
  </head>
  <body>
  <div class='container'>
    <div class='row'>
      <div class='col-sm-12'>
        <h1>Импорт товаров из Моего Склада</h1>
   <?php
}

function makeFooter() {
   $footer = "</div></div></div></body></html>";
   echo $footer;
}