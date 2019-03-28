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

function indppl_enqueue(){
    wp_enqueue_style('indppl-style', INDPPL_ROOT_URL . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'indppl_enqueue');
