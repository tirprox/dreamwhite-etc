<?php

class Product {
   var $product;
   var $productFolderName;
   var $categories;
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
      $this->categories = $folderName;
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
         $this->categories .= "," . $this->gender . " пуховики";
      	//$this->productFolderName .= "," . $this->gender . " пуховики";
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
   function getAttributeValue($attr) {
      $value = "";
      if (is_object($attr)) {
         if (is_bool($attr->value)) {
            $value = $attr->value ? "Есть" : "Нет";
         }
         else if (is_object($attr->value)) {
            $value = $attr->value->name;
         }
      }
      return $value;
   }
   
   function setAttributes() {
      if ($this->product != null) {
         if (property_exists($this->product, "attributes")) {
            $attrs = $this->product->attributes;
            
            $finalAttrs = [];
            $attrSize = count($attrs);
            for ($i = 0; $i < 14; $i++) {
               $finalAttrs[$i] = isset($attrs[$i]) ? $this->getAttributeValue($attrs[ $i ]) : "";
            }
            
            $this->material = $finalAttrs[0];
            $this->uteplitel = $finalAttrs[1];
            $this->podkladka = $finalAttrs[2];
            $this->siluet = $finalAttrs[3];
            $this->dlina = $finalAttrs[4];
            $this->rukav = $finalAttrs[5];
            $this->dlina_rukava = $finalAttrs[6];
            $this->zastezhka = $finalAttrs[7];
            // need to convert boolean
            $this->kapushon = $finalAttrs[8];
            $this->vorotnik = $finalAttrs[9];
            $this->poyas = $finalAttrs[10];
            $this->karmany = $finalAttrs[11];
            $this->koketka = $finalAttrs[12];
            $this->uhod = $finalAttrs[13];
            
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