<?php
defined('ABSPATH') or die('Sectumsempra!'); //For enemies

function indppl_insert_marketing_data($args = array()) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'indppl_remarketing_data';

    $success = $wpdb->insert($table_name, $args);

    return $success;
}