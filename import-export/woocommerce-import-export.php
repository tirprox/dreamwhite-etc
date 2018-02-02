<?php
/*
Plugin Name: Мой склад
Plugin URI:
Description: Синхронизация WooCommerce - Мой Склад
Version:     1.0
Author:      Gleb Samsonov
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: moysklad-woocommerce
Domain Path: /languages
*/

add_action("admin_menu", "addMenu");
//add_action('woocommerce_thankyou', 'onWooCommerceOrderCompleted');
//add_action('woocommerce_new_order', 'onWooCommerceOrderCompleted');



function addMenu() {
   add_menu_page("Run Import", "Run Import", 4, "run-woocommerce-import", "addImportMenu");
}

function addImportMenu() {
   ?>
   <div class="wrap">
      <h1>Импорт</h1>
      <form action="/wp-content/plugins/import-export/import-products/makeReport.php" method="post">
         <?php settings_fields('plugin_options'); ?>
         <?php do_settings_sections('plugin'); ?>
         <input name="run-import" type="submit" value="Импортировать!" />
      </form></div>
	<?php
}

