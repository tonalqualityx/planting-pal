<?php

//This is a comment! 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );//For security
function indppl_planting_pal_home_ajax(){
    if(isset($_POST['lat'])){
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];

        // do_shortcode('[planting_pal_home]');
        $return = planting_pal_home($lat, $lon);
        echo $return;
    }
    // var_dump($_POST);
    die();
}
add_action( 'wp_ajax_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax' );
add_action('wp_ajax_nopriv_indppl_planting_pal_home_ajax', 'indppl_planting_pal_home_ajax');

function indppl_switch_live_ajax(){
    if(isset($_POST['id'])){
        $store_id = $_POST['id'];
        $status = get_post_meta($store_id, 'wpcf-issetup', true);
        if($status){
            update_post_meta($store_id, 'wpcf-issetup', 0);
            echo 0;
        }else {
            update_post_meta( $store_id, 'wpcf-issetup', 1);
            echo 1;
        }
    }
    die();
}
add_action( 'wp_ajax_indppl_switch_live_ajax', 'indppl_switch_live_ajax' );
add_action('wp_ajax_nopriv_indppl_switch_live_ajax', 'indppl_switch_live_ajax');
