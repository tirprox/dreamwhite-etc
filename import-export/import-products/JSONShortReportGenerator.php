<?php
namespace Dreamwhite\Import;
class JSONShortReportGenerator {
   private static $path = "" . __DIR__ . "/short-report.json";
   private static $zipFile = "" . __DIR__ . "/short-report.json.zip";

   private static $root = [];


   static function writeJsonToFile() {

       $json = \json_encode(self::$root, JSON_UNESCAPED_UNICODE);
       file_put_contents(self::$path, $json);



       $zip = new \ZipArchive();
       if($zip->open(self::$zipFile ,\ZIPARCHIVE::OVERWRITE)) {
           return false;
       }

       $zip->addFile(self::$path ,self::$path );
       $zip->close();
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