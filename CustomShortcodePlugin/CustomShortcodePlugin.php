<?php

/*

* Plugin Name: Affiliate Disclaimer Shortcode Plugin

* Description: A Simple plugin to add an affiliate disclaimer to your blog post with a shortcode.

* Version: 1.0

* Author: Kara Rajchel

* Author URI: https://kararajchel.com

* Plugin URI: https://github.com/karavt/shortcodeplugin.git

*/


add_action('admin_head', 'kr_add_csp_button');
function kr_add_csp_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter('mce_external_plugins', 'kr_add_tinymce_plugin');
        add_filter('mce_buttons', 'kr_register_csp_button');
    }
}
function kr_add_tinymce_plugin($plugin_array) {
    $plugin_array['kr_csp_button'] = plugins_url( 'js/disclaimer-button.js', __FILE__ );
    return $plugin_array;
}
function kr_register_csp_button($buttons) {
   array_push($buttons, "kr_csp_button");
   return $buttons;
}


// Shortcodes

function affiliate_disclaimer(){

return '<p><em>Disclaimer: The following contains affiliate links. That means that if you make a purchase by using the links I will recieve monetary compensation. This does not impact my opion of the products or services I share with you. I only share products/services that I believe in. <a href="/disclosure/">Read my full disclosure statement here.</a></em></p>';

}
add_shortcode('affiliate', 'affiliate_disclaimer');

?>
