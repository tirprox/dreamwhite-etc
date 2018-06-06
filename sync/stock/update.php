<?php
namespace Dreamwhite\StockManager;
require_once "StockManager.php";
require_once "Config.php";

$stocks = json_decode(file_get_contents("https://sync.dreamwhite.ru/output/stock.json"), true);
$ids = json_decode(file_get_contents("https://sync.dreamwhite.ru/output/ids.json"), true);
$stockManager = new StockManager();


foreach ($stocks as $sku => $city) {
    $stockManager->updateStockFromCities($sku, $city);
}

foreach ($ids as $sku => $id) {
    $stockManager->update_ms_id($stockManager->skuPostIdMap[$sku], $id);
}

echo $stockManager->queriesExecuted;
