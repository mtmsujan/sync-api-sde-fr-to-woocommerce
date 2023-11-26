<?php 
/*
Plugin Name: SDE Supplier API
Plugin URI: http://www.api.sde.fr
Description: SDE Supplier API
Version: 1.0
Author: SDE
Author URI: http://www.api.sde.fr
License: GPL2
*/

// block access to this file if browsed directly 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// create and delete sync_products table on plugin activation and deactivation
require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-products-db.php' );
register_activation_hook( __FILE__, 'sde_sync_products_table' );
register_deactivation_hook( __FILE__, 'sde_sync_products_table_remove' );

// create and delete sync_orders table on plugin activation and deactivation
require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-orders-db.php' );
register_activation_hook( __FILE__, 'sde_sync_orders_table' );
register_deactivation_hook( __FILE__, 'sde_sync_orders_table_remove' );

require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-products-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-products-api.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-orders-shortcode.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/sde-orders-api.php' );
require_once( plugin_dir_path( __FILE__ ) . 'codestar-framework/codestar-framework.php' );
require_once( plugin_dir_path( __FILE__ ) . 'metabox.php' );
require_once( plugin_dir_path( __FILE__ ) . 'includes/menu-page.php' );