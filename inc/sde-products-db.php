<?php
// create a table named sync_products on plugin activation
function sde_sync_products_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        operation_type varchar(255) NOT NULL,
        operation_value TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        status varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}


// remove the table named sync_products on plugin deactivation
function sde_sync_products_table_remove() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}

