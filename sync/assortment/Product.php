<?php
namespace Dreamwhite\Assortment;
class Product {
   var $product;
   var $productFolderName;

   var $pathName = "";

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
   var $regularPrice = "0", $salePrice = "";
   var $isOnSale = false;
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
   
   var $video ="";
   
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

       if ( count( $product->barcodes ) > 0 ) {
           $this->barcode = $product->barcodes[ 0 ];
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
    
	   $this->getPrices();
      $this->setAttributes();
	   $this->gender = Tools::match($this->productFolderName, "женские") ? "Женские" : "Мужские";
      
      if (Tools::match($this->uteplitel, "пух")) {
         $this->categories .= "," . $this->gender . " пуховики";
      	//$this->productFolderName .= "," . $this->gender . " пуховики";
      }
      
      Log::d($this->name, "product", "p", "products");
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
         else if(is_string($attr->value)) {
	         $value = $attr->value;
         }
      }
      return $value;
   }
   
   function setAttributes() {
      if ($this->product != null) {
         if (property_exists($this->product, "attributes")) {
            $attrs = $this->product->attributes;
            
            $attrSet = [];
            
            foreach ($attrs as $attr) {
	            $attrSet[$attr->name] = $this->getAttributeValue($attr);
            }
	
	         $this->material = $attrSet['Материал'] ?? "";
	         $this->uteplitel = $attrSet['Утеплитель'] ?? "";
	         $this->podkladka = $attrSet['Подкладка'] ?? "";
	         $this->siluet = $attrSet['Силуэт'] ?? "";
	         $this->dlina = $attrSet['Длина изделия'] ?? "";
	         $this->rukav = $attrSet['Рукав'] ?? "";
	         $this->dlina_rukava = $attrSet['Длина рукава'] ?? "";
	         $this->zastezhka = $attrSet['Застежка'] ?? "";
	         // need to convert boolean
	         $this->kapushon = $attrSet['Капюшон'] ?? "";
	         $this->vorotnik = $attrSet['Воротник'] ?? "";
	         $this->poyas = $attrSet['Пояс'] ?? "";
	         $this->karmany = $attrSet['Карманы'] ?? "";
	         $this->koketka = $attrSet['Кокетка'] ?? "";
	         $this->uhod = $attrSet['Уход'] ?? "";
	
	         if (isset($attrSet['Видео'])) $this->video = $attrSet['Видео'];
         }
      }
   }
   function textFromBool($bool){
      return $bool ? "Есть" : "Нет";
   }
   
   function getPrices () {
      foreach ($this->product->salePrices as $price) {
         if ( $price->priceType === "Цена продажи" ) {
            $this->regularPrice = $price->value / 100;
         }
         else if ($price->priceType === "Распродажа") {
            //var_dump($price);
            if ($price->value > 0) {
               $this->salePrice = $price->value / 100;
               $this->isOnSale = true;
            }
            
         }
      }
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