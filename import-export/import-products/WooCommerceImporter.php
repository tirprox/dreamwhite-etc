<?php
require dirname(__DIR__) . '/vendor/autoload.php';
use Automattic\WooCommerce\Client;


class WooCommerceImporter {
   public $apiClient;
   function __construct() {
      /*$this->apiClient = new Client(
         'https://dreamwhite.ru',
         'ck_37e2445b182467ddc28475919a53ac219bbc6cd6',
         'cs_2d64855dd5f2ea2ab58c75a00d4c1ea0c70c50d5',
         [
            'wp_api' => true,
            'version' => 'wc/v2',
            'timeout' => 3600,
            'verify_ssl' => false,
            'query_string_auth' => true,
         ]
      );*/
   
      $this->apiClient = new Client(
         'http://localhost/wp/',
         'ck_d24e2b45eb0e7efc47cfe51e42e08f624c6926d6',
         'cs_1930aa3dc4adf0f2273d5a3491434c95a633d409',
         [
            'wp_api' => true,
            'version' => 'wc/v2',
            'timeout' => 3600,
            //'verify_ssl' => false,
            //'query_string_auth' => true,
         ]
      );
   }
   
   function createProduct($product) {
      $data = [
         'name' => $product->name,
         'type' => 'variable',
         'regular_price' => $product->salePrice,
         'description' => $product->description,
      ];
   
      $this->apiClient->post('products', $data);
   }
   
   function batchCreateProducts($products) {
      $data = ['create' => [], 'update' => [], 'delete' => []];
      foreach ($products as $product) {
         /*$variations = [];
         foreach ($product->variations as $variation) {
            $variant = [
               'sku' => $variation->code,
               'name' => $variation->name,
               'regular_price' => $variation->salePrice,
               'description' => $variation->description,
            ];
            $variations[]=$variant;
         }*/
         
         $item = [
            'sku' => $product->code,
            'name' => $product->name,
            'type' => 'variable',
            'regular_price' => $product->salePrice,
            'description' => $product->description,
            'variations' => $product->description,
         ];
         $data['create'][] = $item;
      }
     
      $response = $this->apiClient->post('products/batch', $data);
      
   }
   
   function import() {
   }
   
}