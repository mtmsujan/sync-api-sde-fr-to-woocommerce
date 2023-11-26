<?php
// create a table named sync_orders on plugin activation
function sde_sync_orders_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_orders';
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

// remove the table named sync_orders on plugin deactivation
function sde_sync_orders_table_remove() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_orders';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}


// add new order in sync_orders table on order creation
add_action('woocommerce_new_order', 'sde_sync_orders_table_add', 10, 1);
function sde_sync_orders_table_add($order_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_orders';
    $operation_type = 'create_order';
    $operation_value = $order_id;
    $status = 'pending';
    $wpdb->insert(
        $table_name,
        array(
            'operation_type' => $operation_type,
            'operation_value' => $operation_value,
            'status' => $status
        )
    );
}