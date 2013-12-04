<?php
/**
 * Written by walter at 11/11/13
 */

function dbInsertVideo($user, $contentId, $state, $status, $urlThumbs, $categories, $urlPlay, $title, $duration, $showtimeId,$acquired_identifier) {
    global $wpdb;
    $video = array("uid" => $user,
                   "contentidentifier" => $contentId,
                   "mytimestamp" => time(),
                   "position" => '0',
                   "state" => $state,
                   "viewVideoModule" => '3',
				   "acquiredIdentifier" => $acquired_identifier,
                   "status" => $status,
                   "urlThumbs" => mysql_real_escape_string($urlThumbs),
                   "category" => $categories,
                   "urlPlay" => mysql_real_escape_string($urlPlay),
                   "title" => mysql_real_escape_string($title),
                   "duration" => $duration,
                   "showtimeidentifier" => $showtimeId);
    return $wpdb->insert(VIDEO_TABLE_NAME, $video);
}

function dbUpdateVideo($state, $status, $title, $urlThumbs, $urlPlay, $duration, $showtimeId, $categories, $contentId,$acquired_identifier) {
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
											 acquiredIdentifier = '{$acquired_identifier}',
                                             duration='{$duration}',
                                             showtimeidentifier='{$showtimeId}',
                                             category='{$categories}'
                                         WHERE contentidentifier='{$contentId}'");
}

function dbUpdateVideoState($contentId, $state, $showtimeId=null) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET state='{$state}'";
    if ($showtimeId)
        $set .= ", showtimeidentifier='{$showtimeId}'";
    return $wpdb->query("UPDATE {$table} {$set} WHERE contentidentifier='{$contentId}'");
}

function dbDeleteVideo($contentIdentifier) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    return $wpdb->query("DELETE FROM {$table} WHERE contentidentifier='{$contentIdentifier}'");
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

function dbSetVideoPosition($contentId, $position, $state=null) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $set = "SET position='{$position}'";
    if ($state)
        $set .= ", state='{$state}'";
    return $wpdb->query("UPDATE {$table} {$set} WHERE contentidentifier='{$contentId}'");
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

function dbGetUserVideos($user, $showtime, $public, $offset=0, $rows=0) {
    global $wpdb;
    $table = VIDEO_TABLE_NAME;
    $where = dbBuildGetVideosWhere($showtime, $public);
	if (!$showtime){
		$query = "SELECT * FROM {$table} WHERE uid='{$user}' {$where} ORDER BY mytimestamp DESC LIMIT {$offset}, ${rows}";
	} else {
		    $query = "SELECT * FROM {$table} WHERE uid='{$user}' {$where} ORDER BY position ASC LIMIT {$offset}, ${rows}";
	}
    return $wpdb->get_results($query);
}

function dbBuildVideosIn($listVideos, $in=true) {
    if (count($listVideos)) {
        $where = " AND contentidentifier ";
        if (!$in)
            $where .= "NOT";
        $where .= " IN (";
        foreach ($listVideos as $index=>$video) {
            $where .= "'" . $video . "'";
            if ($index < count($listVideos)-1)
                $where .= ", ";
        }
        $where .= ")";
        return $where;
    }
    return "";
}

function dbGetUserVideosIn($user, $listVideos, $showtime=false, $playlist=true) {
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