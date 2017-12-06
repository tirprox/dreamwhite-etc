<?php

class Group {
    var $url;
    var $name;
    var $id;
    
    var $products = [];
    var $stockCodes = [];
    var $stocks = [];
    
    function __construct($url, $name, $id) {
       $this->url = $url;
       $this->name = $name;
       $this->id = $id;
    }
    
    function addProduct($product) {
       $this->products[] = $product;
    }
}