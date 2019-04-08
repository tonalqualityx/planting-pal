<?php
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

function indppl_save_container_data_ajax(){
    if(isset($_POST['store_id'])){
        $store_id = $_POST['store_id'];
    }
    if(isset($_POST['date'])){
        $date = $_POST['date'];
        foreach($date as $key => $value){
            $name = 'wpcf-' . $value['name'];
            $val = $value['value'];
            update_post_meta($store_id, $name, $val);
        }
    }
    if(isset($_POST['default_container'])){
        $non_default = $_POST['non_default'];
        $available = $_POST['available'];
        $default = $_POST['default_container'];
        foreach ($default as $key => $value) {
            $name = explode("-", $value['name']);
            // $default[$key]['id'] = $name[0];
            if(in_array($name[0], $available)){
                echo $value['name'];
                
            }
        }
        
        // $v;
        // foreach($available as $key => $value){
        //     $v = array_keys($default, $value);
        //     // if(in_array($value, $default[$key]['id']){
        //     //     echo $default[$key]['id'];
        //     // }
        //     var_dump($v);
        // }
    }
    die();
}
add_action( 'wp_ajax_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax' );
add_action('wp_ajax_nopriv_indppl_save_container_data_ajax', 'indppl_save_container_data_ajax');