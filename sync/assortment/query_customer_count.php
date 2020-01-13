<?php

namespace Dreamwhite\Assortment;

require "includes.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->retaildemand;

$stores = [
  'msk' => 'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/ba03c6d8-7161-11e8-9ff4-34e80003eb04',
  'spb' => 'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/735616d5-e309-11e6-7a34-5acf000ffe76'
];

$store = 'spb';

$cursor = $collection->aggregate([
  [
    '$addFields' => [
      'date' => [
        '$dateFromString' => [
          'dateString' => '$updated',
          'format' => '%Y-%m-%d %H:%M:%S',
          'onError' => '$date'
        ]
      ]

    ],
  ],
  [
    '$group': []
  ],
  [

  ],
  [

  ]
], []);

$csv = "";
$header = 'year;month;yearOfBirth;count' . PHP_EOL;

$csv .= $header;

foreach ($cursor as $item) {
  $item = json_decode(json_encode($item), true);
  $line = implode(';', [$item['year'], $item['month'], $item['yearOfBirth'], $item['count']]) . PHP_EOL;
  $csv .= $line;
}

file_put_contents($store . "_age.csv", $csv);
