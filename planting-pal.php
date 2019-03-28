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

function indppl_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'indppl_enqueue');

function page_template_enqueue(){
    if(is_page_template("no-header-no-footer-template.php")){
        wp_enqueue_style('indppl-bootstrap-style', INDPPL_ROOT_URL . 'assets/bootstrap/css/bootstrap.min.css');
        wp_enqueue_style('indppl-template-style', INDPPL_ROOT_URL . 'assets/css/styles.css');
        wp_enqueue_style('indppl-font-style', INDPPL_ROOT_URL . 'assets/fonts/material-icons.min.css');
        wp_enqueue_style('indppl-gfont-berkshire-style', 'https://fonts.googleapis.com/css?family=Berkshire+Swash|Kaushan+Script');
        wp_enqueue_style('indppl-gfont-open-sans-style', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,800');
    }
}
add_action('wp_enqueue_scripts', 'page_template_enqueue');