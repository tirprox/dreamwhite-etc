<?php

namespace Dreamwhite\Assortment;

require "includes.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->retaildemand;

$stores = [
  'msk' => 'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/ba03c6d8-7161-11e8-9ff4-34e80003eb04',
  'spb' =>'https://online.moysklad.ru/api/remap/1.1/entity/retailstore/735616d5-e309-11e6-7a34-5acf000ffe76'
];

$store = 'spb';

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
      'retailstore.meta.href' => $stores[$store],
      'counterparty.attributes.name' => 'Дата рождения',
    ],
  ],
  [
    '$project' => [
      'year' => [
        '$year' => [
          '$dateFromString' => [
            'dateString' => '$counterparty.attributes.value'
          ]
        ]
      ],
      'updated' => 1,
      '_id' => 0
    ]

  ],
  [
    '$group' => [
      '_id' => [
        'year' => [
          '$substr' => ['$updated', 0, 4]
        ],
        'month' => [
          '$substr' => ['$updated', 5, 2]
        ],
        'yearOfBirth' => '$year'
      ],
      'count' => ['$sum' => 1]
    ]
  ],
  [
    '$project' => [
      'year' => '$_id.year',
      'month' => '$_id.month',
      'yearOfBirth' => '$_id.yearOfBirth',
      'count' => 1,
      '_id' => 0
    ]

  ],
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
