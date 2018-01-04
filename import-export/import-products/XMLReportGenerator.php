<?php

class XMLReportGenerator {
   private static $path = "" . __DIR__ . "/report.xml";
   private static $document;
   private static $root;
   
   static function getDocument() {
      if (self::$document == null) {
         self::createDocument();
      }
      return self::$document;
   }
   
   static function createDocument() {
         self::$document = new DOMDocument('1.0', 'UTF-8');
         self::$document->formatOutput = true;
   
         self::$root = self::$document->createElement('products');
         self::$root = self::$document->appendChild(self::$root);
   }
   
   
   static function writeXmlToFile() {
      if (self::$document != null) {
         self::$document->save(self::$path);
      }
   }
   
   static function addProduct($product) {
      $xmlProduct = self::addChild('product', self::$root);
   
      self::addNode('name', $product->name, $xmlProduct);
      self::addNode('group', $product->categories, $xmlProduct);
      self::addNode('sku', $product->code, $xmlProduct);
      self::addNode('article', $product->article, $xmlProduct);
      self::addNode('uom', $product->uom, $xmlProduct);
      self::addNode('stock', $product->stock, $xmlProduct);
      self::addNode('availability',
         $product->stock > 0 ? "instock" : "outofstock",
         $xmlProduct);
      self::addNode('price', $product->regularPrice, $xmlProduct);
      self::addNode('salePrice', $product->salePrice, $xmlProduct);
      self::addNode('description', $product->description, $xmlProduct);
      
      $attrs = self::addChild('attributes', $xmlProduct);
      self::addNode('material', $product->material, $attrs);
      self::addNode('uteplitel', $product->uteplitel, $attrs);
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
      
   
      self::addNode('photo', $product->productPhotoUrl, $xmlProduct);
	  self::addNode('photoGallery', $product->galleryUrls, $xmlProduct);
	   self::addNode('video', $product->video, $xmlProduct);
	   self::addNode('video-youtube-part', Tools::removeYoutubeBase($product->video), $xmlProduct);
      self::addNode('tags', $product->tags, $xmlProduct);
      //$xmlProduct = self::addChild('variants', $xmlProduct);
      
      $variants = XMLReportGenerator::addChild('variations', $xmlProduct);
      foreach ($product->variants as $variant) {
         $xmlVariant = self::addChild('variation', $variants);
         self::addNode('name', $variant->name, $xmlVariant);
         self::addNode('sku', $variant->code, $xmlVariant);
         //self::addNode('article', $variant->article, $productNode);
         //self::addNode('uom', $variant->uom, $productNode);
         self::addNode('stock', $variant->stock, $xmlVariant);
         self::addNode('availability',
            $variant->stock > 0 ? "instock" : "outofstock",
            $xmlVariant);
         self::addNode('price', $variant->regularPrice, $xmlVariant);
         self::addNode('salePrice', $variant->salePrice, $xmlVariant);
   
         self::addNode('description', $variant->description, $xmlVariant);
   
         self::addNode('color', $variant->color, $xmlVariant);
         self::addNode('size', $variant->size, $xmlVariant);
         self::addNode('photo', $variant->variantPhotoUrl, $xmlVariant);
      }
      
      return $xmlProduct;
   }
   
   /*static function addVariantToProductNode($variant, $productNode) {
      $xmlVariant = self::addChild('variant', $productNode);
      self::addNode('name', $variant->name, $xmlVariant);
      self::addNode('sku', $variant->code, $xmlVariant);
      //self::addNode('article', $variant->article, $productNode);
      //self::addNode('uom', $variant->uom, $productNode);
      
      self::addNode('stock', $variant->stock, $xmlVariant);
      
      self::addNode('available',
         $variant->stock > 0 ? "instock" : "outofstock",
         $xmlVariant);
      self::addNode('price', $variant->salePrice, $xmlVariant);
      
      self::addNode('description', $variant->description, $xmlVariant);
   
      self::addNode('color', $variant->color, $xmlVariant);
      self::addNode('size', $variant->size, $xmlVariant);
      return $productNode;
   
   }*/
   
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