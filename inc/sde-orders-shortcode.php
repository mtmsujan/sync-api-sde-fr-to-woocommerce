<?php 

// shortcode to create orders from sync_orders table to api
add_shortcode('sde_create_order', 'sde_create_order');
function sde_create_order() {

    $api_key = get_option( 'sde_api_key' );
    ob_start();
    // get orders from sync_orders table 
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_orders';
    $status = 'pending';
    $orders = $wpdb->get_results("SELECT * FROM $table_name WHERE status = '$status'");

    foreach($orders as $order){
        $operation_type = $order->operation_type;
        $operation_value = $order->operation_value;

        if($operation_type == "create_order"){
            $order_id = $operation_value;
            $order = wc_get_order($order_id);

            // Mode (assuming you have this information stored somewhere)
            $mode = "sandbox";
            // Language (assuming you have this information stored somewhere)
            $lang = "en";
            // Delivery information
            $delivery_info = array(
                "lastName" => $order->get_shipping_last_name(),
                "firstName" => $order->get_shipping_first_name(),
                "company" => $order->get_shipping_company(),
                "street" => $order->get_shipping_address_1(),
                "street2" => $order->get_shipping_address_2(),
                "postalCode" => $order->get_shipping_postcode(),
                "city" => $order->get_shipping_city(),
                "countryCode" => $order->get_shipping_country() == "BD" ? "FR" : $order->get_shipping_country(),
                "mail" => $order->get_billing_email(),
                "phone" => $order->get_billing_phone(),
                "orderNumber" => $order->get_order_number()
            );

            // Products information
            $products = array();
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $products[] = array(
                    "sku" => $product->get_sku(),
                    "quantity" => $item->get_quantity()
                );
            }

            // Constructing the final information array
            $order_info = array(
                "mode" => $mode,
                "lang" => $lang,
                "delivery" => $delivery_info,
                "products" => $products
            );

            // Convert the $order_info array to JSON
            $order_json = json_encode($order_info, JSON_PRETTY_PRINT);

            // Send the JSON to the API
            $url = "https://api.sde.fr/v1/order/create";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $order_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'api-key: ' . $api_key
                )
            );
            $result = curl_exec($ch);
            curl_close($ch);

            $api_order_id = json_decode($result)->orderId;

            // save the api_order_id for the order using order meta 
            $order->update_meta_data('api_order_id', $api_order_id);
            // update order meta data for api_order_id
            $order->save_meta_data();

            // show the api_order_id in the admin order page
            $order->add_order_note(
                sprintf(
                    __( 'API Order ID: %s', 'textdomain' ),
                    $api_order_id
                )
            );

            // Update the status of the order in the sync_orders table
            $table_name = $wpdb->prefix . 'sync_orders';
            $status = 'completed';
            $wpdb->update(
                $table_name,
                array(
                    'status' => $status
                ),
                array(
                    'operation_type' => $operation_type,
                    'operation_value' => $operation_value
                )
            );
        }
    }

    return [
        'result' => $result,
        'message' => 'Order created successfully'
    ];

    return ob_get_clean();
}

// get order status from api and update the order status in woocommerce
add_shortcode('sde_update_order_status', 'sde_get_order_status');
function sde_update_order_status(){
    ob_start();

    // get all orders from woocommerce
    $orders = wc_get_orders( array( 'limit' => -1 ) );

    foreach($orders as $order){
        $api_order_id = $order->get_meta('api_order_id'); // The Order data
        $order_id = $order->get_id(); // The Order data

        if($api_order_id != "" && $api_order_id != null){
            // get order status from api
            $api_key = get_option( 'sde_api_key' );
            $url = "https://api.sde.fr/v1/order/status";

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_POSTFIELDS =>'{"orderId":"' . $api_order_id . '"}',
              CURLOPT_HTTPHEADER => array(
                'content-type: application/json',
                'api-key: ' . $api_key
              ),
            ));
            
            $result = curl_exec($curl);
            
            curl_close($curl);

            $status = json_decode($result)->status;

            // update order status in woocommerce
            $order->update_status($status);
            
        }
    }

    return [
        'result' => $result,
        'message' => 'Order status updated successfully'
    ];

    return ob_get_clean();
}


// show api_order_id in the admin order page
add_action( 'woocommerce_admin_order_data_after_billing_address', 'sde_show_api_order_id_in_admin_order_page', 10, 1 );
function sde_show_api_order_id_in_admin_order_page( $order ){

    $api_order_id = $order->get_meta('api_order_id'); // The Order data]

    if($api_order_id != "" && $api_order_id != null){
        echo '<p><strong>'.__('API Order ID').':</strong> ' . $api_order_id . '</p>';
    }
}