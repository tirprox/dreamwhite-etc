<?php

namespace Dreamwhite\Assortment;

use GuzzleHttp\Pool;
use GuzzleHttp\Promise;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class AssortmentManager
{

    public $storeId = "baedb9ed-de2a-11e6-7a34-5acf00087a3f"; // Садовая
    public $testUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?store.id=baedb9ed-de2a-11e6-7a34-5acf00087a3f&productFolder.id=cc91a970-07e7-11e6-7a69-93a700454ab8&stockMode=all";
    public $assortmentUrl = "https://online.moysklad.ru/api/remap/1.1/entity/assortment?limit=100&filter=productFolder=";
    public $productsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/product/?limit=100&expand=uom,supplier&filter=pathName=";
    public $variantsUrl = "https://online.moysklad.ru/api/remap/1.1/entity/variant?limit=100&expand=product.uom,product.supplier&filter=productid=";
    public $stocksUrl = "https://online.moysklad.ru/api/remap/1.1/report/stock/all?stockMode=all&limit=1000&store.id=";

    const storePrefix = "https://online.moysklad.ru/api/remap/1.1/entity/store/";
    const expand = "&expand=uom,supplier";
    public $groups = null;

    public $stock = [];
    public $msidToSku = [];

    public $productRequestUrl;
    public $imageDirPath = "http://static.dreamwhite.ru/photo/dir.php";

    var $fromServer = true;

    function generateObjects()
    {

        Connector::init();
        Settings::load();

        Log::d(Settings::get("fromServer") ? "Using Server Config" : "Using Local Config", "config", "p");
        $imgPromise = Connector::requestAsync($this->imageDirPath);
        $imgPromise->then(
            function (ResponseInterface $res) {
                Tools::$imageDirList = json_decode($res->getBody());
                $count = count(Tools::$imageDirList);
                for ($i = 0; $i < $count; $i++) {
                    Tools::$imageDirList[$i] = str_replace("\0", "", Tools::$imageDirList[$i]);
                }
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        );
        Connector::addPromise($imgPromise);
        Connector::completeRequests();

        // multiple cities start here

        $cityGroups = [];
        foreach (Config::CITIES as $city) {
            Timers::start("groups");

            $groups = new Groups();
            $groups->groupArray = $groups->getGroupsForCity($city);
            $this->groups = $groups;

            $cityGroups[] = $groups;

            Timers::stop("groups");

            $groups = $this->getAssortmentForGroups($groups);
            $this->setTagsForGroups($groups);

        }

        JSONWriter::write($this->stock, "stock.json");
        JSONWriter::write($this->msidToSku, "ids.json");


        foreach ($cityGroups as $groups) {
            $this->createReportsForGroups($groups);
        }

    }

    function getAssortmentForGroups(&$groups)
    {
        Timers::start("assortment");
        foreach ($groups->groupArray as $group) {
            $requestUrl = $this->assortmentUrl . $group->url . "&stockstore=" . self::storePrefix . $group->storeId . self::expand;
            Log::d($requestUrl, "groups", "p", "groups");
            $promise = Connector::requestAsync($requestUrl);
            $promise->then(
                function (ResponseInterface $res) use ($group, $requestUrl) {

                    $group->firstResponse = json_decode($res->getBody());;
                    $group->firstRequestUrl = $requestUrl;

                },
                function (RequestException $e) {
                    Log::d("Getting initial assortment error" . $e->getMessage(), "errors", "p", "errors");
                    Log::d($e->getRequest()->getMethod(), "errors", "p", "errors");
                });
            Connector::addPromise($promise);
            if (!Settings::get("async")) Connector::completeRequests();
        }
        if (Settings::get("async")) Connector::completeRequests();

        foreach ($groups->groupArray as $group) {
            $group->assortment = $group->firstResponse;
            $this->getNextAssortments($group->firstResponse, $group->firstRequestUrl, $group);
            if (!Settings::get("async")) Connector::completeRequests();
        }
        if (Settings::get("async")) Connector::completeRequests();

        foreach ($groups->groupArray as $group) {
            foreach ($group->unpreparedResponses as $temp) {
                $group->assortment->rows = array_merge($group->assortment->rows, $temp->rows);
            }
            $group->products = $this->parseAssortment($group->assortment, $group);
        }


        Timers::stop("assortment");

        return $groups;
    }

    function getNextAssortments($res, $requestUrl, $group)
    {
        $size = $res->meta->size;
        $limit = $res->meta->limit;

        if ($size > $limit) {
            Log::d("size more than limit", "http-client");
            $iterations = intdiv($size, $limit) + 1;
            for ($i = 1; $i < $iterations; $i++) {
                $offset = "&offset=" . $i * $limit;
                $offsetUrl = $requestUrl . $offset;
                $promise = Connector::requestAsync($offsetUrl);

                $promise->then(
                    function (ResponseInterface $res) use ($group) {
                        $resp = json_decode($res->getBody());
                        Log::d("next url json received", "http-client");
                        $group->unpreparedResponses[] = $resp;
                    },
                    function (RequestException $e) {
                        Log::d("Getting next assortments error" . $e->getMessage(), "errors", "p", "errors");
                        Log::d($e->getRequest()->getMethod(), "errors", "p", "errors");
                    });
                Connector::addPromise($promise);
                if (!Settings::get("async")) Connector::completeRequests();
            }
            if (Settings::get("async")) Connector::completeRequests();
        }
    }

    var $productPrefix = "https://online.moysklad.ru/api/remap/1.1/entity/product/";

    function parseAssortment($assortment, $group)
    {
        // key = productHref, value = product
        $productHashMap = [];

        foreach ($assortment->rows as $row) {
            if ($row->meta->type === "product") {

                $newProduct = new Product($row, $row->stock, $group->name);
                $newProduct->pathName = $row->pathName;

                $this->stock[$newProduct->code][$group->city] = $newProduct->stock;
                $this->msidToSku[$newProduct->code] = $newProduct->id;
                $productHashMap[$this->productPrefix . $row->id] = $newProduct;

                unset($row);
            }
        }
        foreach ($assortment->rows as $row) {
            if ($row->meta->type === "variant") {
                $newVariant = new ProductVariant($row, $row->stock, $productHashMap[$row->product->meta->href]);

                $this->stock[$newVariant->code][$group->city] = $newVariant->stock;
                $this->msidToSku[$newVariant->code] = $newVariant->id;

                $productHashMap[$row->product->meta->href]->variants[] = $newVariant;
                //unset($row);
            }
        }
        $products = [];
        foreach ($productHashMap as $href => $product) {

            if ($product->pathName === $group->pathName) {
                $products[] = $product;
            }
            //$products[] = $product;

        }
        return $products;
    }

    function setTagsForGroups(&$groups)
    {
        Timers::start("tags");
        $tagFactory = new TagFactory();
        $tagFactory->loadTagsFromFile();
        foreach ($groups->groupArray as $group) {
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
        Timers::stop("tags");
    }

    function createReportsForGroups($groups)
    {

        XMLReportGenerator::createDocument();
        XMLReportGenerator::stock($this->stock);

        foreach ($groups->groupArray as $group) {
            foreach ($group->products as $product) {
                $xmlProductNode = XMLReportGenerator::addProduct($product);
                JSONShortReportGenerator::addProduct($product);
            }
        }
        XMLReportGenerator::city($groups->groupArray[0]->city);
        XMLReportGenerator::writeXmlToFile();

        JSONShortReportGenerator::writeJsonToFile();
    }

}