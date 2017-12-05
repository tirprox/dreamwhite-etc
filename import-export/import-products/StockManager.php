<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 03.12.2017
 * Time: 2:02
 */

class StockManager {
   var $wpdb;
   var $postIdSkuMap = [];
   var $postmeta;
   
   function __construct() {
      define( 'SHORTINIT', true );
      require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
      global $wpdb;
      $this->wpdb = $wpdb;
      $this->getPostIdSkuMap();
      //og::d(var_dump($this->wpdb));
      //$this->wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   }
   
   function getPostIdSkuMap() {
      $query = "SELECT meta_value, post_id FROM " . $this->wpdb->postmeta . " WHERE meta_key = '_sku'";
      //Log::d($query);
      $results = $this->wpdb->get_results( $query , "ARRAY_A");
      
      foreach ($results as $result) {
         $this->postIdSkuMap[$result['meta_value']] =  $result['post_id'];
      }
      
      $this->postmeta = $this->wpdb->postmeta;
      //Log::d(var_dump($this->postIdSkuMap));
   }
   
   function update_stock_status(){
      $sql1 = "UPDATE " . $this->wpdb->postmeta . " stock, (SELECT DISTINCT post_id FROM " . $this->wpdb->postmeta .
         " WHERE meta_key = '_stock' AND meta_value < 1 ) id SET stock.meta_value = 'outofstock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";
      $sql2 = "UPDATE " . $this->wpdb->postmeta . " stock, (SELECT DISTINCT post_id FROM " . $this->wpdb->postmeta .
         " WHERE meta_key = '_stock' AND meta_value > 0 ) id SET stock.meta_value = 'instock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";
      //Log::d(var_dump($sql));
      $this->wpdb->query( $sql1 );
      $this->wpdb->query( $sql2 );
   }
   
   function update_stock($sku, $stock){
      $sql = "UPDATE " . $this->postmeta .
         " SET $this->postmeta.meta_value = " . $stock . " WHERE $this->postmeta.post_id = " . $this->postIdSkuMap[$sku] . " AND $this->postmeta.meta_key = '_stock';";
      //Log::d(var_dump($this->postIdSkuMap[$sku]));
      //Log::d(var_dump($sql));
      $this->wpdb->query( $sql );
   }
}