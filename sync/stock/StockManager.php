<?php
/**
 * Created by PhpStorm.
 * User: Gleb
 * Date: 03.12.2017
 * Time: 2:02
 */

namespace Dreamwhite\StockManager;
include_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

class StockManager {
  var $wpdb;
  var $skuPostIdMap = [];
  var $postIdStockMap = [];
  var $queriesNotExecuted = 0, $skuMiss = 0, $queriesExecuted = 0;

  var $stocks = [];

  function __construct() {
    define('SHORTINIT', true);

    global $wpdb;
    $this->wpdb = $wpdb;
    $this->getSkuPostIdMap();
    $this->getPostIdStockMap();

  }

  function resetStock() {
    $meta = $this->wpdb->postmeta;
    $sql1 = "UPDATE $meta stock SET stock.meta_value = '0' WHERE stock.meta_key = '_stock';";
    $this->wpdb->query($sql1);
  }

  function getSkuPostIdMap() {
    $meta = $this->wpdb->postmeta;
    $query = "SELECT m.meta_value, m.post_id FROM $meta m WHERE m.meta_key = '_sku';";
    $results = $this->wpdb->get_results($query, "ARRAY_A");
    foreach ($results as $result) {
      $this->skuPostIdMap[$result['meta_value']] = $result['post_id'];
    }
  }

  function getPostIdStockMap() {
    $meta = $this->wpdb->postmeta;
    $query = "SELECT m.meta_value, m.post_id FROM $meta m WHERE m.meta_key = '_stock';";
    $results = $this->wpdb->get_results($query, "ARRAY_A");

    foreach ($results as $result) {
      $this->postIdStockMap[$result['post_id']] = $result['meta_value'];
    }
  }

  function update_stock_status() {
    $meta = $this->wpdb->postmeta;
    $sql1 = "UPDATE $meta stock, (SELECT DISTINCT post_id FROM $meta WHERE meta_key = '_stock' AND meta_value = '0 ') id SET stock.meta_value = 'outofstock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";
    $sql2 = "UPDATE $meta stock, (SELECT DISTINCT post_id FROM $meta WHERE meta_key = '_stock' AND meta_value != '0' ) id SET stock.meta_value = 'instock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';";

    $sql3 = "UPDATE $meta m SET m.meta_value = 'yes' WHERE m.meta_key = '_manage_stock';";
    $sql4 = "UPDATE $meta m SET m.meta_value = 'outofstock' WHERE m.meta_value = 'onbackorder';";


    $this->wpdb->query($sql1);
    $this->wpdb->query($sql2);
    $this->wpdb->query($sql3);
    $this->wpdb->query($sql4);

    wp_cache_flush();
  }

  function update_ms_id($post_id, $id) {
    update_post_meta($post_id, "_ms_id", $id);
  }

  function updateStockFromCities($sku, $cityStockValues) {
    if (!empty($this->skuPostIdMap[$sku])) {
      $meta = $this->wpdb->postmeta;

      $stock = 0;

      foreach ($cityStockValues as $city => $value) {

        if ($value < 0 ) $value = 0;

        update_post_meta($this->skuPostIdMap[$sku], "stock_" . $city, $value);
        $this->queriesExecuted++;

        if ($city == 'spb') {
          $stock += $value;
        }
      }

      if ($stock == 0) {
        $terms = array('outofstock');
      }
      else {
        $terms = array();
      }

      wp_set_post_terms($this->skuPostIdMap[$sku], $terms, 'product_visibility', false);

      if ($this->postIdStockMap[$this->skuPostIdMap[$sku]] != $stock) {

        $sql = "UPDATE $meta m SET m.meta_value = $stock WHERE m.post_id = $this->skuPostIdMap[$sku] AND m.meta_key = '_stock';";
        $this->wpdb->query($sql);

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