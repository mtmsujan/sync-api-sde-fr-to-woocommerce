<?php 

// create admin menu page to add only api key 
add_action('admin_menu', 'sde_admin_menu');
function sde_admin_menu() {
    add_menu_page(
        'SDE API Options',
        'SDE API Options',
        'manage_options',
        'sde-plugin',
        'sde_plugin_page',
        'dashicons-admin-generic',
        100
    );
}

// callback function for admin menu page
function sde_plugin_page(){

    // check if user is admin
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    } 

    // check if api key is set
    if ( isset( $_POST['sde_api_key'] ) ) {
        update_option( 'sde_api_key', $_POST['sde_api_key'] );
    }

    // get api key from database
    $sde_api_key = get_option( 'sde_api_key' );

    // current page url
    $current_page_url = admin_url( 'admin.php?page=sde-plugin' );

    // display admin menu page
    if ( isset( $_POST['sde-submit'] ) ) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Settings updated successfully.', 'sde-plugin' ); ?></p>
        </div>
    <?php } ?>

    <div class="wrap">
        <h1>SDE API Settings</h1>
        <form method="post" action="<?php echo $current_page_url; ?>" novalidate="novalidate">
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="sde_api_key">SDE API Key</label></th>
                        <td><input name="sde_api_key" type="text" id="sde_api_key" value="<?php echo $sde_api_key; ?>" class="regular-text"></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="sde-submit" id="submit" class="button button-primary" value="Save Changes"></p>
        </form>

    </div>

    <?php 
    
}