<?php 
defined('ABSPATH') or die('No script kiddies please!'); //For security

//SETUP THE DATABASE
function indppl_install() {

    global $wpdb;
    $db_version = get_option('indppl_db_version');
    if (!$db_version) {
        update_option('indppl_db_version', '1.0');
    }
    // var_dump($db_version);
    if (version_compare($db_version, '1.0') != 0) {

        $charset_collate = $wpdb->get_charset_collate();
        $table_name      = $wpdb->prefix . 'indppl_remarketing_data';

        $sql = "CREATE TABLE $table_name (

            remarketing_id bigint(20) NOT NULL AUTO_INCREMENT,
            user_email varchar(255) NOT NULL,
            store_id bigint(20) NOT NULL,
            shopping_list longtext,
            plants longtext,
            process_date timestamp,
            PRIMARY KEY  (remarketing_id)
		) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $table_name = $wpdb->prefix . 'indppl_sponsor_stats';

        $sql = "CREATE TABLE $table_name (
			sponsor_stats_id bigint(20) NOT NULL AUTO_INCREMENT,
			sponsorship_id bigint(20) NOT NULL,
			store_id bigint(20) NOT NULL,
            display_date timestamp,
			PRIMARY KEY  (sponsor_stats_id)
		) $charset_collate";

        dbDelta($sql);
    }
}

register_activation_hook(__FILE__, 'indppl_install');
add_action('admin_init', 'indppl_install');