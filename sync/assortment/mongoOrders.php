<?php 
namespace Dreamwhite\Assortment;
require "includes.php";


$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");

$collection = $client->dreamwhite->order;


$cursor = $collection->find([
    '$or' => [
        ["agent.tags" => "anketa-site"],
        ["agent.tags" => "anketa-vk"]
    ]
]
    ,['typeMap' => ['array' => 'array', 'obj' => 'array']]
);


/*$cursor = $collection->find();*/

$csv = "";

foreach ($cursor as $item) {

    $item = json_decode(json_encode($item), true);

    $line = $item['name'] .  "," .
    implode('|', $item['agent']['tags']) .  "," .
    $item['created'] .  "," .
    $item['agent']['name'] .  "," .
    $item['agent']['phone'] .  "," .
    $item['agent']['created'] . PHP_EOL;

    echo $line;

    $csv .= $line;
}

file_put_contents("orders.csv", $csv);