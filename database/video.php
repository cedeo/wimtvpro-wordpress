<?php
/**
 * Written by walter at 11/11/13
 */

function dbInsertVideo($user, $contentId, $state, $status, $urlThumbs, $categories, $urlPlay, $title, $duration, $showtimeId) {
    global $wpdb;
    $video = array("uid" => $user,
                   "contentdentifier" => $contentId,
                   "mytimestamp" => time(),
                   "position" => '0',
                   "state" => $state,
                   "viewVideoModule" => '3',
                   "status" => $status,
                   "urlThumbs" => mysql_real_escape_string($urlThumbs),
                   "category" => $categories,
                   "urlPlay" => mysql_real_escape_string($urlPlay),
                   "title" => mysql_real_escape_string($title),
                   "duration" => $duration,
                   "showtimeidentifier" => $showtimeId);
    return $wpdb->insert(VIDEO_TABLE_NAME, $video);
}

function dbUpdateVideo($state, $status, $title, $urlThumbs, $urlPlay, $duration, $showtimeId, $categories, $contentId) {
    global $wpdb;

    $title = mysql_real_escape_string($title);
    $urlThumbs = mysql_real_escape_string($urlThumbs);
    $urlPlay = mysql_real_escape_string($urlPlay);
    $contentId = mysql_real_escape_string($contentId);

    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("UPDATE {$table} SET state='{$state}',
                                             status='{$status}',
                                             title='{$title}',
                                             urlThumbs='{$urlThumbs}',
                                             urlPlay='{$urlPlay}',
                                             duration='{$duration}',
                                             showtimeidentifier='{$showtimeId}',
                                             category='{$categories}'
                                         WHERE contentidentifier='{$contentId}'");
}

function dbDeleteVideo($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("DELETE FROM {$table} WHERE contentidentifier={$contentIdentifier}");
}

function dbGetVideo($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->get_results("SELECT * FROM {$table} WHERE contentidentifier={$contentIdentifier}");
}


function dbGetUserVideosId($user) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->get_results("SELECT contentidentifier FROM {$table} WHERE uid='{$user}'");
}

function dbBuildGetVideosWhere($showtime, $public) {
    $where = "";
    if ($showtime)
        $where .= "AND state='showtime'";
    if ($public) {
        $where .= "AND ((viewVideoModule like '{$public}%') OR (viewVideoModule like '3%')) ";
    }
    return $where;
}

function dbGetVideosCount($user, $showtime, $public) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $where = dbBuildGetVideosWhere($showtime, $public);
    $query = "SELECT count(*) as count FROM {$table} WHERE uid='{$user}' " . $where;
    return $wpdb->get_results($query);
}

function dbGetUserVideos($user, $showtime, $public, $offset, $rows) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $where = dbBuildGetVideosWhere($showtime, $public);
    $query = "SELECT * FROM {$table} WHERE uid='{$user}' {$where} ORDER BY mytimestamp DESC LIMIT {$offset}, ${rows}";
    return $wpdb->get_results($query);
}