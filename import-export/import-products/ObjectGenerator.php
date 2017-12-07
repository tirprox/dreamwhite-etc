<?php
include(dirname(__DIR__)."/Connector.php");
include("CSVReportGenerator.php");
include("XMLReportGenerator.php");
include("Product.php");
include("ProductVariant.php");
include("Group.php");
include("Groups.php");
include("StockManager.php");
include("WooCommerceImporter.php");

include "CSVTagFactory.php";
include "Tools.php";

use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class ObjectGenerator {
   
   public $storeId = "baedb9ed-de2a-11e6-7a34-5acf00087a3f"; // Садовая
   public $testUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?store.id=baedb9ed-de2a-11e6-7a34-5acf00087a3f&productFolder.id=cc91a970-07e7-11e6-7a69-93a700454ab8&stockMode=all";
   public $assortmentUrl = "https://online.moysklad.ru/api/remap/1.1/entity/assortment?limit=100&filter=productFolder=https://online.moysklad.ru/api/remap/1.1/entity/productfolder/cc91a970-07e7-11e6-7a69-93a700454ab8";
   public $productsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/product/?limit=100&expand=uom,supplier&filter=pathName=";
	//public $productsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/product/?limit=100&expand=uom,supplier&productFolder=";
   public $variantsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/variant?limit=100&expand=product.uom,product.supplier&filter=productid=";
   public $stocksUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?stockMode=all&limit=1000&store.id=";
   
   public $groups = null;
   
   public $productRequestUrl;
   public $imageDirPath = "http://static.dreamwhite.ru/photo/dir.php";
   
   
   function generateObjects() {
      Timers::start("groups");
      Connector::init();
      
      Tools::$imageDirList = json_decode(file_get_contents($this->imageDirPath));
      $count = count(Tools::$imageDirList);
      for($i=0;$i<$count;$i++) {
	      Tools::$imageDirList[$i] = str_replace("\0", "", Tools::$imageDirList[$i]);
      }
      
      $this->groups = new Groups();
      
      $this->groups->getGroupsFromConfig();
      $this->groups->getGroupsFromServer(Connector::$baseUrl, Connector::$context);
      $this->groups->getGroupArray();
      Timers::stop("groups");
      $client = new Client();
      $promises = [];
   
      Timers::start("group stocks");
      foreach ($this->groups->groupArray as $group) {
         $requestUrl = $this->stocksUrl . $group->storeId . "&productFolder.id=" . $group->id;
         Log::d($requestUrl);
         $promise = Connector::requestAsync($requestUrl);
         $promise->then(
            function (ResponseInterface $res) use ($group) {
               $stocksForGroup = json_decode($res->getBody());
               while (property_exists($stocksForGroup->meta, "nextHref")) {
                  $tempObject = Connector::getRemoteObject2($stocksForGroup->meta->nextHref);
                  //var_dump($tempObject);
                  //$tempObject = json_decode(file_get_contents($stocksForGroup->meta->nextHref, false, Connector::$context));
                  $stocksForGroup->meta = $tempObject->meta;
                  $stocksForGroup->rows = array_merge($stocksForGroup->rows, $tempObject->rows);
               }
               
               $group->stocks = $stocksForGroup;
               $stockCodes = [];
               foreach ($stocksForGroup->rows as $row) {
                  $stockCodes[] = $row->code;
               }
               if (Log::isLogging()) {
                  Log::d("\nStocks: \n\n");
                  foreach ($stocksForGroup->rows as $thing) {
                     Log::d("$thing->name: " . $thing->meta->type . ", Stock: $thing->stock\n");
                  }
               }
               $group->stockCodes = $stockCodes;
            },
            function (RequestException $e) {
               echo $e->getMessage() . "\n";
               echo $e->getRequest()->getMethod();
            }
         );
         Connector::addPromise($promise);
      }
      
      Connector::completeRequests();
      Timers::stop("group stocks");
      
      foreach ($this->groups->groupArray as $group) {
         $stockCodes = $group->stockCodes;
         $stocks = $group->stocks;
         
         //Getting products
         $productRequestUrl = $this->productsUrl . urlencode($group->pathName);
	      //$productRequestUrl = $this->productsUrl . "\"" . $group->url . "\"";
	      //$productRequestUrl = $this->productsUrl . $group->id;
         Log::d($productRequestUrl);
         $products = Connector::getRemoteObject($productRequestUrl);
         
         foreach ($products->rows as $product) {
            $productInStocks = array_search($product->code, $stockCodes);
            $productStock = $stocks->rows[ $productInStocks ]->stock;
            $newProduct = new Product($product, $productStock, $group->name);
            
            $variantRequestUrl = $this->variantsUrl . $newProduct->id;
            
            $promise = $client->requestAsync('GET', $variantRequestUrl,
               [
                  'auth'           => [Connector::$username, Connector::$password],
                  'stream_context' => [
                     'ssl' => [
                        'allow_self_signed' => true
                     ],
                  ],
                  'verify'         => false,
               ]);
            
            // Нужно переписать для использования клиента guzzle вместо file_get_contents
            $promise->then(
               function (ResponseInterface $res) use ($newProduct, $stocks, $stockCodes) {
                  $variants = json_decode($res->getBody());
                  while (property_exists($variants->meta, "nextHref")) {
                     Log::d("Retrieving variants synchronously");
                     $tempObject = json_decode(file_get_contents($variants->meta->nextHref, false, Connector::$context));
                     $variants->meta = $tempObject->meta;
                     $variants->rows = array_merge($variants->rows, $tempObject->rows);
                  }
                  $newProduct = $this->addVariantsToProduct($variants, $newProduct, $stocks, $stockCodes);
               },
               function (RequestException $e) {
                  echo $e->getMessage() . "\n";
                  echo $e->getRequest()->getMethod();
               }
            );
            $promises[] = $promise;

            $group->products[] = $newProduct;
         }
         
      }
      Promise\settle($promises)->wait();
      Timers::start("tags");
      $this->setTags();
      Timers::stop("tags");
      Timers::start("sql query stock updates");
      $this->updateStock();
      Timers::stop("sql query stock updates");
   }
   
   function setTags() {
      $tagFactory = new CSVTagFactory();
      $tagFactory->loadTagsFromFile();
      foreach ($this->groups->groupArray as $group) {
         foreach ($group->products as $product) {
            $tagFactory->setProductTag($product);
         }
      }
      
      $header = "<?php class TagRewriteRules {\nstatic \$rules = [\n";
      $footer = "\n];\n}";
      $tagRewriteRules = "";
      foreach ($tagFactory->tags as $tag) {
         if ($tag->hasColors && !empty($tag->realColors)) {
            $tagName = strtolower(Tools::transliterate($tag->name));
            $colorList = implode(",", $tag->realColors);
   
            $tagRewriteRules .= "\"" . $tagName . "\"" . " => "
               . "\"" . $colorList . "\"" . ",\n";
            
         }
      }
      $file = $header . $tagRewriteRules . $footer;
      file_put_contents("TagRewriteRules.php", $file);
      require_once("TagRewriteRules.php");
      //flush_rewrite_rules();
   }
   
   function updateStock() {
      $stockManager = new StockManager();
      foreach ($this->groups->groupArray as $group) {
         foreach ($group->products as $product) {
            foreach ($product->variants as $variant) {
               $stockManager->update_stock($variant->code, $variant->stock);
            }
            $stockManager->update_stock($product->code, $product->stock);
         }
      }
      $stockManager->update_stock_status();
   }
   
   function addVariantsToProduct($variants, $product, $stocks, $stockCodes) {
      Log::d("\nVariants of product $product->name: \n\n");
      foreach ($variants->rows as $variant) {
         $variantKey = array_search($variant->code, $stockCodes);
         $variantStock = $stocks->rows[ $variantKey ]->stock;
         $newVariant = new ProductVariant($variant, $variantStock, $product);
         $product->variants[] = $newVariant;
      }
      return $product;
   }
   
   function createCSVReport() {
      CSVReportGenerator::openFile();
      
      foreach ($this->groups->groupArray as $group) {
         foreach ($group->products as $product) {
            CSVReportGenerator::appendCSVString($product->getProductCsvRow());
            foreach ($product->variants as $variant) {
               CSVReportGenerator::appendCSVString($variant->getCsvRow());
            }
         }
      }
      CSVReportGenerator::writeCsvHeader();
      CSVReportGenerator::writeCSVToFile();
   }
   
   function createXMLReport() {
      
      XMLReportGenerator::createDocument();
      foreach ($this->groups->groupArray as $group) {
         foreach ($group->products as $product) {
            $xmlProductNode = XMLReportGenerator::addProduct($product);
         }
      }
      XMLReportGenerator::writeXmlToFile();
   }
   
   function importViaAPI() {
      
      $importer = new WooCommerceImporter();
      
      foreach ($this->groups->groupArray as $group) {
         $importer->batchCreateProducts($group->products);
         
         /*foreach ($group->products as $product) {
            $importer->createProduct($product);
            foreach ($product->variants as $variant) {
               CSVReportGenerator::appendCSVString($variant->getCsvRow());
            }
         }*/
      }
   }
}