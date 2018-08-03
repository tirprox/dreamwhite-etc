<?php
namespace Dreamwhite\Assortment;
class XMLReportGenerator {
   private static $document;
   private static $root, $city = '', $stock;
   
   static function getDocument() {
      if (self::$document == null) {
         self::createDocument();
      }
      return self::$document;
   }
   
   static function createDocument() {
         self::$document = new \DOMDocument('1.0', 'UTF-8');
         self::$document->formatOutput = true;
   
         self::$root = self::$document->createElement('products');
         self::$root = self::$document->appendChild(self::$root);
   }
   

   static function city($city) {
       self::$city = $city;
   }

    static function stock($stock) {
        self::$stock = $stock;
    }

   static function writeXmlToFile() {
      if (self::$document != null) {
          $path = dirname(__DIR__) . '/output/assortment-' . self::$city . '.xml';
          self::$document->save($path);
      }
   }
   
   static function addProduct($product) {
      $xmlProduct = self::addChild('product', self::$root);

      $stock = self::$stock[$product->code];


       self::addNode('id', $product->id, $xmlProduct);
      self::addNode('name', $product->name, $xmlProduct);
      self::addNode('group', $product->categories, $xmlProduct);
       //self::addNode('group', $product->categories, $xmlProduct);
      self::addNode('sku', $product->code, $xmlProduct);
       self::addNode('barcode', $product->barcode, $xmlProduct);

      self::addNode('article', $product->article, $xmlProduct);
      self::addNode('uom', $product->uom, $xmlProduct);
      //self::addNode('stock', $product->stock, $xmlProduct);

       $stocks = self::addChild('stocks', $xmlProduct);
       $stockSum = 0;

      foreach ($stock as $city => $value) {
          self::addNode('stock-' . $city, $value, $stocks);
          $stockSum += $value;
      }



       self::addNode('stock', $stockSum, $xmlProduct);
       self::addNode('availability',
           $stockSum > 0 ? 'instock' : 'outofstock',
           $xmlProduct);


       /*self::addNode('stock', $product->stock, $xmlProduct);
      self::addNode('availability',
         $product->stock > 0 ? 'instock' : 'outofstock',
         $xmlProduct);*/

      self::addNode('price', $product->regularPrice, $xmlProduct);
      self::addNode('salePrice', $product->salePrice, $xmlProduct);
      self::addNode('description', $product->description, $xmlProduct);
      
      $attrs = self::addChild('attributes', $xmlProduct);

       self::addNode('color', $product->color, $attrs);
       self::addNode('colorGroup', $product->colorGroup, $attrs);
       self::addNode('texture', $product->texture, $attrs);

      self::addNode('material', $product->material, $attrs);
      self::addNode('uteplitel', $product->uteplitel, $attrs);
       self::addNode('season', $product->season, $attrs);

      self::addNode('podkladka', $product->podkladka, $attrs);
      self::addNode('siluet', $product->siluet, $attrs);
      self::addNode('dlina', $product->dlina, $attrs);
      self::addNode('rukav', $product->rukav, $attrs);
      self::addNode('dlina_rukava', $product->dlina_rukava, $attrs);
      self::addNode('zastezhka', $product->zastezhka, $attrs);
      self::addNode('kapushon', $product->kapushon, $attrs);
      self::addNode('vorotnik', $product->vorotnik, $attrs);
      self::addNode('poyas', $product->poyas, $attrs);
      self::addNode('karmany', $product->karmany, $attrs);
      self::addNode('koketka', $product->koketka, $attrs);
      self::addNode('uhod', $product->uhod, $attrs);

       /*self::addNode('photo', $product->productPhotoUrl, $xmlProduct);
       self::addNode('photoGallery', $product->galleryUrls, $xmlProduct);*/


      self::addNode('photo', $product->images['primary'], $xmlProduct);
	  self::addNode('photoGallery', implode(',', $product->images['gallery']), $xmlProduct);

	  self::addNode('video', $product->video, $xmlProduct);
	   self::addNode('video-youtube-part', Tools::removeYoutubeBase($product->video), $xmlProduct);
      self::addNode('tags', $product->tags, $xmlProduct);
      //$xmlProduct = self::addChild('variants', $xmlProduct);
      
      $variants = XMLReportGenerator::addChild('variations', $xmlProduct);
      foreach ($product->variants as $variant) {
         $xmlVariant = self::addChild('variation', $variants);
          self::addNode('id', $variant->id, $xmlVariant);
         self::addNode('name', $variant->name, $xmlVariant);
         self::addNode('sku', $variant->code, $xmlVariant);
          self::addNode('barcode', $variant->barcode, $xmlVariant);
         //self::addNode('article', $variant->article, $productNode);
         //self::addNode('uom', $variant->uom, $productNode);
         //self::addNode('stock', $variant->stock, $xmlVariant);


          $variantStock = self::$stock[$variant->code];

          $varStockSum = 0;
          $varStocks = self::addChild('stocks', $xmlVariant);
          foreach ($variantStock as $city => $value) {
              self::addNode($city, $value, $varStocks);
              $varStockSum += $value;
          }

          self::addNode('stock', $varStockSum, $xmlVariant);

          self::addNode('availability',
              $varStockSum > 0 ? 'instock' : 'outofstock',
              $xmlVariant);

         /*self::addNode('availability',
            $variant->stock > 0 ? 'instock' : 'outofstock',
            $xmlVariant);*/


         self::addNode('price', $variant->regularPrice, $xmlVariant);
         self::addNode('salePrice', $variant->salePrice, $xmlVariant);
   
         self::addNode('description', $variant->description, $xmlVariant);
   
         self::addNode('color', $variant->color, $xmlVariant);
         self::addNode('size', $variant->size, $xmlVariant);
          self::addNode('height', $variant->height, $xmlVariant);

         self::addNode('photo', $variant->variantPhotoUrl, $xmlVariant);
      }
      
      return $xmlProduct;
   }

   static function addNode($name, $value, $parent) {
      $node = self::addChild($name, $parent);
      $nodeVal = self::addTextNode($value, $node);
   }
   
   static function addChild($elementName, $parent) {
      $element = self::$document->createElement($elementName);
      $element = $parent->appendChild($element);
      return $element;
   }
   
   static function addTextNode($text, $parent) {
      $textNode = self::$document->createTextNode($text);
      $textNode = $parent->appendChild($textNode);
      return $textNode;
   }
   
   
}