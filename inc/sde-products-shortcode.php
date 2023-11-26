<?php 

function get_api_products($api_key){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.sde.fr/v1/products',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{
        "lang" : "en"
    }',
    CURLOPT_HTTPHEADER => array(
        'content-type: application/json',
        'api-key: ' . $api_key
    ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response_array = json_decode($response, true);
    $products = $response_array['products'];
    return $products;
}

add_shortcode( 'import_products_from_api', 'sde_products_shortcode' );

function sde_products_shortcode( $atts = array() ) {
    $api_key = get_option( 'sde_api_key' );
    $products = get_api_products($api_key);
    ob_start();
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';

    // remove existing products from the database
    $wpdb->query("TRUNCATE TABLE $table_name");
    foreach($products as $product){
        // insert each product into the database
        $wpdb->insert( 
            $table_name, 
            array( 
                'operation_type' => 'product_create', 
                'operation_value' => json_encode($product), 
                'status' => 'pending'
            ) 
        );
    }
    
    return [
        'success' => true,
        'message' => 'Products imported successfully'
    ];
    
    return ob_get_clean();

}

// add new woocommerce product from the database
add_shortcode( 'add_new_product_from_db', 'sde_add_new_product_from_db' );
function sde_add_new_product_from_db(){
    ob_start();

    global $wpdb;
    $table_name = $wpdb->prefix . 'sync_products';
    $products = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'pending' LIMIT 1");

    foreach($products as $product){
        $products_array = $product->operation_value;
        $product_array = json_decode($products_array, true);

        $childs = isset($product_array['childs']) ? $product_array['childs'] : array();

        foreach($childs as $child_product){
            $title = isset($child_product['name']) ? $child_product['name'] : "";
            $description = isset($child_product['description']) ? $child_product['description'] : "";
            $price = isset($child_product['price']) ? $child_product['price'] : 0;
            $sku = isset($child_product['sku']) ? $child_product['sku'] : "";
            $stock = isset($child_product['stock']) ? $child_product['stock'] : 0;
            $manage_stock = $stock > 0 ? 'yes' : 'no';
            $category = isset($child_product['category']) ? $child_product['category'] : "";
            $image_url_1 = isset($child_product['image-url-1']) ? $child_product['image-url-1'] : "";
            $image_url_2 = isset($child_product['image-url-2']) ? $child_product['image-url-2'] : "";
            $image_url_3 = isset($child_product['image-url-3']) ? $child_product['image-url-3'] : "";
            $image_url_4 = isset($child_product['image-url-4']) ? $child_product['image-url-4'] : "";
            $ean = isset($child_product['ean']) ? $child_product['ean'] : "";
            $brand = isset($child_product['brand']) ? $child_product['brand'] : "";
            $color = isset($child_product['color']) ? $child_product['color'] : "";
            $size = isset($child_product['size']) ? $child_product['size'] : "";
            $matter = isset($child_product['matter']) ? $child_product['matter'] : "";
            $care = isset($child_product['care']) ? $child_product['care'] : "";
            $customs_nomenclature = isset($child_product['customs-nomenclature']) ? $child_product['customs-nomenclature'] : "";
            $recommended_retail_price = isset($child_product['recommended-retail-price']) ? $child_product['recommended-retail-price'] : "";
            $weight = isset($child_product['weight']) ? $child_product['weight'] : "";
            $volume = isset($child_product['volume']) ? $child_product['volume'] : "";
            $sustainability = isset($child_product['sustainability']) ? $child_product['sustainability'] : "";
            $pcb = isset($child_product['pcb']) ? $child_product['pcb'] : "";
            $pcb_dropshipping = isset($child_product['pcb-dropshipping']) ? $child_product['pcb-dropshipping'] : "";
            $country_code = isset($child_product['country-code']) ? $child_product['country-code'] : "";
            $theme = isset($child_product['theme']) ? $child_product['theme'] : "";


            // if sku already exists, update the product
            $args = array(
                'post_type' => 'product',
                'meta_query' => array(
                    array(
                        'key' => '_sku',
                        'value' => $sku,
                        'compare' => '='
                    )
                )
            );
            $query = new WP_Query($args);
            if($query->have_posts()){
                $product_id = $query->posts[0]->ID;
                wp_update_post( array(
                    'ID' => $product_id,
                    'post_title' => $title,
                    'post_content' => $description,
                    'post_status' => 'publish',
                    'post_type' => "product",
                ) );
                
            }else{
                $product_id = wp_insert_post( array(
                    'post_title' => $title,
                    'post_content' => $description,
                    'post_status' => 'publish',
                    'post_type' => "product",
                ) );
            }

            wp_set_object_terms( $product_id, 'simple', 'product_type' );
            update_post_meta( $product_id, '_visibility', 'visible' );
            update_post_meta( $product_id, '_stock_status', 'instock');
            update_post_meta( $product_id, 'total_sales', '0' );
            update_post_meta( $product_id, '_downloadable', 'no' );
            update_post_meta( $product_id, '_virtual', 'yes' );
            update_post_meta( $product_id, '_regular_price', $price );
            update_post_meta( $product_id, '_sale_price', "" );
            update_post_meta( $product_id, '_purchase_note', "" );
            update_post_meta( $product_id, '_featured', "no" );
            update_post_meta( $product_id, '_weight', "" );
            update_post_meta( $product_id, '_length', "" );
            update_post_meta( $product_id, '_width', "" );
            update_post_meta( $product_id, '_height', "" );
            update_post_meta( $product_id, '_sku', $sku );
            update_post_meta( $product_id, '_product_attributes', array() );
            update_post_meta( $product_id, '_sale_price_dates_from', "" );
            update_post_meta( $product_id, '_sale_price_dates_to', "" );
            update_post_meta( $product_id, '_price', $price );
            update_post_meta( $product_id, '_sold_individually', "" );
            update_post_meta( $product_id, '_manage_stock', $manage_stock );
            update_post_meta( $product_id, '_backorders', "no" );
            update_post_meta( $product_id, '_stock', $stock );
            // add category 
            wp_set_object_terms( $product_id, $category, 'product_cat' );
            // add images from image-url-1, image-url-2, image-url-3
            
            $image_urls = array($image_url_1, $image_url_2, $image_url_3, $image_url_4);
            // remove blank image urls
            $image_urls = array_filter($image_urls);

            foreach ($image_urls as $image_url) {
                $image_name = basename($image_url); // Extracting image name
                $upload_dir = wp_upload_dir(); // Get WordPress upload directory

                // Download the image from URL and save it to the upload directory
                $image_data = file_get_contents($image_url);
                $image_file = $upload_dir['path'] . '/' . $image_name;
                file_put_contents($image_file, $image_data);

                // Prepare image data to be attached to the product
                $file_path = $upload_dir['path'] . '/' . $image_name;
                $file_name = basename($file_path);

                $attachment = [
                    'post_mime_type' => mime_content_type($file_path),
                    'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                    'post_content' => '',
                    'post_status' => 'inherit'
                ];

                // Insert the image as an attachment
                $attach_id = wp_insert_attachment($attachment, $file_path, $product_id);

                // Add image to the product gallery
                if ($attach_id && !is_wp_error($attach_id)) {
                    // Set the product image
                    set_post_thumbnail($product_id, $attach_id);

                    // set gallery
                    $gallery_ids = get_post_meta($product_id, '_product_image_gallery', true);
                    $gallery_ids = explode(',', $gallery_ids);

                    // Add the new image to the existing gallery
                    $gallery_ids[] = $attach_id;

                    // Update the product gallery
                    update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));
                }
            }

            // update ean post meta 
            $metabox_options = get_post_meta( $product_id, 'sde_metabox_options', true );
            if(!$metabox_options){
                $metabox_options = array();
            }
            $metabox_options['ean'] = $ean;
            $metabox_options['brand'] = $brand;
            $metabox_options['color'] = $color;
            $metabox_options['size'] = $size;
            $metabox_options['matter'] = $matter;
            $metabox_options['care'] = $care;
            $metabox_options['customs-nomenclature'] = $customs_nomenclature;
            $metabox_options['recommended-retail-price'] = $recommended_retail_price;
            $metabox_options['weight'] = $weight;
            $metabox_options['volume'] = $volume;
            $metabox_options['sustainability'] = $sustainability;
            $metabox_options['pcb'] = $pcb;
            $metabox_options['pcb-dropshipping'] = $pcb_dropshipping;
            $metabox_options['country-code'] = $country_code;
            $metabox_options['theme'] = $theme;

            update_post_meta( $product_id, 'sde_metabox_options', $metabox_options );

        }

        // update status to completed
        $wpdb->update( 
            $table_name, 
            array( 
                'status' => 'completed'
            ), 
            array( 'id' => $product->id )
        );
        
    }

    

    return [
        'success' => true,
        'message' => 'Products added successfully'
    ];

    return ob_get_clean();
}

