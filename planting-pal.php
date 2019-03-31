<?php
/*
 * Plugin Name: Planting Pal
 * Plugin URI: https://becomeindelible.com
 * Description: Sets up and displays the user interface for planting pal
 * Author: Indelible Inc.
 * Version: 0.1.0
 * Author URI: https://becomeindelible.com
 * License: GPL2+
 * Github Plugin URI: tonalqualityx/planting-pal
 */


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security

define('INDPPL_ROOT_PATH', plugin_dir_path(__FILE__));
define('INDPPL_ROOT_URL', plugin_dir_url(__FILE__));

require_once(INDPPL_ROOT_PATH . "/functions.php");
require_once(INDPPL_ROOT_PATH . "/shortcodes.php");
require_once(INDPPL_ROOT_PATH . "/conversion.php");
require_once(INDPPL_ROOT_PATH . "/ajax-functions.php");

function indppl_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css');
    wp_register_script( 'indppl-js', INDPPL_ROOT_URL . 'js/app.js', array( 'jquery' ), true);
    wp_localize_script( 'indppl-js', 'indppl_ajax',
      array(
         'ajaxurl' => admin_url( 'admin-ajax.php' ),
         'pluginDirectory' => plugins_url(),
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

//Add support for a custom single-store page
function indppl_single_store_template($single) {
    
    global $post;

    if ($post->post_type == "store" && $template !== locate_template(array("single-store.php"))) {   
        return plugin_dir_path(__FILE__) . "/templates/single-store.php";
    }

    return $template;
    
}

add_filter('single_template', 'indppl_single_store_template');