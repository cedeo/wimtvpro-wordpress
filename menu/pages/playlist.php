<?php
/**
 * Written by walter at 24/10/13
 */
 
 include ("modules/playlist-logica.php");
 
 function wimtvpro_playlist() { 
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
					<td><a href="?page=WimTV_Playlist&namefunction=modPlaylist&id=' . $record_new->id . '"><span class="icon_modPlay"></span></a></td>
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
 
?>