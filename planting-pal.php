<?php
/*
 * Plugin Name: Planting Pal
 * Plugin URI: https://becomeindelible.com
 * Description: Sets up and displays the user interface for planting pal
 * Author: Indelible Inc.
 * Version: 1.1.1
 * Author URI: https://becomeindelible.com
 * License: GPL2+
 * Github Plugin URI: tonalqualityx/planting-pal
 */

add_filter('wp_headers', 'indppl_add_ie_edge_wp_headers');

function indppl_add_ie_edge_wp_headers($headers) {
    if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
        $headers['X-UA-Compatible'] = 'IE=edge,chrome=1';
    }

    return $headers;
}

function my_function_admin_bar($content) {
	return ( current_user_can( 'administrator' ) ) ? $content : false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');



defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security

define('INDPPL_ROOT_PATH', plugin_dir_path(__FILE__));
define('INDPPL_ROOT_URL', plugin_dir_url(__FILE__));

// General Required Files
require_once(INDPPL_ROOT_PATH . "/functions.php");
require_once(INDPPL_ROOT_PATH . "/shortcodes.php");
require_once(INDPPL_ROOT_PATH . "/conversion.php");
require_once(INDPPL_ROOT_PATH . "/ajax-functions.php");
require_once(INDPPL_ROOT_PATH . "/admin-functions.php");
require_once(INDPPL_ROOT_PATH . "/data/db-functions.php");

// Admin Required Files
if(is_admin()){
    require_once(INDPPL_ROOT_PATH . "/data/db.php");
}

function indppl_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css', false, '1.3');
    wp_enqueue_style('font-awesome-backup', "/wp-content/plugins/bb-plugin/fonts/fontawesome/css/all.min.css");
    wp_enqueue_style('print-styles', INDPPL_ROOT_URL . "css/print.css", array(), '', 'print');
    wp_register_script( 'indppl-js', INDPPL_ROOT_URL . 'js/app.js', array( 'jquery' ), '1.13');
    wp_localize_script( 'indppl-js', 'indppl_ajax',
      array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'pluginDirectory' => plugins_url(),
         'guide_nonce' => wp_create_nonce(),
      )
   );
   wp_enqueue_script('indppl-js');
}
add_action('wp_enqueue_scripts', 'indppl_enqueue');

function page_template_enqueue(){

    global $post;

    if(is_page_template("no-header-no-footer-template.php") || $post->post_type == "store"){
        wp_enqueue_style('indppl-bootstrap-style', INDPPL_ROOT_URL . 'assets/bootstrap/css/bootstrap.min.css');
        wp_enqueue_style('indppl-template-style', INDPPL_ROOT_URL . 'assets/css/styles.css');
        wp_enqueue_style('indppl-font-style', INDPPL_ROOT_URL . 'assets/fonts/material-icons.min.css');
        wp_enqueue_style('indppl-gfont-berkshire-style', 'https://fonts.googleapis.com/css?family=Berkshire+Swash|Kaushan+Script');
        wp_enqueue_style('indppl-gfont-open-sans-style', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,800');
    }
}
add_action('wp_enqueue_scripts', 'page_template_enqueue');


function admin_functions_enqueue(){
    wp_enqueue_style('indppl-admin-style', INDPPL_ROOT_URL . 'css/admin-style.css');
    wp_register_script( 'indppl-admin-js', INDPPL_ROOT_URL . 'js/admin-app.js', array( 'jquery' ), true);
    wp_localize_script( 'indppl-admin-js', 'indppl_admin_ajax',
      array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'pluginDirectory' => plugins_url(),
      )
   );
   wp_enqueue_script('indppl-admin-js');
}
add_action('admin_enqueue_scripts', 'admin_functions_enqueue');

//Add support for attaching author to stores, products, and packages
function indppl_cpt_author() {
    add_post_type_support('store', 'author');
    add_post_type_support('product', 'author');
    add_post_type_support('package', 'author');
}
add_action('init', 'indppl_cpt_author');


//Add support for a custom single-store page
function indppl_single_store_template($single) {
    
    global $post;

    if ($post->post_type == "store" && $template !== locate_template(array("single-store.php"))) {   
        return plugin_dir_path(__FILE__) . "/templates/single-store.php";
    }

    //set guides template...
    if($post->post_type == 'guide'){
        return plugin_dir_path(__FILE__) . "/templates/single-guide.php";
    }
    
    return $template;
    
}

add_filter('single_template', 'indppl_single_store_template');

function indppl_set_app_template($template){
    if ( is_page( 'app' ) ) {
        $directory = dirname( __FILE__ );
        return  $directory . '/templates/app.php';
    }

    return $template;
}
add_filter( 'template_include', 'indppl_set_app_template' );

function indppl_meta_tags(){
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
}

add_action('wp_head', 'indppl_meta_tags');

function indppl_set_content_type() {
    return "text/html";
}
add_filter('wp_mail_content_type', 'indppl_set_content_type');

// Add custom template to tempaltes list
function indppl_add_dashboard_template_to_list($post_templates, $wp_theme, $post, $post_type) {

    // Add custom template named template-custom.php to select dropdown
    $post_templates['template-dashboard.php'] = __('Dashboard');

    return $post_templates;
}

add_filter('theme_page_templates', 'indppl_add_dashboard_template_to_list', 10, 4);

// Load dashboard template
function indppl_dashboard_template($template) {

    if (get_page_template_slug() === 'template-dashboard.php') {

        if ($theme_file = locate_template(array('/templates/template-dashboard.php'))) {
            $template = $theme_file;
        } else {
            $template = plugin_dir_path(__FILE__) . '/templates/template-dashboard.php';
        }
    }

    if ($template == '') {
        throw new \Exception('No template found');
    }

    return $template;
}

add_filter('template_include', 'indppl_dashboard_template');


