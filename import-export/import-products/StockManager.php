<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 03.12.2017
 * Time: 2:02
 */
namespace Dreamwhite\Import;
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
class StockManager {
   var $wpdb;
   var $postIdSkuMap = [];
	var $postIdStockMap = [];
   var $postmeta;
   var $queriesNotExecuted = 0, $skuMiss = 0, $queriesExecuted=0;

   
   function __construct() {
      define( 'SHORTINIT', true );

      global $wpdb;
      $this->wpdb = $wpdb;
      $this->getPostIdSkuMap();
	   $this->getPostIdStockMap();
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
   
   function getPostIdStockMap() {
	   $query = "SELECT meta_value, post_id FROM " . $this->wpdb->postmeta . " WHERE meta_key = '_stock'";
	   $results = $this->wpdb->get_results( $query , "ARRAY_A");
	
	   foreach ($results as $result) {
		   $this->postIdStockMap[$result['post_id']] =  $result['meta_value'];
	   }
   }

   function update_ms_id($post_id, $id){
           \update_post_meta( $post_id, "_ms_id", $id);
   }


   function update_stock_status(){
      $sql1 = "UPDATE " . $this->wpdb->postmeta . " stock, (SELECT DISTINCT post_id FROM " . $this->wpdb->postmeta .
         " WHERE meta_key = '_stock' AND meta_value < 1 ) id SET stock.meta_value = 'outofstock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";
      $sql2 = "UPDATE " . $this->wpdb->postmeta . " stock, (SELECT DISTINCT post_id FROM " . $this->wpdb->postmeta .
         " WHERE meta_key = '_stock' AND meta_value > 0 ) id SET stock.meta_value = 'instock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";
      $sql3 = "UPDATE " . $this->wpdb->postmeta . " SET " . $this->wpdb->postmeta . ".meta_value = 'yes' WHERE " . $this->wpdb->postmeta . ".meta_key = '_manage_stock';";

      $this->wpdb->query( $sql1 );
      $this->wpdb->query( $sql2 );
      $this->wpdb->query( $sql3 );
       wp_cache_flush();
   }

   function update_stock($sku, $stock){
      if (!empty($this->postIdSkuMap[$sku])){

/*          $sql = "UPDATE " . $this->postmeta .
              " SET $this->postmeta.meta_value = " . $stock . " WHERE $this->postmeta.post_id = " . $this->postIdSkuMap[$sku] . " AND $this->postmeta.meta_key = '_stock';";
          $this->wpdb->query( $sql );*/

      	if ($this->postIdStockMap[$this->postIdSkuMap[$sku]] != $stock) {
	        $sql = "UPDATE " . $this->postmeta .
	               " SET $this->postmeta.meta_value = " . $stock . " WHERE $this->postmeta.post_id = " . $this->postIdSkuMap[$sku] . " AND $this->postmeta.meta_key = '_stock';";
	        $this->wpdb->query( $sql );
            $this->queriesExecuted++;
	        Log::d("Stock is updated for sku: $sku, stock: $stock.", "stock", "p", "sql");
        }
        else {
	        $this->queriesNotExecuted++;
	        //Log::d("Stock is the same, no update needed. Total saved count: $this->queriesNotExecuted", "stock", "p", "sql");
        }
      }
      else {
         $this->skuMiss++;
         //Log::d("SKU miss. Total miss count: $this->skuMiss", "stock", "p", "sql");
      }

   }
}