<?php
  	global $user,$wpdb;
  	include("../../../../wp-load.php");
	

  	$idPlayList=$_GET['id'];
	$page=$_GET['page'];
	
    $table_name = $wpdb->prefix . 'wimtvpro_playlist';
  	$playlist = $wpdb->get_results("SELECT listVideo,name FROM {$table_name} WHERE id='" . $idPlayList . "'");
	foreach ($playlist as $record) {
		$listVideo = $record->listVideo;
		$title = $record->name;
	}
	
	$uploads_info = wp_upload_dir();
    $directory = $uploads_info["baseurl"] .  "/skinWim";
    $nameFile = "/playlist_" .  $idPlayList . ".xml";
        
     if (!isset($page))	{
    		$height = get_option("wp_heightPreview") +190;
			$width = get_option("wp_widthPreview") +280;
			$widthP = get_option("wp_widthPreview") +250; 	

			echo "<div style='text-align:center;height:" . $height . "px;width:" . $width . "px;'><h3>" . $title . "</h3>";
			$playlistSize = "30%";
			$dimensions = "width: '100%',";
			$code = "<div id='container-" . $idPlayList . "' style='margin:0;padding:0 10px;'></div>";

	} else {
		$playlistSize = "30%";
		$dimensions = "width: '100%',";
		$code = "<div id='container-" . $idPlayList . "' style='width: 10px; height: 10px; background-color: #c7cc63;'></div>";
	
	}
	
	//echo "Change order: ";
	$doc = simplexml_load_file($directory . $nameFile);
	$sxe = new SimpleXMLElement($doc->asXML());
	$channel = $sxe->channel;
	$playlist = "";
	foreach ($channel->item as $items) {
	  
	  $urlPlay = explode("$$", $items->file);
	 
	  if (isset($urlPlay[1])) {

	  $playlist .= "{
	     file: '" . $urlPlay[1] . "',
		 streamer:'" . $urlPlay[0] . "',
         'image':'" . $items->image . "',
         'title':'" . str_replace ("+" ," ", urlencode($items->title)) . "'},";
	  //echo $items->title . " ";
	  
	  } else {
	  
	  $playlist .= "{
	     file: '" . $urlPlay[0] . "',
	     'image':'" . $items->image . "',
         'title':'" .  str_replace ("+" ," ", urlencode($items->title)) . "'},";

	  
	  }
	}
	$playlist = substr($playlist , 0, -1);

	
		//For jwplayer 6 - skin is file xml
		/*
		echo "
		<script type='text/javascript'>

		    jwplayer('container').setup({";
		    if (get_option('wp_nameSkin')!="") echo "skin: '" . $directory . "/" . get_option('wp_nameSkin') . ".zip',";
 		echo "
 			width: '" . $widthP . "',
		    height: '" . get_option("wp_heightPreview") . "',

		        'flashplayer': url_pathPlugin + 'script/jwplayer/player.swf',
		        'playlist': [" .  $playlist . "],

			listbar: {
		        position: 'right',
		        size: 250
		    },
		    });
		   
		</script>";
		*/
		
		//For jwplayer 5
		$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf"; 
		$code .= "<script type='text/javascript'>jwplayer('container-" . $idPlayList . "').setup({";
		$code .=  "modes: [{type: 'flash',src:'" . $dirJwPlayer . "'}],";
        if (get_option('wp_nameSkin')!="") {
	     $uploads_info = wp_upload_dir();
	     $directory =  $uploads_info["baseurl"] .  "/skinWim";
	     $skin = "skin:'" . $directory  . "/" . get_option('wp_nameSkin') . ".zip',";
	    }

 		$code .= $skin . $dimensions . "'flashplayer':'" .  $dirJwPlayer . "','playlist': [" .  $playlist . "],'playlist.position': 'right',	'playlist.size': '" . $playlistSize  . "'});</script>&nbsp;";
		
		echo $code;
	if (!isset($page))	{
		echo "<p>Embedded:</p><textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus(); this.select();'>" . htmlentities($code) . "</textarea>";

		echo "</div>";
	}


?>