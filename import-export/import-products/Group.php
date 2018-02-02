<?php
namespace Dreamwhite\Import;
class Group {
    var $url;
    var $name;
    var $pathName;

    var $id;
    var $storeId;
    
    var $products = [];
    var $stockCodes = [];
    var $stocks = [];
    var $productHashMap = [];
    var $unpreparedResponses = [];
    var $firstResponse, $firstRequestUrl;
    var $assortment;

    
    
    function __construct($url, $name, $id, $storeId, $pathName) {
       $this->url = $url;
       $this->name = $name;
       $this->id = $id;
       $this->storeId = $storeId;
       $this->pathName = $pathName;
    }
    
    function addProductToHashMap($href, $product) {
	    $this->productHashMap[$href] = $product;
    }
    
    function addProduct($product) {
       $this->products[] = $product;
    }
}