<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/2/18
 * Time: 2:16 PM
 */

namespace Dreamwhite\Assortment;
require "includes.php";


$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@localhost:27017");

$collection = $client->tags->tags;

$data = json_decode(file_get_contents('tags.json'), true);

$filter = ['name' => 'test'];
$options = ['upsert' => true];


/*foreach ($data as $item) {
    $filter = ['name' => $item['name']];
    //$values = explode(",", $value);

    $record = [
        'name' => $item['name'],
        'attributes' => $item['realAttrs'],
        'relations' => $item['relations'],
        'seo' => $item['seo'],
    ];


    $collection->updateOne($filter, ['$set' => $record], $options);
    //$collection->updateOne($filter, ['$pull' => ['colors' => 'testColor']], $options);
}*/

//$collection->updateOne($filter, ['$set' => $update], $options);


/*$result = $collection->aggregate([
    [
        '$lookup' => [
            'from' => 'counterpartyReports',
            'localField' => 'id',
            'foreignField' => 'counterparty.id',
            'as' => 'report'
        ]
    ],
    [
        '$unwind' => '$report'
    ]
], []);*/

// db.getCollection('tags').find({"attributes.colorGroup" : {"$eq": "Бежевый", "$size" : 1}, "relations.gender" : "Женские", "relations.type" : "Пальто" })

$result = $collection->find(
    [
        'attributes.colorGroup' => 'Бежевый',
        'attributes.season' => 'Демисезонные',
        'relations.gender' => 'Женские',
        'relations.type' => 'Пальто',
    ]
);

//$result = $collection->find();


foreach ($result as $item) {
    var_dump($item);
}
