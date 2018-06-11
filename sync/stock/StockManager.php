<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 03.12.2017
 * Time: 2:02
 */
namespace Dreamwhite\StockManager;
include_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
class StockManager {
   var $wpdb;
   var $skuPostIdMap = [];
	var $postIdStockMap = [];
   var $postmeta;
   var $queriesNotExecuted = 0, $skuMiss = 0, $queriesExecuted=0;

   var $stocks = [];


   function __construct() {
      define( 'SHORTINIT', true );

      global $wpdb;
      $this->wpdb = $wpdb;

      $this->getSkuPostIdMap();
	   $this->getPostIdStockMap();

   }

   function getSkuPostIdMap() {
      $query = "SELECT meta_value, post_id FROM " . $this->wpdb->postmeta . " WHERE meta_key = '_sku'";
      //Log::d($query);
      $results = $this->wpdb->get_results( $query , "ARRAY_A");
      
      foreach ($results as $result) {
         $this->skuPostIdMap[$result['meta_value']] =  $result['post_id'];
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

    function update_ms_id($post_id, $id){
        update_post_meta( $post_id, "_ms_id", $id);
    }

   function update_stock($sku, $stock){
      if (!empty($this->skuPostIdMap[$sku])){

      	if ($this->postIdStockMap[$this->skuPostIdMap[$sku]] != $stock) {
	        $sql = "UPDATE " . $this->postmeta .
	               " SET $this->postmeta.meta_value = " . $stock . " WHERE $this->postmeta.post_id = " . $this->skuPostIdMap[$sku] . " AND $this->postmeta.meta_key = '_stock';";
	        $this->wpdb->query( $sql );
            $this->queriesExecuted++;
        }
        else {
	        $this->queriesNotExecuted++;
        }
      }
      else {
         $this->skuMiss++;
      }

   }

    function updateStockFromCities($sku, $cityStockValues){
        if (!empty($this->skuPostIdMap[$sku])){

            //$stock = $cityStockValues[Config::CITY] ?? 0;
            $stock = 0;

            foreach ($cityStockValues as $city => $value) {
                update_post_meta($this->skuPostIdMap[$sku], "stock_" . $city, $value);
                $this->queriesExecuted++;
                $stock+=$value;
            }

            if ($this->postIdStockMap[$this->skuPostIdMap[$sku]] != $stock) {
                $sql = "UPDATE " . $this->postmeta .
                    " SET $this->postmeta.meta_value = " . $stock . " WHERE $this->postmeta.post_id = " . $this->skuPostIdMap[$sku] . " AND $this->postmeta.meta_key = '_stock';";
                $this->wpdb->query( $sql );
                $this->queriesExecuted++;
            }
            else {
                $this->queriesNotExecuted++;
            }



        }
        else {
            $this->skuMiss++;
        }

    }
}