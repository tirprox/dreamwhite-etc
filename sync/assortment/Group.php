<?php
namespace Dreamwhite\Assortment;
class Group {
    var $url;
    var $name;
    var $pathName;
    var $category;

    var $id;
    var $storeId;
    
    var $products = [];
    var $stockCodes = [];
    var $stocks = [];
    var $productHashMap = [];
    var $unpreparedResponses = [];
    var $firstResponse, $firstRequestUrl;
    var $assortment;

    var $city;

    
    
    function __construct($url, $name, $id, $storeId, $pathName, $category) {
       $this->url = $url;
       $this->name = $name;
       $this->id = $id;
       $this->storeId = $storeId;
       $this->pathName = $pathName;
       $this->category = $category;
    }
    
    function addProductToHashMap($href, $product) {
	    $this->productHashMap[$href] = $product;
    }
    
    function addProduct($product) {
       $this->products[] = $product;
    }
}