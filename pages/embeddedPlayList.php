<?php
  	global $user,$wpdb;
  	
	if (!isset($itShortcode))
		include("../../../../wp-load.php");
	
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
  	$idPlayList=$_GET['id'];
	$page=$_GET['page'];

	//Read Playlist's Videos into DB
	
    $table_name = $wpdb->prefix . 'wimtvpro_playlist';
  	$record = $wpdb->get_results("SELECT listVideo,name FROM {$table_name} WHERE id='" . $idPlayList . "'");
	
	$listVideo = $record[0]->listVideo;
	$title = $record[0]->name;
     if (!isset($page))	{
		 
    		$height = get_option("wp_heightPreview") +190;
			$width = get_option("wp_widthPreview") +280;
			$widthP = get_option("wp_widthPreview") +250; 	
			
			if (!isset($itShortcode))
				echo "<div style='text-align:center;height:" . $height . "px;width:" . $width . "px;'><h3>" . $title . "</h3>";
	 		else
				echo "<div style='text-align:center;width:100%;'>";
				
			$playlistSize = "30%";
			$dimensions = "width: '100%',";
			$code = "<div id='container-" . $idPlayList . "' style='margin:0;padding:0 10px;'></div>";

	} else {
		
		$playlistSize = "30%";
		$dimensions = "width: '100%',";
		$code = "<div id='container-" . $idPlayList . "' style='width: 10px; height: 10px; background-color: #c7cc63;'></div>";
	
	}
	
	//Read Data videos
	
	$videoList = explode (",",$listVideo);
	$sql_where  = " 1=2 ";
	for ($i=0;$i<count($videoList);$i++){
	  $sql_where .= "  OR contentidentifier='" . $videoList[$i] . "' ";
	}
	$sql_where = "AND (" . $sql_where . ")"; 
	$array_videos = $wpdb->get_results("SELECT * FROM " .  $wpdb->prefix . "wimtvpro_video WHERE uid='" . get_option("wp_userWimtv") . "' " . $sql_where);

    $array_videos_new_drupal = array();
    for ($i=0;$i<count($videoList);$i++){
	 foreach ($array_videos as $record_new) {
		if ($videoList[$i] == $record_new->contentidentifier){
			array_push($array_videos_new_drupal, $record_new);
		}
	 }
	}
	
	$playlist = "";
	foreach ($array_videos_new_drupal as $videoT){
		$videoArr[0] = $videoT;
		$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
		
		$configFile  = wimtvpro_viever_jwplayer($user_agent,$videoT->contentidentifier,$videoArr,$dirJwPlayer);
		if (!isset($videoT->urlThumbs)) $thumbs[1] = "";
		else $thumbs = explode ('"',$videoT->urlThumbs);
		
		$playlist .= "{" . $configFile . " 'image':'" . $thumbs[1]  . "','title':'" . str_replace ("+"," ",urlencode($videoT->title)) . "'},";
	
	}

	$uploads_info = wp_upload_dir();

	//Create jwplayer playlist

	$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf"; 
	$code .= "<script type='text/javascript'>jwplayer('container-" . $idPlayList . "').setup({";
	$urlPlay = explode("$$",$video[0]->urlPlay);
	
	//Check if browser is mobile
	$isiPad = (bool) strpos($user_agent,'iPad');
    $isiPhone = (bool) strpos($user_agent,'iPhone');
    $isAndroid = (bool) strpos($user_agent,'Android');
	$mobile = false;
	if ($isiPad  || $isiPhone || $isAndroid) {
		$mobile=true;
	}
	
	//Mobile: HTML5, Web: Flash
    if (!$mobile) {
		$code .=  "modes: [{type: 'flash',src:'" . $dirJwPlayer . "'}],";
	} else
		$code .=  "modes: [{type: 'html5'}],";
		
	$code .="'repeat':'always',";
	$skin = "";

		$uploads_info = wp_upload_dir();
		$nomeFilexml  = wimtvpro_searchFile($uploads_info["basedir"] .  "/skinWim/" . get_option('wp_nameSkin'),"xml");
		if (get_option('wp_nameSkin')!="") {
			$directory =  $uploads_info["baseurl"] .  "/skinWim"; 
			$skin = "'skin':'" . $directory  . "/" . get_option('wp_nameSkin') . "/" . $nomeFilexml . "',";
		}
	
	$code .= $skin . $dimensions . "'fallback':'false',";
	$code .= "playlist: [" . $playlist . "],";
	$code .= "'playlist.position': 'right',	'playlist.size': '" . $playlistSize  . "'});</script>";
	echo $code;
	
	//echo htmlspecialchars($code);
	
	if (!isset($itShortcode)){
		
		if (!isset($page))	{
			echo "<div style='float:left; width:50%;'>
			Embedded:<textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus(); this.select();'>" . htmlentities($code) . "</textarea></div>";
			
			echo "<div style='float:left; width:50%;'>Shortcode:<textarea style='resize: none; width:90%;height:70px;font-size:20px' readonly='readonly' onclick='this.focus(); this.select();'>[playlistWimtv id='" . $idPlayList . "']</textarea></div>";
			
		}
	}
	echo "</div>";

?>