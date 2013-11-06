<?php
/**
  * @file
  * This file is use for the function and utility General.
  *
  */

//MY STREAMING: This API allows to list videos in my streaming public area. Even details may be returned


function update_page_mystreaming(){
  if (get_option("wp_publicPage")=="Yes"){
	  global $user,$wpdb;  
	  $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name LIKE 'my_streaming_wimtv%'");
      $my_streaming_wimtv= array();
      $my_streaming_wimtv['ID'] = $post_id;
      $my_streaming_wimtv['post_content'] = "<div class='itemsPublic'>" . wimtvpro_getVideos(TRUE, FALSE, FALSE, "page") . "</div>";
      wp_update_post($my_streaming_wimtv);
      
      if (get_option("wp_publicPage")=="Yes"){
	    change_post_status($post_id,'publish');
      } else {
        change_post_status($post_id,'private');
      }

      
  }

}

function change_post_status($post_id,$status){
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    wp_update_post($current_post);
}

function wimtvpro_checkCleanUrl($base, $url) {
    return plugins_url($base . "/" . $url, __FILE__);
}

function getDateRange($startDate, $endDate, $format="d/m/Y") {

    //Create output variable

    $datesArray = array();

    //Calculate number of days in the range

    $total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;

    if($total_days<0) {
        return false;
    }

    //Populate array of weekdays and counts

    for($day=0; $day<$total_days; $day++) {
        $datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));
    }

    //Return results array

    return $datesArray;

}

function wimtvpro_alert_reg() {

	//If user isn't registered or not inser user and password
	if ((get_option("wp_registration")=='FALSE') && ((get_option("wp_userwimtv")=="username") && get_option("wp_passwimtv")=="password")){
		echo "<div class='error'>" . __("If you don't have a WimTV account","wimtvpro") . " <a href='?page=WimTvPro_Registration'>" . __("REGISTER","wimtvpro") . "</a> | <a href='?page=WimTvPro'>" . __("LOGIN","wimtvpro") . "</a> " .   __("with your WimTV credentials","wimtvpro") . "</div>";
		return FALSE;
	} else {
		return TRUE;
	}
}

function wimtvpro_viever_jwplayer($userAgent,$contentId,$video,$dirJwPlayer) {

    $isiPad = (bool) strpos($userAgent,'iPad');
    $urlPlay = explode("$$",$video[0]->urlPlay);
    $isiPhone = (bool) strpos($userAgent,'iPhone');
    $isAndroid = (bool) strpos($userAgent,'Android');
	
    if ($isiPad  || $isiPhone || $isAndroid) {

        $contentId = $video[0]->contentidentifier;
        $response = apiGetDetailsVideo($contentId);
        $arrayjson   = json_decode($response);

    }

    if ($isiPad  || $isiPhone) {
        $urlPlayIPadIphone = "";
        $urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
        $configFile = "'file': '" . $urlPlayIPadIphone . "',";
    } else if ($isAndroid) {
        $urlPlayAndroid =$arrayjson->streamingUrl->streamer;
        $filePlayAndroid =$arrayjson->streamingUrl->file;
        $configFile = "file: '" . $arrayjson->url . "',";
    } else {
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $urlPlay[1] . "','streamer':'" . $urlPlay[0] . "',";
    }
    return $configFile;
}

function wimtvpro_unzip($location,$newLocation) {
	require_once(ABSPATH .'/wp-admin/includes/file.php'); //the cheat
	WP_Filesystem();
	return unzip_file($location, $newLocation);
}

function wimtvpro_searchFile($mainDir, $ext) {
	if ($directory_handle = @opendir($mainDir)) {
		//Read directory for skin JWPLAYER
		while (($file = readdir($directory_handle)) !== FALSE) {
			if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
				$explodeFile = explode("." , $file);
				if ($explodeFile[1]==$ext){
					closedir($directory_handle);	   
					return $file;
		 		}
			}
		}
	}
	else {
		$uploads_info = wp_upload_dir();
		if (wimtvpro_unzip($mainDir .".zip", $uploads_info["basedir"] .  "/skinWim")==TRUE) {
			return wimtvpro_searchFile($mainDir, $ext);
		}
	}
} 

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

