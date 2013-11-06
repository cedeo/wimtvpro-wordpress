<?php
/**
  * @file
  * This file is use for the function and utility General.
  *
  */


function wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense,$playlist) {
  global $user,$wpdb;
  $form = "";
  $my_media= "";
  $content_item_new = $record_new -> contentidentifier;
  $state = $record_new -> state;
  $position = $record_new -> position;
  $status_array = explode("|",$record_new -> status);
  $urlThumbs = $record_new -> urlThumbs;
  $urlPlay = $record_new -> urlPlay;
  $acquider_id = $record_new -> acquiredIdentifier;
  $file_name = explode("|",$record_new -> mytimestamp);
  $view_video_state = $record_new -> viewVideoModule;
  $duration = "";
  $title = $record_new -> title;
  $showtime_identifier = $record_new -> showtimeIdentifier;
  $stateView = explode ("|",$view_video_state);
  $array =  explode (",",$stateView[0]);
  $typeUser["U"] = array();
  $typeUser["R"] = array();
  $viewPublicVideo = FALSE;
  $status = $status_array[0];
  foreach ($array as $key=>$value) {
  	$var = explode ("-",$value);
  	if ($var[0]=="U") {
  		array_push($typeUser["U"], $var[1]);
  	}
  	elseif ($var[0]=="R") {
  		array_push($typeUser["R"], $var[1]);
  	}
  	else
  		$typeUser[$var[0]] = "";

    if (($var[0]=="All") || ($var[0]=="")) {
    
    	$viewPublicVideo = TRUE;
    
    }
   
  }

  $user = wp_get_current_user();
  $idUser = $user->ID;
  $userRole = $user->roles[0];
  //Video is visible only a user

  if ((!$private && $viewPublicVideo) || (($userRole=="administrator") || (in_array($idUser,$typeUser["U"])) || (in_array($userRole,$typeUser["R"])) || (array_key_exists("All",$typeUser)) || (array_key_exists ("",$typeUser)))){
  
   
  /*  $param_thumb = get_option("wp_basePathWimtv") . str_replace(get_option("wp_replaceContentWimtv"), 		$content_item_new, get_option("wp_urlThumbsWimtv"));
	echo $param_thumb;  
    $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
    $ch_thumb = curl_init();
    curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
    curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
    curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);
    $replace_video  =curl_exec($ch_thumb);*/
	
	$replace_video = apiGetThumbsVideo($content_item_new);

    $licenseType = "";
	if ( $showtime_identifier!=""){	
		$licenseType = isset($stLicense[$showtime_identifier]) ? $stLicense[$showtime_identifier] : "";
	}

	

	
	$isfound = false;
	if ((!strstr($replace_video, 'Not Found')) || (!isset($replace_video)) || ($replace_video==""))
	  $isfound = true; 
	
    if ($isfound!="") {
      $replace_video = '<img src="' . $replace_video . '" title="' . $title . '" class="" />';
      if ($licenseType!="") $replace_video .= '<div class="icon_licence ' . $licenseType . '"></div>';
	} else {
		
	}

   }
   
   $wimtvpro_url = "";
   //For Admin
   if ($isfound) {
	  $video  = "<span class='wimtv-thumbnail' >" . $replace_video . "</span>";
   } else {
	  $video  =  $replace_video;
      $replace_video = false;
   }
   if ($replace_video) {

    $form_st = '
		<div class="free">' . __("FREE OF CHARGE","wimtvpro") . '</div>
		
		<div class="cc">' . __("CREATIVE COMMONS","wimtvpro") . '</div>
	';

	if (get_option("wp_activePayment")=="true")
		$form_st .= '<div class="ppv">' . __("PAY PER VIEW","wimtvpro") . '</div>';
	else
		$form_st .= '<div class="ppvNoActive">' . __("PAY PER VIEW","wimtvpro") . '</div>';
   
	
	if (!$insert_into_page) {
	 if ($showtime_identifier!="")  $my_media .= "<tr class='streams' id='" . $content_item_new . "'>";
     else $my_media .= "<tr id='" . $content_item_new . "'>";
   }
   else
     $my_media .= "<tr>";
   $form = "";
   //if ($private)
     //$action .= "<div class='thumb ui-state-default'>";
   //else 
     //$action .= "<div class='thumbPublic'>";
   
  
   if ($private) {

	$response = apiGetDetailsVideo($content_item_new);
	$arrayjson   = json_decode($response);
    $action = "";
   if ((!$showtime) || (trim($showtime)=="FALSE")) {
    $id  = "";
    $title_add = __("Add to WimVod","wimtvpro") ;
    $title_remove = __("Remove from WimVod","wimtvpro");
    if ($state!="") {
      //The video is into My Streaming
      $id= "id='" . $showtime_identifier . "'";
      if ($status=="ACQUIRED") {
        $class_r = "AcqRemoveshowtime";
        $class_a = "AcquPutshowtime";
      }
      else{ 
        $class_r = "Removeshowtime";
        $class_a = "Putshowtime";
      }
      if ($user->roles[0] == "administrator"){
        $action  .= "<td><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . "></span>";
        $action  .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " style='display:none;'></span></td>";
      }
    } 
    else {
      //The video isn't into showtime	
    		
      $id = "id='" . $acquider_id . "'";
      if ($status=="ACQUIRED") {
        $class_r = "AcqRemoveshowtime";
        $class_a = "AcquPutshowtime";
      }
      elseif ($status=="OWNED") {
        $class_r = "Removeshowtime";
        $class_a = "Putshowtime";
      } 
      else {
        $class_a ="";
        $class_r ="";
      }
      
      if ($class_a!="") {
    	
        if ($user->roles[0] == "administrator"){
          $action .= "<td class='icon'><span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . " style='display:none;'></span>";
          $action .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " ></span>";
		  $action .= "<div class='formVideo'>" . $form_st . "</div></td>";
        }
      }
    }
    
   }
   else {
    if ($user->roles[0] == "administrator"){
      $action .= "<td class='icon'><span class='icon_RemoveshowtimeInto' title='Remove to My Streaming' id='" . $showtime_identifier . "'></span></td>";
      $action .= "<td><span class='icon_moveThumbs' title='" . __("Drag","wimtvpro") . "'></span></td>";
      $action .= "<td><span class='icon_viewVideo' rel='" . $view_video_state . "' title='Video Privacy'></span></td>";
      
      /*if ($licenseType!="PAYPERVIEW") $action  .= "<td><span class='icon_playlist' rel='" . $showtime_identifier . "' title='Add to Playlist selected'></span></td>";*/
    }
   }
 
  if ($isfound) {
    $urlVideo = downloadVideo($content_item_new,$status_array[0]);
  	$action .= "<td><a class='icon_download' href='" . $urlVideo . "' title='Download'></a></td>";
  }
  else
  	$action .= "<td><span class='icon_downloadNone' title='Download'></span></td>";
	
  if ($showtime_identifier!="") {
    $style_view = "";
	if (($private) && ($licenseType=="PAYPERVIEW"))
		$href_view = wimtvpro_checkCleanUrl("pages", "embeddedAll.php?c=" . $content_item_new. "&s=" . $showtime_identifier);
	else
    	$href_view = wimtvpro_checkCleanUrl("pages", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
    $title_view = __("View Video","wimtvpro");
    $play=TRUE;
    
  }
  else {
    $style_view = "";
    if ($urlPlay!="") {
      $href_view = wimtvpro_checkCleanUrl("pages", "embeddedAll.php?c=" . $content_item_new);
      $play=TRUE;
    }
    else $play=FALSE;
    $title_view = __("Preview Video","wimtvpro");
  }
 
   if($play==TRUE){
     $action .= "<td><a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
	 $linkView = "";
	 if ($playlist)
	 	$linkView= "<a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a>";
   }else
		$action .= "<td></td>";
     if ($state!="showtime") 
     	$action .= "<td><span title='" . __("Remove") . "' class='icon_remove' " . $id . " ></span></td>";
     else
	 	$action .= "<td></td>";
		
   $action .= $form . "<div class='loader'></div></div>"; 


 
  } else {
    $style_view = "";
    $title_view = "";
	$href_view = wimtvpro_checkCleanUrl("pages", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
	$action = "<td style='display:none;'><a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
	  
  }
 
    if ($insert_into_page) {
      //if ($showtime_identifier!="")
        //$replace_video = str_replace('#thumbnail-' . $content_item_new. '"' , 'embedded/
        //$my_media .= "<div class='headerBox'>";
		//<div class='icon'><a class='addThumb' href='#' id='" . $showtime_identifier . "'>" . __("Add") . "</a>  <a class='removeThumb' href='#' id='" . $showtime_identifier . "'>" . __("Remove") . "</a></div>";
    }
	if ($playlist) $action = "";
    $my_media .= "<td class='image'>" . $video . "<br/><b>" . $title . "</b>" . $linkView . "</td>" . $action ;
    $send = "";
    if ($insert_into_page) {
      $my_media .= '<td><input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';
      $my_media .= "W <input style='width:30px;' maxweight='3' class='w' type='text' value='" . get_option("wp_widthPreview") . "'>px  <br/>  H <input style='width:30px;' maxweight='3' class='h' type='text' value='" . get_option("wp_heightPreview") . "'>px<br/>";
      $send = get_submit_button( __( 'Insert into Post',"wimtvpro" ), 'buttonInsert', $content_item_new, false );
    } 
	$my_media .= $send .  "</td></tr>"; 

    //$my_media .= $send .  "</div> </tr>";
    $position_new = $position;
  }

  return $my_media;
}

function wimtvpro_readOptionCategory(){
	$category="";
	$response = apiGetVideoCategories();
	$category_json = json_decode($response);

	foreach ($category_json as $cat) {
	  foreach ($cat as $sub) {
		$category .= '<optgroup label="' . $sub->name . '">';
		foreach ($sub->subCategories as $subname) {
		  $category .= '<option value="' . $sub->name . '|' . $subname->name . '">' . $subname->name . '</option>';
		}
		$category .= '</optgroup>';
	  }
	}
	return  $category;
}

//MY STREAMING: This API allows to list videos in my streaming public area. Even details may be returned
function wimtvpro_detail_showtime($single, $st_id) {
  if (!$single) {
	$array_detail = apiGetShowtimes();
  }
  else {
    
	$array_detail = apiGetDetailsShowtime($st_id);

  }

  return $array_detail;
}

function wimtvpro_elencoLive($type, $identifier,$onlyActive=true){
    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';

	if ($type!="table")
		echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '
    var timezone = -(new Date().getTimezoneOffset())*60*1000;
	//window.location.assign(window.location + "&timezone="+timezone);

	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "pages/live.php", 		      
			type: "POST",
			dataType: "html",
			async: false,
			data: "type='. $type . '&timezone =" + timezone  + "&id=' . $identifier . '&onlyActive=' . $onlyActive . '",  
			success: function(response) {
';

	if ($type=="table") {
	
		echo 'jQuery("#tableLive tbody").html(response)';
	
	} else {
	
		echo 'jQuery(".live_' . $type . '").html(response)';
	
	}

echo '
			},
	});
});
</script>
';


}


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


function downloadVideo($id,$infofile) {
	$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
	$filename = "";
	$ext = "";
	if ($infofileName!=""){
		$infoFile = explode (".",$infofileName);
		$numeroCount = count($infoFile); // se ci fosse un file che ha più di un punto
		$ext = $infoFile[$numeroCount-1];
		$filename = $infoFile[0];
		for ($i=1;$i<$numeroCount-1;$i++){
			$filename .= "." . $infoFile[$i];
		}
	}
	$url_download = get_option("wp_basePathWimtv") . "videos/" . $id . "/download";
	if ($filename!=""){
		$url_download .= "?filename=" . $filename . "&ext=" . $ext;
	}
	
	$url_info = parse_url($url_download);
	$url_path_info = pathinfo($url_info['path']);
	$url = $url_info['scheme'] . '://' . $credential . '@' .
	$url_info['host']  . $url_path_info['dirname'] .'/'. rawurlencode($url_path_info['basename']);  
	return $url;
}