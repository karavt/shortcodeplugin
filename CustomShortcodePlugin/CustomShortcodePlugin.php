<?php

/*

* Plugin Name: Affiliate Disclaimer Shortcode Plugin

* Description: A Simple plugin to add an affiliate disclaimer to your blog post with a shortcode.

* Version: 1.0

* Author: Kara Rajchel

* Author URI: https://kararajchel.com

* Plugin URI: https://github.com/karavt/shortcodeplugin.git

*/

// Add settings page
class kr_csp_plugin {
    public function __construct() {
    	// Hook into the admin menu
    	add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
        // Add Settings and Fields
    	add_action( 'admin_init', array( $this, 'setup_sections' ) );
    	add_action( 'admin_init', array( $this, 'setup_fields' ) );
    }
    public function create_plugin_settings_page() {
    	// Add the menu item and page
    	$page_title = 'Shortcode Plugin Settings';
    	$menu_title = 'Shortcode Plugin';
    	$capability = 'manage_options';
    	$slug = 'kr_csp';
    	$callback = array( $this, 'plugin_settings_page_content' );
    	$icon = 'dashicons-admin-plugins';
    	$position = 100;
    	add_menu_page( $page_title, $menu_title, $capability, $slug, $callback, $icon, $position );
    }
    public function plugin_settings_page_content() {?>
    	<div class="wrap">
    		<h2>Shortcode Plugin Settings</h2><?php
            if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ){
                  $this->admin_notice();
            } ?>
    		<form method="POST" action="options.php">
                <?php
                    settings_fields( 'kr_csp' );
                    do_settings_sections( 'kr_csp' );
                    submit_button();
                ?>
    		</form>
    	</div> <?php
    }

    public function admin_notice() { ?>
        <div class="notice notice-success is-dismissible">
            <p>Your settings have been updated!</p>
        </div><?php
    }
    public function setup_sections() {
        add_settings_section( 'kr_first_section', 'Affiliate Shortcode Settings', array( $this, 'section_callback' ), 'kr_csp' );
        add_settings_section( 'kr_second_section', 'Newsletter Opt In Form Settings', array( $this, 'section_callback' ), 'kr_csp' );
        add_settings_section( 'kr_third_section', 'Signature Shortcode Settings', array( $this, 'section_callback' ), 'kr_csp' );
    }
    public function section_callback( $arguments ) {
    	switch( $arguments['id'] ){
    		case 'kr_first_section':
    			echo 'Add your own custom affiliate disclaimer text, and link to your full disclosure statement.';
    			break;
    		case 'kr_second_section':
    			echo 'Add your newsletter opt in form.';
    			break;
    		case 'kr_third_section':
    			echo 'Customize your Post Signature';
    			break;
    	}
    }
    public function setup_fields() {
        $fields = array(
        	array(
        		'uid' => 'kr_disclaimer_field',
        		'label' => 'Affiliate Disclaimer Page URL',
        		'section' => 'kr_first_section',
        		'type' => 'text',
        		'placeholder' => 'ex: https://yourblog.com/disclaimer',
        		'helper' => 'Enter the full url of the page of your affiliate disclaimer.'
        	),
          array(
            'uid' => 'kr_disclaimer_text',
            'label' => 'Affiliate Disclaimer Text',
            'section' => 'kr_first_section',
            'type' => 'textarea',
            'placeholder' => 'Enter your affiliate disclaimer text here.',
            'helper' => 'Clearly and concisely declare your affiliate links. A link to your full disclosure statement will follow this text.'
          ),
          array(
            'uid' => 'kr_newsletter',
            'label' => 'Affiliate Disclaimer Text',
            'section' => 'kr_second_section',
            'type' => 'textarea',
            'placeholder' => 'Copy & paste your newsletter form code here.',
          ),
          array(
            'uid' => 'kr_signature',
            'label' => 'Post Signature',
            'section' => 'kr_third_section',
            'type' => 'file',
            'helper' => 'Select an image to be used as your signature.'
            )
        );
    	foreach( $fields as $field ){
        	add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'kr_csp', $field['section'], $field );
            register_setting( 'kr_csp', $field['uid'] );
    	}
    }
    public function field_callback( $arguments ) {
        $value = get_option( $arguments['uid'] );
        if( ! $value ) {
            $value = $arguments['default'];
        }
        switch( $arguments['type'] ){
            case 'text':
            case 'password':
            case 'number':
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
            case 'textarea':
                printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
                break;
            case 'select':
            case 'multiselect':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $attributes = '';
                    $options_markup = '';
                    foreach( $arguments['options'] as $key => $label ){
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if( $arguments['type'] === 'multiselect' ){
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="%1$s[]" id="%1$s" %2$s>%3$s</select>', $arguments['uid'], $attributes, $options_markup );
                }
                break;
            case 'radio':
            case 'checkbox':
                if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
                    $options_markup = '';
                    $iterator = 0;
                    foreach( $arguments['options'] as $key => $label ){
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments['uid'], $arguments['type'], $key, checked( $value[ array_search( $key, $value, true ) ], $key, false ), $label, $iterator );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper );
        }
        if( $supplimental = $arguments['supplimental'] ){
            printf( '<p class="description">%s</p>', $supplimental );
        }
    }
}
new kr_csp_plugin();

// Plugin Functionality

function su_allow_subscriber_to_uploads() {
    $administrator = get_role('administrator');

    if ( ! $administrator->has_cap('upload_files') ) {
        $administrator->add_cap('upload_files');
    }
}
add_action('admin_init', 'su_allow_subscriber_to_uploads');

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

echo '<p><em>';
echo get_option(kr_disclaimer_text);
echo '<a href="';
echo get_option(kr_disclaimer_field);
echo '"> Read my full disclosure statement here.</a></em></p>';

}
add_shortcode('affiliate', 'affiliate_disclaimer');

function newsletter_code(){
  echo get_option(kr_newsletter);
}
add_shortcode('newsletter', 'newsletter_code');

function signature_img(){
  echo get_option(kr_signature);
}
add_shortcode('signature', 'signature_img');

?>
