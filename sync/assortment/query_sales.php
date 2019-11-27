<?php

namespace Dreamwhite\Assortment;

require "includes.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->retaildemand;

$cursor = $collection->aggregate([
  [
    '$lookup' => [
      'from' => 'counterparty',
      'localField' => 'agent.id',
      'foreignField' => 'id',
      'as' => 'counterparty'
    ]
  ],
  [
    '$unwind' => '$counterparty'
  ],
  [
    '$unwind' => '$counterparty.attributes'
  ],
  [
    '$match' => [
      'counterparty.attributes.name' => 'ClientID Metrica',
    ],
  ],
  [
    '$project' => [
      'name' => 1,
      'moment' => 1,
      'store' => '$retailstore.meta.href',
      'counterparty' => '$counterparty.name',
      'clientid' => '$counterparty.attributes.value',
      'cashsum' => 1,
      'nocashsum' => 1,
      '_id' => 0
    ]

  ],

], []);

$csv = "";
$header = 'number;moment;store;counterparty;clientid;total' . PHP_EOL;

$csv .= $header;

$stores = [
  'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/ba03c6d8-7161-11e8-9ff4-34e80003eb04' => 'Арма',
 'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/735616d5-e309-11e6-7a34-5acf000ffe76' => 'Флигель',
];

foreach ($cursor as $item) {
  $item = json_decode(json_encode($item), true);
  $item['store'] = $stores[$item['store'] ];

  $total = $item['cashsum'] + $item['nocashsum'];

  $line = implode(';', [$item['name'], $item['moment'], $item['store'], $item['counterparty'], $item['clientid'], $total ]) . PHP_EOL;
  $csv .= $line;
}

file_put_contents("sales.csv", $csv);
