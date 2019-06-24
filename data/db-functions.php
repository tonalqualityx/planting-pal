<?php
defined('ABSPATH') or die('Sectumsempra!'); //For enemies

function indppl_insert_marketing_data($args = array()) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'indppl_remarketing_data';

    $success = $wpdb->insert($table_name, $args);

    return $success;
}

function indppl_dup_auth($options, $action = 'insert'){
    global $wpdb;

    $table_name = $wpdb->prefix . 'indppl_dup_auth';

    if($action == 'insert'){
        $args = array(
            'user_email' => $options['user_email'],
            'store_id' => $options['store_id'],
        );

        $success = $wpdb->insert($table_name, $args);
    } elseif($action == 'delete'){
        $success = $wpdb->delete($table_name, $options);
    } else {
        $success = "You done messed up a-a-ron!";
    }

    return $success;
}

function indppl_get_dup_auth($id, $user = 'owner'){

    global $wpdb;

    if($user == 'owner'){
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}indppl_dup_auth WHERE store_id = {$id}");
    } elseif($user == 'sub'){
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}indppl_dup_auth WHERE user_email = {$id}");
    } else {
        $results = "Error - you haven't entered the data quite right...";
    }

    return $results;
}