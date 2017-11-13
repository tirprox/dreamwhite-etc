<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 05.11.2017
 * Time: 0:12
 */
include "InvertableAttribute.php";
include "Tag.php";

class CSVTagFactory {
   var $tags = [];
   
   
   function loadTagsFromFile() {
      $csvFile = file('tags.csv');
      $data = [];
      
      foreach ($csvFile as $line) {
         $data[] = str_getcsv($line, ";");
      }
      
      // removing csv header
      unset($data[0]);
      
      foreach ($data as $item) {
         $this->createTag($item);
         //var_dump($item);
      }
      
   }
   
   function createTag($csvRow) {
      $tag = new Tag();
      $tag->name = $csvRow[0];
      $tag->group = $this->splitAttr($csvRow[1]);
      
      $tag->color = $this->splitAttr($csvRow[2]);
      if ($tag->color !== "") {
	      $tag->hasColors = true;
      }
      
      $tag->size = $this->splitAttr($csvRow[3]);
      
      $tag->material = $this->splitAttr($csvRow[4]);
      $tag->uteplitel = $this->splitAttr($csvRow[5]);
      $tag->podkladka = $this->splitAttr($csvRow[6]);
      $tag->siluet = $this->splitAttr($csvRow[7]);
      $tag->dlina = $this->splitAttr($csvRow[8]);
      $tag->rukav = $this->splitAttr($csvRow[9]);
      $tag->dlina_rukava = $this->splitAttr($csvRow[10]);
      $tag->zastezhka = $this->splitAttr($csvRow[11]);
      $tag->kapushon = $this->splitAttr($csvRow[12]);
      $tag->vorotnik = $this->splitAttr($csvRow[13]);
      $tag->poyas = $this->splitAttr($csvRow[14]);
      $tag->karmany = $this->splitAttr($csvRow[15]);
      $tag->koketka = $this->splitAttr($csvRow[16]);
      $tag->uhod = $this->splitAttr($csvRow[17]);
      
      
      
      $this->tags[] = $tag;
   }
   
   function splitAttr($atrrString){
      $data[] = str_getcsv($atrrString, ",");
      $attrs = [];
      foreach ($data[0] as $item) {
         $item = trim($item);
         
         if (substr($item, 0, 1) === "-") {
            $item=substr($item,1);
            $attrs[] = new InvertableAttribute($item, true);
         }
         else {
            $attrs[] = new InvertableAttribute($item, false);
         }
      }
      
      return $attrs;
   }
   
   function setProductTag($product) {
      foreach ($this->tags as $tag) {
         //check basic attrs
         if (!$this->compareAttrs($tag->group, $product->productFolderName)) continue;
         if (!$this->compareAttrs($tag->color, $product->colors)) continue;
	
	      
         if (!$this->compareAttrs($tag->size, $product->sizes)) continue;
   
         if (!$this->compareAttrs($tag->material, $product->material)) continue;
         if (!$this->compareAttrs($tag->uteplitel, $product->uteplitel)) continue;
         if (!$this->compareAttrs($tag->podkladka, $product->podkladka)) continue;
         if (!$this->compareAttrs($tag->siluet, $product->siluet)) continue;
         if (!$this->compareAttrs($tag->dlina, $product->dlina)) continue;
         if (!$this->compareAttrs($tag->rukav, $product->rukav)) continue;
         
         if (!$this->compareAttrs($tag->dlina_rukava, $product->dlina_rukava)) continue;
         
         if (!$this->compareAttrs($tag->zastezhka, $product->zastezhka)) continue;
         
         if (!$this->compareAttrs($tag->kapushon, $product->kapushon)) continue;
         if (!$this->compareAttrs($tag->vorotnik, $product->vorotnik)) continue;
         if (!$this->compareAttrs($tag->poyas, $product->poyas)) continue;
         if (!$this->compareAttrs($tag->karmany, $product->karmany)) continue;
         if (!$this->compareAttrs($tag->koketka, $product->koketka)) continue;
         if (!$this->compareAttrs($tag->uhod, $product->uhod)) continue;
         
         
         
         $product->tags .= $tag->name . ",";
         
      }
   }
   
   function compareAttrs($tagAttrArray, $productAttr) {
      
      if ($tagAttrArray[0]->attribute == "") return true;
      
      $match = false;
      foreach ($tagAttrArray as $attr){
         if (Tools::match($productAttr, $attr->attribute)) {

         	//print("Товар: \"" . $productAttr . "\" Метка: \"" . $attr->attribute . "\"\n");
            $match = $attr->isInverted ? false : true;
         }
         else $match =  $attr->isInverted ? true : false;
         
         if ($match) return true;
      }
      
      return $match;
   }

}