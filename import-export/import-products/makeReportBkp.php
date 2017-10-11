<?php
include("Connector.php");
include("CSVReportGenerator.php");
include("Product.php");
include("ProductVariant.php");
include("Group.php");
include("Groups.php");
include("Log.php");

$start = microtime(true);
Connector::init();
Log::enable();
$groups = new Groups();

$groups->getGroupsFromConfig();
$groups->getGroupsFromServer(Connector::$baseUrl, Connector::$context);
$groups->getGroupArray();

$storeId = "baedb9ed-de2a-11e6-7a34-5acf00087a3f"; // Садовая
$testUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?store.id=baedb9ed-de2a-11e6-7a34-5acf00087a3f&productFolder.id=cc91a970-07e7-11e6-7a69-93a700454ab8&stockMode=all";

$assortmentUrl = "https://online.moysklad.ru/api/remap/1.1/entity/assortment?limit=100&filter=productFolder=https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cc91a970-07e7-11e6-7a69-93a700454ab8";

$productsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/product/?limit=100&expand=uom,supplier&filter=pathName=";
$variantsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/variant?limit=100&expand=product.uom,product.supplier&filter=productid=";
$stocksUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?stockMode=all&limit=1000&store.id=";

foreach ($groups->groupArray as $group) {
   //Getting stocks
   $requestUrl = $stocksUrl . $storeId . "&productFolder.id=" . $group->id;
   $stocks = Connector::getRemoteObject($requestUrl);
   
   //Getting stock indexes for later stock lookup by code
   $stockCodes = [];
   foreach ($stocks->rows as $row) {
      $stockCodes[] = $row->code;
   }
   
   if(Log::isLogging()) {
      Log::d("\nStocks: \n\n");
      foreach ($stocks->rows as $thing) {
         Log::d("$thing->name: " . $thing->meta->type . ", Stock: $thing->stock\n");
      }
   }
   
   //Getting products
   $productRequestUrl = $productsUrl . urlencode($group->name);
   $products = Connector::getRemoteObject($productRequestUrl);
   foreach ($products->rows as $product) {
      $productInStocks = array_search($product->code, $stockCodes);
      $productStock = $stocks->rows[ $productInStocks ]->stock;
      
      $newProduct = new Product($product, $productStock, $group->name);
      
      CSVReportGenerator::appendCSVString($newProduct->getProductCsvRow());
      $variantRequestUrl = $variantsUrl . $newProduct->id;
      $variants = Connector::getRemoteObject($variantRequestUrl);
   
      Log::d("\nVariants of product $newProduct->name: \n\n");
      foreach ($variants->rows as $variant) {
         $variantKey = array_search($variant->code, $stockCodes);
         $variantStock = $stocks->rows[ $variantKey ]->stock;
         $newVariant = new ProductVariant($variant, $variantStock, $newProduct);
         $newProduct->variants[]=$newVariant;
         CSVReportGenerator::appendCSVString($newVariant->getCsvRow());
      }
      $group->products[] = $newProduct;
   }
}

CSVReportGenerator::openFile();
CSVReportGenerator::writeCsvHeader();
CSVReportGenerator::writeCSVToFile();

$time_elapsed_secs = microtime(true) - $start;
Log::i("\nTime spent: " . $time_elapsed_secs);



