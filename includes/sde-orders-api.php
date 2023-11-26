<?php 

// create an api endpoint for orders
add_action( 'rest_api_init', 'sde_orders_api' );

function sde_orders_api() {
    // add new api endpoint to get products from api and add them to database
    register_rest_route( 'sde/v1', '/create-order', array(
        'methods' => 'GET',
        'callback' => 'sde_create_order_api_callback'
    ) );

    // update order status in woocommerce from api
    register_rest_route( 'sde/v1', '/order-status-update', array(
        'methods' => 'GET',
        'callback' => 'sde_update_order_status_api_callback'
    ) );

}

// callback function for api endpoint to get products from api and add them to database
function sde_create_order_api_callback( $request ) {
    return sde_create_order();
}


// callback function to update order status in woocommerce from api
function sde_update_order_status_api_callback( $request ) {
    return sde_update_order_status();
}