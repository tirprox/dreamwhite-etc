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
      
      
      var_dump($tag->kapushon);
   }
   
   function splitAttr($atrrString){
      $data[] = str_getcsv($atrrString, ",");
      $attrs = [];
      //var_dump($data);
      foreach ($data[0] as $item) {
         //$val = $item[0];
         $item = trim($item);
         //var_dump($item);
         
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
      
      }
   }
}