<?php
namespace Dreamwhite\Import;
class XMLShortReportGenerator {
   private static $path = "" . __DIR__ . "/short-report.xml";
   private static $zipFile = "" . __DIR__ . "/short-report.xml.zip";

   private static $document;
   private static $root;


   
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
   
   
   static function writeXmlToFile() {


      if (self::$document != null) {
         self::$document->save(self::$path);
      }

       $zipArchive = new \ZipArchive();
       $zipArchive->open(self::$zipFile, \ZipArchive::OVERWRITE);
       $zipArchive->addFile(self::$path);
       $zipArchive->close();



   }
   
   static function addProduct($product) {
      //$xmlProduct = self::addChild('product', self::$root);
      foreach ($product->variants as $variant) {
          $xmlVariant = self::addChild('variation', self::$root);
          self::addNode('id', $variant->id, $xmlVariant);
         self::addNode('name', $variant->name, $xmlVariant);
          self::addNode('barcode', $variant->barcode, $xmlVariant);

      }
      return $xmlVariant;
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