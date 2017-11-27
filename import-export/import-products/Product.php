<?php

class Product {
   var $product;
   var $productFolderName;
   var $name;
   var $id;
   var $wc_id;
   var $url;
   var $variants = [];
   
   var $stock = 0;
   
   var $code;
   var $uom = "";
   
   var $supplier = "";
   var $description = "";
   var $color = "";
   var $size = "";
   var $variantName = "";
   var $barcode = "";
   var $salePrice = "0";
   var $article = "";
   
   var $material = "";
   var $uteplitel = "";
   var $podkladka = "";
   var $siluet = "";
   var $dlina = "";
   var $rukav = "";
   var $dlina_rukava = "";
   var $zastezhka = "";
   var $kapushon = "";
   var $vorotnik = "";
   var $poyas = "";
   var $karmany = "";
   var $koketka = "";
   var $uhod = "";
   
   var $baseUrl = "http://static.dreamwhite.ru/photo/";
   
	var $productPhotoUrl = "";
	var $galleryUrls = "";
	
	var $colors = [], $sizes = "";
	var $tags = "";
	var $gender;
   
   
   function __construct($product, $stock, $folderName) {
      $this->product = $product;
      $this->productFolderName = $folderName;
      $this->id = $product->id;
      $this->name = $product->name;
      $this->stock = $stock;
      
      $this->code = $product->code;
      if (property_exists($product, "uom")) {
         $this->uom = $product->uom->name;
      }
      if (property_exists($product, "supplier")) {
         $this->supplier = $product->supplier->name;
      }
      if (property_exists($product, "description")) {
         $this->description = $product->description;
      }
      if (property_exists($product, "article")) {
         $this->article = $product->article;
      }
	
	   $photoFileName = $this->article . ".jpg";
	   $photoFileName = str_replace(" ", "-", $photoFileName );
	
	   if (in_array($photoFileName,Tools::$imageDirList)) {
		   $urlToEncode = $this->baseUrl
		                  . $this->productFolderName
		                  . "/" . $this->article
		                  . "/" . $this->article . ".jpg";
		   $urlToEncode = str_replace(" ", "-", $urlToEncode);
		   $this->productPhotoUrl = $urlToEncode;
		   $this->galleryUrls .= $urlToEncode . ",";
	   }
      
      $this->setAttributes();
	   $this->gender = Tools::match($this->productFolderName, "женские") ? "Женские" : "Мужские";
      
      if (Tools::match($this->uteplitel, "пух")) {
      	$this->productFolderName .= "," . $this->gender . " пуховики";
      }
      
      Log::d("\n$this->name\n");
   }
   
   function getProductCsvRow() {
      return "\"" . $this->code . "\"," .
         "\"" . $this->productFolderName . "\"," .
         "\"" . $this->uom . "\"," .
         "\"" . $this->supplier . "\"," .
         "\"" . $this->description . "\"," .
         "\"" . $this->color . "\"," .
         "\"" . $this->size . "\"," .
         "\"" . $this->variantName . "\"," .
         "\"" . $this->code . "\"," .
         "\"" . $this->barcode . "\"," .
         "\"" . $this->stock . "\"," .
         "\"" . $this->salePrice . "\"," .
         "\"" . $this->name . "\"," .
         "\"" . $this->article . "\"," .
         $this->getAttributesString() .
         "\n";
   }
   
   function setAttributes() {
      if ($this->product != null) {
         if (property_exists($this->product, "attributes")) {
            $attrs = $this->product->attributes;
            
            if (is_object($attrs[ 0 ]->value)) {
               $this->material = $attrs[ 0 ]->value->name;
               $this->uteplitel = $attrs[ 1 ]->value->name;
               $this->podkladka = $attrs[ 2 ]->value->name;
               $this->siluet = $attrs[ 3 ]->value->name;
               $this->dlina = $attrs[ 4 ]->value->name;
               $this->rukav = $attrs[ 5 ]->value->name;
               $this->dlina_rukava = $attrs[ 6 ]->value->name;
               $this->zastezhka = $attrs[ 7 ]->value->name;
               // need to convert boolean
               $this->kapushon = $attrs[ 8 ]->value ? "Есть" : "Нет";
               $this->vorotnik = $attrs[ 9 ]->value->name;
               $this->poyas = $attrs[ 10 ]->value ? "Есть" : "Нет";
               $this->karmany = $attrs[ 11 ]->value ? "Есть" : "Нет";
               $this->koketka = $attrs[ 12 ]->value->name;
               $this->uhod = $attrs[ 13 ]->value->name;
            }
            
         }
      }
   }
   function textFromBool($bool){
      return $bool ? "Есть" : "Нет";
   }
   
   function getAttributesString() {
      return
         "\"" . $this->material . "\"," .
         "\"" . $this->uteplitel . "\"," .
         "\"" . $this->podkladka . "\"," .
         "\"" . $this->siluet . "\"," .
         "\"" . $this->dlina . "\"," .
         "\"" . $this->rukav . "\"," .
         "\"" . $this->dlina_rukava . "\"," .
         "\"" . $this->zastezhka . "\"," .
         "\"" . $this->kapushon . "\"," .
         "\"" . $this->vorotnik . "\"," .
         "\"" . $this->poyas . "\"," .
         "\"" . $this->karmany . "\"," .
         "\"" . $this->koketka . "\"," .
         "\"" . $this->uhod . "\"";
   }
}

?>