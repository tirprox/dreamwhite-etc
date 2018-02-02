<?php
namespace Dreamwhite\Import;
class InvertableAttribute {
   var $attribute, $isInverted;
   
   function __construct($attr, $inverted) {
      $this->attribute = $attr;
      $this->isInverted = $inverted;
   }
   
   function compareTo($attr) {
      if (!$this->isInverted){
         if ($attr === $this->attribute) {
            return 1;
         }
         else return 0;
      }
      else {
         if ($attr === $this->attribute) {
            return 0;
         }
         else return 1;
      }
   }
   
   
}