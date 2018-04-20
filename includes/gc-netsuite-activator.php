<?php

class Gc_Netsuite_Activator
{
    public static function activate(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'gc_netsuite_posts';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id int NOT NULL AUTO_INCREMENT,
        url nvarchar(255),
        data nvarchar(255),
        success boolean DEFAULT NULL,
        error boolean DEFAULT NULL,
        message nvarchar(255),
        UNIQUE KEY id(id)
        ) $charset_collate;";

        require(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

