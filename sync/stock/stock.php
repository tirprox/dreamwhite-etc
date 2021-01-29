<?php
/* Script for updating stock/ 1c id with short interval
  Works for legacy dreamwhite.ru / msk.dreamwhite.ru / kgd.dreamwhite.ru
  and new docker subdomains (aaa.dreamwhite.ru)
*/

//include_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
require_once('../wp-load.php');
define('SHORTINIT', true);

global $wpdb;
$meta = $wpdb->postmeta;

echo "Meta: $meta";

$skuPostIdMap = []; // map sku:postID
$postIdStockMap = []; // serves performance reasons - prevents updating the same stock value
$skipped = 0; $skuMiss = 0; $executed = 0;

// map sku:postID
$query = "SELECT m.meta_value, m.post_id FROM $meta m WHERE m.meta_key = '_sku';";
$results = $wpdb->get_results($query, "ARRAY_A");

foreach ($results as $result) {
  $skuPostIdMap[$result['meta_value']] = $result['post_id'];
}

// map postID:stock
$query = "SELECT m.meta_value, m.post_id FROM $meta m WHERE m.meta_key = '_stock';";
$results = $wpdb->get_results($query, "ARRAY_A");

foreach ($results as $result) {
  $postIdStockMap[$result['post_id']] = $result['meta_value'];
}

// download data from api
$data = json_decode(file_get_contents("https://service.dreamwhite.ru/output/combinedStock.json"), true);

// iterate data as sku:entry['id','city','domain']; sku may be actually a barcode
foreach ($data as $sku => $entry) {

  // update stock
  if (!empty($skuPostIdMap[$sku])) {

    $postID = $skuPostIdMap[$sku];
    //update 1c ID
    update_post_meta($postID, "_ms_id", $entry['id']); $executed++;

    $domainStock = $entry['domain'];

    // default stock for subdomains
    $defaultDomainStock = $domainStock['other'];

    // global CITY should be defined in wp-config.php
    $stock = $domainStock[CITY];

    // updating postmeta for custom product plugin (ex. stock_spb)
    foreach ($entry['city'] as $city => $value) {
      if ($value < 0 ) $value = 0;
      update_post_meta($postID, "stock_" . $city, $value); $executed++;
    }

    $terms = $stock <= 0 ? ['outofstock'] : [];
    wp_set_post_terms($postID, $terms, 'product_visibility', false);

    if ($postIdStockMap[$postID] != $stock) {
      $wpdb->query("UPDATE $meta m SET m.meta_value = $stock WHERE m.post_id = $postID AND m.meta_key = '_stock';");
      $executed++;
    } else {
      $skipped++;
    }
  } else {
    $skuMiss++;
  }
}

// Update stock status
$queries = [
  "UPDATE $meta stock, (SELECT DISTINCT post_id FROM $meta WHERE meta_key = '_stock' AND meta_value = '0 ') id SET stock.meta_value = 'outofstock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';",
  "UPDATE $meta stock, (SELECT DISTINCT post_id FROM $meta WHERE meta_key = '_stock' AND meta_value != '0' ) id SET stock.meta_value = 'instock' WHERE stock.post_id = id.post_id AND stock.meta_key = '_stock_status';",
  "UPDATE $meta m SET m.meta_value = 'yes' WHERE m.meta_key = '_manage_stock';",
  "UPDATE $meta m SET m.meta_value = 'outofstock' WHERE m.meta_value = 'onbackorder';"
];

foreach ($queries as $q) {
  $wpdb->query($q);
  $executed++;
}

wp_cache_flush();

echo $executed;
