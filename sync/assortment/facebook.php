<?php

namespace Dreamwhite\Assortment;

require "includes.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->counterpartyReport;

$cursor = $collection->find([
    'averagereceipt' => [
      '$gt' => 0
    ],
    'counterparty.companytype' => 'individual',
    'phone' => [
      '$ne' => ''
    ],
    'lastdemanddate' => [
      '$ne' => ''
    ],
  ]
  , ['typeMap' => ['array' => 'array', 'obj' => 'array'],
    'sort' => [
      'lastdemanddate' => -1
    ]
  ]
);

/*$cursor = $collection->find();*/

$csv = "";

$pattern = '/[^0-9]/m';
$gen = 'F';
$event_name = 'Purchase';
$currency = 'RUB';

$format = 'Y-m-d H:i:s';
$format2 = 'Y-m-d';

$header = 'email;phone;gen;event_name;event_time;value;currency' . PHP_EOL;

$csv .= $header;

foreach ($cursor as $item) {
  $item = json_decode(json_encode($item), true);

  $email = $item['counterparty']['email'];
  $phone = preg_replace($pattern, '', $item['counterparty']['phone']);

  $d = \DateTime::createFromFormat($format, $item['lastdemanddate']);
  $date = $d->format($format2);
  $value = $item['averagereceipt'] / 100;

  $line = implode(';', [$email, $phone, $gen, $event_name, $date, $value, $currency]) . PHP_EOL;
  /*$line = $email . ";" .
    $phone . ";" .
    $gen . ";" .
    $event_name . ";" .
    $date . ";" .
    $value . ';' .
    $currency . PHP_EOL;*/

  //echo $line;

  $csv .= $line;
}

file_put_contents("facebook.txt", $csv);

header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="facebook.txt"');
readfile('facebook.txt');