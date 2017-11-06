<?php

class ProductVariant {
   var $variant;
   var $parentProduct;
   
   var $parentProductCode;
   var $parentName = "";
   
   var $id;
   var $vc_id;
   var $url;
   var $name;
   var $stock;
   
   var $price = "";
   var $code = "";
   var $uom = "";
   
   var $supplier = "";
   var $description = "";
   var $color = "";
   var $size = "";
   var $variantName = "";
   var $barcode = "";
   var $salePrice = "";
   var $article = "";
   var $variantPhotoUrl = "";
   
   public static $attributeString = "\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"";
   
   function __construct($variant, $stock, $parentProduct) {
      $this->variant = $variant;
      $this->parentProduct = $parentProduct;
      
      $this->id=$variant->id;
      $this->name=$variant->name;
      $this->stock=$stock;
      
      $this->parentProductCode=$parentProduct->code;
   
      if (property_exists($variant->product, "uom")) {
         $this->uom = $variant->product->uom->name;
      }
      if (property_exists($variant->product, "supplier")) {
         $this->supplier = $variant->product->supplier->name;
      }
      if (property_exists($variant->product, "description")) {
         $this->description = $variant->product->description;
      }
   
      $this->color = $this->getAttribute($variant, "Цвет");
      $this->size = $this->getAttribute($variant, "Размер");
      $this->variantName = $variant->name;
      if (property_exists($variant, "code")) {
         $this->code = $variant->code;
      }
      if (count($variant->barcodes) > 0) {
         $this->barcode = $variant->barcodes[ 0 ];
      }
      $this->salePrice = $this->getSalePrice($variant);
      $this->parentName = $parentProduct->name;
      if (property_exists($variant->product, "article")) {
         $this->article = $variant->product->article;
      }
	
	   $urlToEncode = $this->parentProduct->baseUrl
	                  . $this->parentProduct->productFolderName
	                  . "/" . $this->article
	                  . "/" . $this->article . " " . $this->color .".jpg";
      
	   //$this->variantPhotoUrl = urlencode($urlToEncode);
	   $urlToEncode = str_replace(" ", "-", $urlToEncode);
	   $this->variantPhotoUrl = $urlToEncode;
	   
	   $this->parentProduct->galleryUrls .= $urlToEncode . ",";
	   if ($this->stock > 0) {
	   	if (!Tools::match($this->parentProduct->colors, $this->color)) {
			   $this->parentProduct->colors .= $this->color . ",";
		   }
		if (!Tools::match($this->parentProduct->sizes, $this->size)) {
			   $this->parentProduct->sizes .= $this->size . ",";
		   }
		   
	   }
      
   
      Log::d("$this->name (В наличии $this->stock) \n");
      //print("$this->name (В наличии $this->stock) \n");
   }
   
   function getCsvRow() {

      return "\"" . $this->parentProductCode . "\"," .
         "\"" . $this->parentProduct->productFolderName . "\"," .
         "\"" . $this->uom . "\"," .
         "\"" . $this->supplier . "\"," .
         
         "\"" . $this->description . "\"," .
         "\"" . $this->color . "\"," .
         "\"" . $this->size . "\"," .
         "\"" . $this->name . "\"," .
         "\"" . $this->code . "\"," .
         
         "\"" . $this->barcode . "\"," .
         "\"" . $this->stock . "\"," .
         "\"" . $this->salePrice . "\"," .
         "\"" . $this->parentName . "\"," .
         "\"" . $this->article . "\"," .
         self::$attributeString .
         "\n";
   }
   
   function getAttribute($variant, $attrName) {
      foreach ($variant->characteristics as $ch) {
         if ($ch->name == $attrName) {
            return $ch->value;
         }
      }
      return "";
   }
  
   function getSalePrice($variant) {
      $salePrice = -1;
      foreach ($variant->product->salePrices as $price) {
         if ($price->priceType == "Цена продажи") {
            $salePrice = $price->value / 100;
         }
      }
      return $salePrice;
   }
   
}