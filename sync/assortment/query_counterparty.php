<?php

namespace Dreamwhite\Assortment;

error_reporting(E_ERROR);
require "includes.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->counterparty;
$cursor = $collection->aggregate([
/*  [
    '$limit' => 5
  ],*/
  [
    '$lookup' =>[
      "from" => 'counterpartyReport',
      'localField' => 'id',
      'foreignField' => 'counterparty.id',
      'as' => 'report'
    ]
  ],
  [
    '$unwind' => '$report'
  ]
], []);

$csv = "";
//$header = 'tags;name;legalAddress;actualAddress;INN/KPP;OKPO;phone;email;BIK;Bank;KS;RS;discountCard;comment;lastName;source;anketaDate;feedback;birthday;facebook;country;city;address;index;promocode;metrica;GA;utmstat;discountAccum;corrAccum;saleAmount' . PHP_EOL;
$header = 'externalCode;saleAmount' . PHP_EOL;

$csv .= $header;

foreach ($cursor as $item) {
  $item = json_decode(json_encode($item), true);

  $data = [];

  $data[] = $item['externalcode'];
  $data[] = $item['salesamount'];

  /*$tags = $item['tags'];
  if ($tags !== null) {
    $data[] =  implode(',', $item['tags']);
  } else {
    $data[] =  "";
  }

  $data[] =  $item['name'];


  $data[] = $item['legalAddress'];
  $data[] = $item['actualAddress'];
  $data[] = $item['inn'] . '/' .  $item['kpp'];
  $data[] = $item['okpo'];

  $data[] = $item['phone'];
  $data[] = $item['email'];*/

  $line = implode(';', $data) . PHP_EOL;
  $csv .= $line;
}

file_put_contents("counterparty.csv", $csv);
