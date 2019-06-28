<?php
namespace Dreamwhite\Assortment;
class XMLTaxonomyListGenerator {
   private static $document;
   private static $root;

   public static $globalAttrs = [];

   static function addGlobalAttr($attr, $value) {


   }

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
       self::addNode('slug', $tag->slug, $xmlTagNode);

       $relations = self::addChild('relations', $xmlTagNode);
       foreach ($tag->relations as $attr => $value) {
           self::addNode($attr, $value, $relations);
       }


      $attrs = self::addChild('attributes', $xmlTagNode);
       //var_dump($tag->realAttrs);
      foreach ($tag->realAttrs as $attr => $value) {
          self::addNode($attr , implode(",", array_filter($value)), $attrs);
      }

       $seo = self::addChild('seo', $xmlTagNode);
       foreach ($tag->seo as $attr => $value) {
           self::addNode($attr, $value, $seo);
       }

      return $xmlTagNode;
   }

   static function addNode($name, $value, $parent) {
       if ($value !== '') {
           $node = self::addChild($name, $parent);
           $nodeVal = self::addTextNode($value, $node);
       }
   }
   
   static function addChild($elementName, $parent) {
      $element = self::$document->createElement($elementName);
      $element = $parent->appendChild($element);
      return $element;
   }
   
   static function addTextNode($text, $parent) {
       if (is_array($text)) { $data = implode(',', $text); }
       else { $data = $text; }

      $textNode = self::$document->createTextNode($data);
      $textNode = $parent->appendChild($textNode);
      return $textNode;
   }
   
   
}