<?php
/**
 * Written by walter at 11/11/13
 */
include('../../../../wp-load.php');


function getCharset() {
    global $wpdb;

    $charset_collate = "";
    if (!empty ($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    if (!empty ($wpdb->collate))
        $charset_collate .= " COLLATE {$wpdb->collate}";
    return $charset_collate;
}

function initDatabase() {
    //createTables();
}

function dropTables() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'wimtvpro_video';
    $wpdb->query("DROP TABLE  {$table_name}");

    $table_name2 = $wpdb->prefix . 'wimtvpro_playlist';
    $wpdb->query("DROP TABLE {$table_name2}");
}

function deleteWimTVPosts() {
    global $wpdb;

    $wpdb->query("DELETE FROM " .  $wpdb->posts . " WHERE post_name LIKE '%my_streaming_wimtv%' OR post_name LIKE '%wimlive_wimtv%'");
}