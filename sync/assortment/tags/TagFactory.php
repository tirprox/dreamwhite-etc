<?php
namespace Dreamwhite\Assortment;
class TagFactory {
   var $tags = [];

   var $parsedTags = [];
   
   function loadTagsFromFile() {
      $csvFile = file(__DIR__ . '/tags.csv');
      $data = [];
      
      foreach ($csvFile as $line) {
         $data[] = str_getcsv($line, ';');
      }
      
      // removing csv header
      unset($data[0]);
      
      foreach ($data as $item) {
         $this->createTag($item);
         //var_dump($item);
      }

   }

   function getTagList($globalAttrs) {
       XMLTaxonomyListGenerator::createDocument();
       foreach ($this->tags as $tag) {

           $colors = [];
           foreach ($tag->color as $color) {
               $colors[] = $color->attribute;
           }

           $tag->colorGroup = array_intersect($colors, $globalAttrs['color']);
           XMLTaxonomyListGenerator::addTag($tag);
       }
       XMLTaxonomyListGenerator::writeXmlToFile();

   }

   /* Creating a tag from a csv row, where row is represented as an array. */
   function createTag($row) {
      $tag = new Tag();
      $tag->name = $row[0];
      $tag->group = $this->splitAttr($row[1]);
      
      $tag->color = $this->splitAttr($row[2]);
      if ($tag->color[0]->attribute !== '') {
	      $tag->hasColors = true;
      }
      
      $tag->size = $this->splitAttr($row[3]);
      
      $tag->material = $this->splitAttr($row[4]);
      $tag->uteplitel = $this->splitAttr($row[5]);
      $tag->podkladka = $this->splitAttr($row[6]);
      $tag->siluet = $this->splitAttr($row[7]);
      $tag->dlina = $this->splitAttr($row[8]);
      $tag->rukav = $this->splitAttr($row[9]);
      $tag->dlina_rukava = $this->splitAttr($row[10]);
      $tag->zastezhka = $this->splitAttr($row[11]);
      $tag->kapushon = $this->splitAttr($row[12]);
      $tag->vorotnik = $this->splitAttr($row[13]);
      $tag->poyas = $this->splitAttr($row[14]);
      $tag->karmany = $this->splitAttr($row[15]);
      $tag->koketka = $this->splitAttr($row[16]);
      $tag->uhod = $this->splitAttr($row[17]);
      
      
      
      $this->tags[] = $tag;
   }

   /* Determine whether attribute should be included or excluded.
   If prepended with -, attribute is excluded from a tag (is inverted) */
   function splitAttr($atrrString){
      $data[] = str_getcsv($atrrString, ',');
      $attrs = [];
      foreach ($data[0] as $item) {
         $item = trim($item);
         
         if (substr($item, 0, 1) === '-') {
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
      // sale tag
      if ($product->isOnSale) {
         $product->tags .= 'Распродажа,';
      }
      
      foreach ($this->tags as $tag) {
          $parsed = [];
         //check basic attrs
         if (!$this->compareAttrs($tag->group, $product->productFolderName)) continue;
         
         // colors do not support attr inversion for now
         //
         if ($tag->hasColors) {
            if (!$this->compareColors($tag, $product->color)) continue;
         }
         else {
            if (!$this->compareAttrs($tag->color, $product->color)) continue;
         }
        
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
         
         
         
         $product->tags .= $tag->name . ',';
         
      }
   }


   /* The order is following:
   First, if attr is empty,  tag match is returned instantly.
   Second, comparing tag attrs to product attrs, on match check for inversion.
   If not inverted, match is found. Else not.
   If match not found, but attr is inverted, return match.
    */

   function compareAttrs($tagAttrArray, $productAttr) {
      if ($tagAttrArray[0]->attribute == '') return true;
      $matchCount=0;
      $match = false;
      foreach ($tagAttrArray as $attr){
      	//comparing tag strings to attributes, if string match is found and not inverted set match true and return match;
	      //string match found
         if (Tools::match($productAttr, $attr->attribute)) {
            $match = $attr->isInverted ? false : true;
         }
         //string match not found
         else {
         	if ($attr->isInverted)
	         $matchCount++;
         }
         if ($matchCount === count($tagAttrArray)) $match = true;
         //else $match =  $attr->isInverted ? true : false;
         if ($match) return true;
      }
      return $match;
   }
   
   function compareColors($tag, $productColor) {
      $tagColors = $tag->color;
      $match = false;

       foreach ($tagColors as $tagColor){

               if (Tools::match($productColor, $tagColor->attribute)) {
                   $match = true;
                   $productColorTranslit = strtolower(Tools::transliterate($productColor));
                   if (!in_array($productColorTranslit, $tag->realColors)) {
                       $tag->realColors[] = $productColorTranslit;
                   }
               }
       }



      return $match;
   }

}