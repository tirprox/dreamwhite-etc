<?php
namespace Dreamwhite\Assortment;
use GuzzleHttp\Client;

require_once "includes.php";

Log::enable();
ini_set("memory_limit", "2048M");
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
error_reporting(-1);

import();

if (Settings::get("fromServer")) {
	makeHeader();
	Log::writeSections();
	makeFooter();
}

function import() {
   Timers::start("overall");
   downloadTags();

   $generator = new AssortmentManager();
   $generator->generateObjects();
   //$generator->createCSVReport();
   //$generator->createXMLReport();
    updateDB();
   Timers::stop("overall");
}

function updateDB () {
    $client = new Client();
    foreach (Config::DBUPDATEURLS as $url) {
        $client->get($url);
    }
}

function downloadTags() {
    $url = 'https://docs.google.com/spreadsheets/u/1/d/1pla3J7Av8es2zj0tVUIXacRt83aMMSwTgQ8XdX0NAs8/export?format=xlsx&id=1pla3J7Av8es2zj0tVUIXacRt83aMMSwTgQ8XdX0NAs8';
    $file = file_get_contents($url);
    file_put_contents('tags_temp.xlsx', $file);

    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load('tags_temp.xlsx');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
    $writer->setDelimiter(';');
    $writer->setUseBOM(true);
    $writer->setEnclosure('"');
    $writer->save("tags/tags.csv");
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