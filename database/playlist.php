<?php
/**
 * Written by walter at 11/11/13
 */
 
 
function dbExtractPlayList($username){
    $table_name= PLAYLIST_TABLE_NAME;
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$table_name}
            WHERE uid='" . $username . "'
            ORDER BY name ASC");
}

function dbDeletePlayist($idPlayList){
    $table_name= PLAYLIST_TABLE_NAME;
    global $wpdb;
    $query = "DELETE FROM {$table_name}
			  WHERE id='" . $idPlayList . "'";
    return $wpdb->query($query);
}

function dbExtractSpecificPlayist($idPlayList){
	$table_name= PLAYLIST_TABLE_NAME;
	global $wpdb;
	$query = "SELECT listVideo,
			name FROM {$table_name}
			WHERE id='" . $idPlayList . "'";
	return $wpdb->get_results($query);
}

function dbGetUSerPlaylist($username, $id) {
    $table_name= PLAYLIST_TABLE_NAME;
    global $wpdb;
    return $wpdb->get_results("SELECT * FROM {$table_name}
            WHERE uid='" . $username . "'
            AND id='" .  $id . "'");
}

function dbUpdatePlaylist($id, $listVideo=null, $namePlaylist=null){
    $table_name= PLAYLIST_TABLE_NAME;
    global $wpdb;

    if ($namePlaylist!=null &&  $listVideo!=null){
        $set=" SET name='" . $namePlaylist . "' ,
		listvideo='" . $listVideo . "'";
    }
    elseif($namePlaylist==null &&  $listVideo==null){
        return null;
    }
    elseif($namePlaylist!=null){
        $set=" SET name='" . $namePlaylist . "'";
    }
    else {
        $set="SET listvideo='" . $listVideo . "'";
    }
    $query="UPDATE " . $table_name  . $set . " WHERE id='" . $id . "'";
    return $wpdb->query($query);
}


function dbInsertPlayist($username, $name){
    global $wpdb;
    $playlist=array('uid' =>$username,
                    'listVideo' => '',
                    'name' =>  $name, );
    return $wpdb->insert(PLAYLIST_TABLE_NAME, $playlist);
}

?>
