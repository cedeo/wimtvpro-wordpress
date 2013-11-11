<?php
/**
 * Written by walter at 11/11/13
 */
require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
include_once('playlist.php');
include_once('video.php');

global $wpdb;
define("PLAYLIST_TABLE_NAME", $wpdb->prefix . 'wimtvpro_playlist');
define("VIDEO_TABLE_NAME", $wpdb->prefix . 'wimtvpro_video');

function getCharset() {
    global $wpdb;
    $charset_collate = "";
    if (!empty ($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    if (!empty ($wpdb->collate))
        $charset_collate .= " COLLATE {$wpdb->collate}";
    return $charset_collate;
}

function dropTables() {
    global $wpdb;

    $table_name = VIDEO_TABLE_NAME;
    $wpdb->query("DROP TABLE  {$table_name}");

    $table_name2 = PLAYLIST_TABLE_NAME;
    $wpdb->query("DROP TABLE {$table_name2}");
}

function deleteWimTVPosts() {
    global $wpdb;

    $wpdb->query("DELETE FROM " .  $wpdb->posts . " WHERE post_name LIKE '%my_streaming_wimtv%' OR post_name LIKE '%wimlive_wimtv%'");
}

function createTables() {
    global $wpdb;

    $table_name = VIDEO_TABLE_NAME;
    $charset= getCharset();
    if ( $wp_db_version == $wp_current_db_version ) {
        $query1 = "CREATE TABLE {$table_name}
                    (
                        uid varchar(100) NOT NULL COMMENT 'User identifier',
                        contentidentifier varchar(100) NOT NULL COMMENT 'Contentidentifier Video',
                        state varchar(100) NOT NULL COMMENT 'Showtime or no',
                        filename varchar(100) NOT NULL COMMENT 'Filename and extention',
                        status varchar(100) NOT NULL COMMENT 'OWNED-ACQUIRED-PERFORMING',
                        acquiredIdentifier varchar(100) NOT NULL,
                        mytimestamp int(11) NOT NULL COMMENT 'My timestamp',
                        position int(11) NOT NULL COMMENT 'Position video user',
                        viewVideoModule varchar(100) NOT NULL COMMENT 'View video into page or block',
                        urlThumbs text NOT NULL COMMENT 'Url thumbs video',
                        urlPlay text NOT NULL COMMENT 'Url player video',
                        category text NOT NULL COMMENT 'Category and subcategory video[Json]',
                        title varchar(100) NOT NULL COMMENT 'Title videos',
                        duration varchar(10) NOT NULL COMMENT 'Duration videos',
                        showtimeIdentifier varchar(100) NOT NULL COMMENT 'showtimeIdentifier videos',
                        PRIMARY KEY (contentidentifier),
                        UNIQUE KEY mycolumn1 (contentidentifier)
                    )
                        {$charset};";

    } else {

        $query1 = "  ALTER TABLE   {$table_name}  ADD   urlThumbs text NOT NULL COMMENT 'Url thumbs video' ";

    }
    dbDelta($query1);

    $table_name2 = PLAYLIST_TABLE_NAME;
    $query2 = "CREATE TABLE {$table_name2}
                (
                    id INT NOT NULL AUTO_INCREMENT COMMENT 'Id',
                    name varchar(100) NOT NULL COMMENT 'Name of playlist',
                    uid varchar(100) COMMENT 'User identifier',
                    listVideo varchar(1000) COMMENT 'List video contentidentifier',
                    PRIMARY KEY (id),
                    UNIQUE KEY mycolumn2 (id)
                )
                    {$charset};";

    dbDelta($query2);
}

