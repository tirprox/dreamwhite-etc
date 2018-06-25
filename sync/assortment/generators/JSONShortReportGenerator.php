<?php
namespace Dreamwhite\Assortment;
class JSONShortReportGenerator {
   private static $root = [];


   static function writeJsonToFile() {

       $json = \json_encode(self::$root, JSON_UNESCAPED_UNICODE);
       $path = dirname(__DIR__) . "/output/short-report.json";
       file_put_contents($path, $json);


       return true;
   }
   
   static function addProduct($product) {
      foreach ($product->variants as $variant) {
          $item = [
              'id' => $variant->id,
              'name' => $variant->name,
              'barcode' => $variant->barcode
          ];
          self::$root[] = $item;
      }
   }

}