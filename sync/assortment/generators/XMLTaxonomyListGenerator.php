<?php
namespace Dreamwhite\Assortment;
class XMLTaxonomyListGenerator {
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
   
         self::$root = self::$document->createElement('taxonomies');
         self::$root = self::$document->appendChild(self::$root);
   }
   


   static function writeXmlToFile() {
      if (self::$document != null) {
          $path = dirname(__DIR__) . "/output/tags.xml";
          self::$document->save($path);
      }
   }
   
   static function addTag($tag) {
      $xmlTagNode = self::addChild('taxonomy', self::$root);



      self::addNode('name', $tag->name, $xmlTagNode);

      self::addNode('description', $tag->description, $xmlTagNode);
      self::addNode('parent', $tag->parent, $xmlTagNode);
      
      $attrs = self::addChild('attributes', $xmlTagNode);

      foreach ($tag->realAttrs as $attr => $value) {
          self::addNode($attr , implode(",", $value), $attrs);
      }

      return $xmlTagNode;
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