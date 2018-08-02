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


      $colors = implode(",", $tag->colorGroup);

      /*foreach ($tag->color as $colorAttr) {
          $colors .= $colorAttr->attribute . ",";
      }*/

       self::addNode('color', $colors, $attrs);
       /*self::addNode('colorGroup', $tag->colorGroup, $attrs);
       self::addNode('texture', $tag->texture, $attrs);

      self::addNode('material', $tag->material, $attrs);
      self::addNode('uteplitel', $tag->uteplitel, $attrs);
       self::addNode('season', $tag->season, $attrs);

      self::addNode('podkladka', $tag->podkladka, $attrs);
      self::addNode('siluet', $tag->siluet, $attrs);
      self::addNode('dlina', $tag->dlina, $attrs);
      self::addNode('rukav', $tag->rukav, $attrs);
      self::addNode('dlina_rukava', $tag->dlina_rukava, $attrs);
      self::addNode('zastezhka', $tag->zastezhka, $attrs);
      self::addNode('kapushon', $tag->kapushon, $attrs);
      self::addNode('vorotnik', $tag->vorotnik, $attrs);
      self::addNode('poyas', $tag->poyas, $attrs);
      self::addNode('karmany', $tag->karmany, $attrs);
      self::addNode('koketka', $tag->koketka, $attrs);
      self::addNode('uhod', $tag->uhod, $attrs);*/


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