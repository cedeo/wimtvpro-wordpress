<?php
  /**
  * @file
  * This file is use for the view playlist and function.
  *
  */
function wimtvpro_listplaylist() { 
	$view_page = wimtvpro_alert_reg();
		
	if ($view_page==TRUE){
	
		global $wpdb; 
	

	
		$table_name = $wpdb->prefix . 'wimtvpro_playlist';
		if ($_GET["namefunction"]=="modPlaylist"){
			$linkReturn =  "<a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=listPlaylist' class='add-new-h2'>" . __( 'Return to list', 'wimtvpro') . "</a> ";
		}
			
		echo "<div class='wrap'><h2>Playlist " . $linkReturn  . "</h2>";
		echo "<p>" . __("Create a playlist of videos (ONLY FREE videos are possible) to be posted to your website","wimtvpro") . "</p>";
		echo "<p>" . __("Move videos from left to right","wimtvpro") . "</p>";
		
		if ($_GET["namefunction"]=="modPlaylist"){
			
			
			if ($_POST["modPlaylist"]=="true"){
			
				$sql = "UPDATE " . $table_name  . " SET name='" . $_POST["namePlaylist"] . "' ,listvideo='" . $_POST["listVideo"] . "' WHERE id='" . $_GET["id"] . "'";
				$wpdb->query($sql);		
				echo '<div class="updated"><p><strong>';
				_e("Update successful","wimtvpro");
				echo '</strong></p></div>';
				
			}
			
			$playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND id=" . $_GET["id"]);
			
			if (count($playlist)>0) {
			  $option = $playlist[0]->option;
			  $array_option = explode(",",$option);
			  $options = array();
			  foreach ($array_option as $value){
				$array = explode(":",$value);
				if ($array[0]!="")
				  $options[$array[0]] = $array[1];
			  }	
			} else {
				$options["loop"] = "";
				$playlist[0]->listVideo = "";
			}
			
			$embedded  = "";
	
			echo '<form method="post" action="#">';
			echo '<input type="submit" class="icon_sync0 button-primary" value="' . __("Update","wimtvpro") . '"/>';
			
			echo '<input type="hidden" class="list" name="listVideo" value="' . $playlist[0]->listVideo . '">';
			echo '<input type="hidden" name="modPlaylist" value="true">';
			echo '<input type="hidden" name="idPlaylist" value="' . $_GET["id"] . '">';
			echo "<div id='post-body' class='metabox-holder columns-2'><div id='post-body-content'><div id='titlediv'><div id='titlewrap'><input type='text' id='title' class='title' name='namePlaylist' value='" . $playlist[0]->name . "'></div></div></div></div>";
			echo '</form>';
		
		$page = "<div class='sortable1'>" . __("All video","wimtvpro") . "<table class='items_playlist' id='droptrue'>" . str_replace("<td></td>","",wimtvpro_getThumbs_playlist($playlist[0]->listVideo,TRUE,TRUE,FALSE,"",FALSE)) . "<tr class='appoggio'></tr></table></div>";
		$page .= "<div class='sortable2'><b>" . __("Playlist","wimtvpro") . "</b><table class='items_playlist' id='dropfalse'>" .  str_replace("<td></td>","",wimtvpro_getThumbs_playlist($playlist[0]->listVideo,TRUE,TRUE,FALSE,"",TRUE)) . "<tr class='appoggio'></tr></table>
		";
		
		echo $page;
		
		} else {
	
			echo "<table  id='tablePlaylist' class='items wp-list-table widefat fixed pages'>";
				echo "<thead><tr style='width:100%'><th  style='width:30%'>" .  __("Title") . "</th><th style='width:30%'>N. Video</th><th style='width:20%'>" . __("Preview") . "</th>
				<th style='width:20%'>" . __("Modify","wimtvpro") . "</th>
				<th style='width:20%'>" . __("Remove") . "</th>
				
				</tr></thead>";
				echo "<tbody>";
			//Count playlist saved in DB
	
			$table_name = $wpdb->prefix . 'wimtvpro_playlist';
			$array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "'  ORDER BY name ASC");
			$numberPlaylist=count($array_playlist);
			$count = 1;
			if ($numberPlaylist>0) {
				foreach ($array_playlist as $record_new) {
				
					$listVideo = $record_new->listVideo;
					$arrayVideo = explode(",", $listVideo);
					if ($listVideo=="") $countVideo = 0;
					else $countVideo = count($arrayVideo);
					echo '
					<tr class="playlist" id="playlist_' . $count . '" rel="' . $record_new->id . '">
					
					<td>' . $record_new->name .  '</td>
					<td>' . $countVideo . '</td>
					<td><span class="icon_viewPlay" id="' . $record_new->id . '"></span></td>
					<td><a href="?page=WimVideoPro_Playlist&namefunction=modPlaylist&id=' . $record_new->id . '"><span class="icon_modPlay"></span></a></td>
					<td><span class="icon_deletePlay"></span></td>
					';
					
					echo '</tr>';
					$count +=1;
				}
			}
			echo '<tr class="playlist new" id="playlist_' . $count . '" rel="">';         
			echo '
			<td><input type="text" value="Playlist ' . $count .  '" /><span class="icon_createPlay"></span></td>
			<td></td><td></td><td></td><td></td></div>';
			echo "</tbody></table>";
		}
	}
	
	}

}




function wimtvpro_getThumbs_playlist($list,$showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="",$playlist=FALSE) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'wimtvpro_video';
	$replace_content = get_option("wp_replaceContentWimtv");
	$my_media= "";
	$response_st = "";
	$sql_where  = "  ";
	$videoList = explode (",",$list);
	if ($showtime)
		$sql_where  = "  state='showtime'";
	else
		if ($playlist)
			$sql_where  = "  1=2";
		else
			$sql_where  = "  1=1";
	if ($playlist) {
		for ($i=0;$i<count($videoList);$i++){
			if ($videoList[$i]!="")
				$sql_where .= "  OR contentidentifier='" . $videoList[$i] . "' ";
		}
		$sql_where = "AND (" . $sql_where . ")";  
	} 
	else {
		for ($i=0;$i<count($videoList);$i++){
			if ($videoList[$i]!="")
				$sql_where .= "  AND contentidentifier!='" . $videoList[$i] . "' ";
		}
		$sql_where = "AND (" . $sql_where . ")"; 
	}


 	$array_videos  = $wpdb->get_results("SELECT * FROM " . $table_name . " WHERE uid='" .  get_option("wp_userWimtv") . "' " . $sql_where);

	$array_videos_new_drupal = array();

	if ($playlist==TRUE) {

		for ($i=0;$i<count($videoList);$i++){
			foreach ($array_videos  as $record_new) {
				if ($videoList[$i] == $record_new->contentidentifier){
					array_push($array_videos_new_drupal, $record_new);	
				}
			}

		}
	} else {
		$array_videos_new_drupal = $array_videos;
	}

	//Select Showtime
	$param_st = get_option("wp_basePathWimtv") . "users/" . get_option("wp_userWimtv") . 	"/showtime?details=true";
	$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
	$ch_st = curl_init();
	curl_setopt($ch_st, CURLOPT_URL, $param_st);
	curl_setopt($ch_st, CURLOPT_VERBOSE, 0);
	curl_setopt($ch_st, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch_st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch_st, CURLOPT_USERPWD, $credential);
	curl_setopt($ch_st, CURLOPT_SSL_VERIFYPEER, FALSE);
	$details_st  =curl_exec($ch_st);
	$arrayjson_st = json_decode( $details_st);
	$st_license = array();
	foreach ($arrayjson_st->items as $st){
		$st_license[$st->showtimeIdentifier] = $st->licenseType;
	}
	$position_new=1;
	//Select video with position
	if (count($array_videos_new_drupal )>0) {
		foreach ($array_videos_new_drupal  as $record_new) {
			if ($showtime) {
				if ((isset($st_license[$record_new->showtimeIdentifier])) && ($st_license[$record_new->showtimeIdentifier] !="PAYPERVIEW"))
					$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
			else {
				$my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$st_license,TRUE);
			}
		}
	}

	return $my_media;
}

?>