<?php

/**
 * Written by walter at 11/11/13
 * Updated by Netsense s.r.l. 2016
 */
function dbInsertVideo($user, $contentId, $state, $status, $urlThumbs, $boxId, $urlPlay, $title, $duration, $vodId, $thumbnailId, $source, $vodCount) {
    global $wpdb;

    $video = array(
        "uid" => $user,
        "boxId" => $boxId,
        "contentidentifier" => $contentId,
        "mytimestamp" => time(),
        "position" => '0',
        "state" => $state,
        "viewVideoModule" => '3',
//        "acquiredIdentifier" => "acquired_identifier",
        "status" => $status,
        "urlThumbs" => isset($urlThumbs) ? $urlThumbs : "",

        "urlPlay" => "urlPlay",
        "title" => $title,
        "duration" => "00:50",
        "showtimeIdentifier" => $vodId,
        "thumbnailId" => $thumbnailId,
        "source" => $source,
        "vodCount" => $vodCount);

    $res = $wpdb->insert(VIDEO_TABLE_NAME, $video);

    return $res;
}

function dbUpdateVideo($state, $status, $title, $urlThumbs, $urlPlay, $duration, $vodId, $boxId, $contentId, $thumbnailId, $source, $vodCount) {
    global $wpdb;

//    $title = mysql_real_escape_string($title);
//    $urlThumbs = mysql_real_escape_string($urlThumbs);
//    $urlPlay = mysql_real_escape_string($urlPlay);
//    $contentId = mysql_real_escape_string($contentId);

    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("UPDATE {$table} SET state='{$state}',
                                             status='{$status}',
                                             title='{$title}',
                                             urlThumbs='{$urlThumbs}',
                                             urlPlay='{$urlPlay}',
                                             duration='{$duration}',
                                             showtimeIdentifier='{$vodId}',
                                             boxId='{$boxId}',
                                             thumbnailId='{$thumbnailId}',
                                             source='{$source}',
                                             vodCount='{$vodCount}'
                                         WHERE contentidentifier='{$contentId}'");
}

function dbUpdateMetadati($title,$boxId) {
    global $wpdb;

    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("UPDATE {$table} SET 
                                             title='{$title}'
                                         WHERE boxId='{$boxId}'");
}

function dbUpdateVideoVod($licenseType,$pricePerView,$boxId) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("UPDATE {$table} SET 
                                             licenseType='{$licenseType}',
                                             price_per_view='{$pricePerView}'
                                         WHERE boxId='{$boxId}'");
}

function dbUpdateVodId($vodId, $boxId) {

    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("UPDATE {$table} SET showtimeIdentifier='{$vodId}',
                                             boxId='{$boxId}',
                                             state='showtime'
                                         WHERE boxId='{$boxId}'");
}

function dbUpdateVideoThumb($contentId, $urlThumbs) {
    global $wpdb;
//    $urlThumbs = mysql_real_escape_string($urlThumbs);
//    $contentId = mysql_real_escape_string($contentId);
    $table = VIDEO_TABLE_NAME;
    $query = "UPDATE {$table} SET urlThumbs='{$urlThumbs}' WHERE contentidentifier='{$contentId}'";
    return $wpdb->query($query);
}

function dbUpdateVideoThumbnailId($contentId, $thumbnailId) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $query = "UPDATE {$table} SET thumbnailId='{$thumbnailId}' WHERE contentidentifier='{$contentId}'";
    return $wpdb->query($query);
}

function dbUpdateVideoStateByBox($boxId, $state, $vodId = null,$licenseType,$pricePerView) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET state='{$state}'";
    if ($vodId)
        $set .= ", showtimeidentifier='{$vodId}'";
        $set .= ", licenseType='{$licenseType}'";
        if($licenseType == "PAY_PER_VIEW"){
             $set .= ", price_per_view='{$pricePerView}'";
        }
//    return $wpdb->query("UPDATE {$table} {$set} WHERE contentidentifier='{$contentId}'");
    return $wpdb->query("UPDATE {$table} {$set} WHERE boxId='{$boxId}'");
}

function dbUpdateVideoState($boxId, $state, $vodId = null) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET state='{$state}'";
    if ($vodId)
        $set .= ", showtimeidentifier='{$vodId}'";
    return $wpdb->query("UPDATE {$table} {$set} WHERE contentidentifier='{$contentId}'");
}

function dbDeleteVideo($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("DELETE FROM {$table} WHERE boxId='{$contentIdentifier}'");
}

function dbGetVideo($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->get_results("SELECT * FROM {$table} WHERE contentidentifier='{$contentIdentifier}'");
}

function dbGetViewVideoModule($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->get_results("SELECT viewVideoModule FROM {$table} WHERE contentidentifier='{$contentIdentifier}'");
}

function dbSetViewVideoModule($contentId, $state) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET viewVideoModule='{$state}'";
    return $wpdb->query("UPDATE {$table} {$set} WHERE contentidentifier='{$contentId}'");
}

function dbSetVideoPosition($boxId, $position, $state = null) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET position='{$position}'";
    if ($state)
        $set .= ", state='{$state}'";
    return $wpdb->query("UPDATE {$table} {$set} WHERE boxId='{$boxId}'");
}

function dbGetUserVideosId($user, $filter = "") {

    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $filter = "";
    switch ($filter) {
        case "":

            $query = "SELECT contentidentifier FROM {$table} WHERE uid='{$user}'";
            break;
        case "pending":
            $query = "SELECT contentidentifier FROM {$table} WHERE uid='{$user}' AND status LIKE '%|%'";
            break;
        default:
            $query = "SELECT contentidentifier FROM {$table} WHERE uid='{$user}'";
            break;
    }
    return $wpdb->get_results($query);
}

function dbBuildGetVideosWhere($showtime, $public, $additional_where = null) {
    $where = "";
    if ($showtime)
        $where .= "AND state='showtime'";
    if ($public) {
        $where .= "AND ((viewVideoModule like '{$public}%') OR (viewVideoModule like '3%')) ";
    }
    if ($additional_where) {
        $where .= "AND " . $additional_where;
    }
    return $where;
}

function dbGetVideosCount($user, $showtime, $public, $where_clause = null) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $where = dbBuildGetVideosWhere($showtime, $public, $where_clause);
    $query = "SELECT count(*) as count FROM {$table} WHERE uid='{$user}' " . $where;
    return $wpdb->get_results($query);
}

function dbGetUserVideos($user, $showtime, $public, $offset = 0, $rows = 0, $where_clause = null, $sql_order = null) {
    global $wpdb;

    $table = VIDEO_TABLE_NAME;
    $where = dbBuildGetVideosWhere($showtime, $public, $where_clause);
    if (!$showtime) {
        if ($sql_order == null) {
            $sql_order = "mytimestamp DESC";
        }
        $query = "SELECT * FROM {$table} WHERE uid='{$user}' {$where} ORDER BY $sql_order LIMIT {$offset}, ${rows}";
    } else {
        if ($sql_order == null) {
            $sql_order = "position ASC";
        }
        $query = "SELECT * FROM {$table} WHERE uid='{$user}' {$where} ORDER BY $sql_order LIMIT {$offset}, ${rows}";
    }

    return $wpdb->get_results($query);
}

function dbBuildVideosIn($listVideos, $in = true) {
    if (count($listVideos)) {
        //        $where = " AND contentidentifier ";
        $where = " AND boxId ";
        if (!$in)
            $where .= "NOT";
        $where .= " IN (";
        foreach ($listVideos as $index => $video) {
            $where .= "'" . $video . "'";
            if ($index < count($listVideos) - 1)
                $where .= ", ";
        }
        $where .= ")";
        return $where;
    }
    return "";
}

function dbSelectVideosByBoxId($boxId) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $query = "SELECT * FROM {$table} WHERE boxId='{$boxId}'";

    return $wpdb->get_results($query);
}

function dbSelectVideosByContentId($contentid) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $query = "SELECT boxId FROM {$table} WHERE contentidentifier='{$contentid}'";

    return $wpdb->get_results($query);
}


function dbGetUserVideosIn($user, $listVideos, $showtime = false, $playlist = true) {
    global $wpdb;
    $and_showtime = "";
    if ($showtime) {
        $and_showtime .= "AND state='showtime'";
    }

    $table = VIDEO_TABLE_NAME;
    $where = dbBuildVideosIn($listVideos, $playlist);

    $query = "SELECT * FROM {$table} WHERE uid='{$user}' {$and_showtime} {$where}";

    return $wpdb->get_results($query);
}