<?php

namespace Dreamwhite\Assortment;
require "includes.php";

use MongoDB\Client;

$user = "admin";
$pwd = '6h8s4ksoq';

$localClient = new Client("mongodb://${user}:${pwd}@localhost:27017");
$localCollection = $localClient->selectDatabase('tags')->selectCollection('tags');

$remoteClient = new Client("mongodb://${user}:${pwd}@dreamwhite.ru:27017");
$remoteCollection = $remoteClient->selectDatabase('tags')->selectCollection('tag-test');

$remoteCollection->drop();

$data = $localCollection->find();

$items = [];

foreach ($data as $item) {
    $items[]=$item;
}

$remoteCollection->insertMany($items);





