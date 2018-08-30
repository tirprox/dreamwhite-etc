<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/2/18
 * Time: 2:16 PM
 */

namespace Dreamwhite\Assortment;
require "includes.php";

$mongo = new MongoTagAdapter();

$MOCK_ATTRS = [
    'colorGroup' => 'Синий',
//    'dlina' => '120см',
//    'poyas' => 'Есть',
//    'vorotnik' => 'Есть',
    'season' => 'Зимние',
//    'siluet' => 'Приталенный',
//    'kapushon' => 'Есть',
];
$MOCK_RELS = [
    'gender' => 'Женские',
    'type' => 'Пальто',
    'filterable' => 1
];

$results = [];
foreach ($mongo->find($MOCK_ATTRS, $MOCK_RELS) as $result) {
    $results[] = $result;
}

selectBestCandidate($results, count($MOCK_ATTRS), count($MOCK_RELS));


function selectBestCandidate($results, $attrCount, $relCount)
{
    foreach ($results as $item) {
        if (count($item->attributes) === $attrCount) {
            echo $item->name . PHP_EOL;
        }
    }

}