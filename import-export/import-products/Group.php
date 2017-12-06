<?php

class Group {
    var $url;
    var $name;
    var $pathName;
    var $id;
    var $storeId;
    
    var $products = [];
    var $stockCodes = [];
    var $stocks = [];
    
    function __construct($url, $name, $id, $storeId, $pathName) {
       $this->url = $url;
       $this->name = $name;
       $this->id = $id;
       $this->storeId = $storeId;
       $this->pathName = $pathName;
    }
    
    function addProduct($product) {
       $this->products[] = $product;
    }
}