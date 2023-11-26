<?php 

// create an api endpoint for products
add_action( 'rest_api_init', 'sde_products_api' );

function sde_products_api() {
    // add new api endpoint to get products from api and add them to database
    register_rest_route( 'sde/v1', '/products', array(
        'methods' => 'GET',
        'callback' => 'sde_products_api_callback'
    ) );

    // add new api endpoint to get products from database add add them to woocommerce
    register_rest_route( 'sde/v1', '/add-product', array(
        'methods' => 'GET',
        'callback' => 'sde_add_products_from_db_api_callback'
    ) );
}

// callback function for api endpoint to get products from api and add them to database
function sde_products_api_callback( $request ) {
    return sde_products_shortcode();
}

// callback function for api endpoint to get products from database add add them to woocommerce
function sde_add_products_from_db_api_callback( $request ) {
    return sde_add_new_product_from_db();
}
