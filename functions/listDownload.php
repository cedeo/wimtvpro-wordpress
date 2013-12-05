<?php
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
  $array =  explode (",",$stateView[1]);
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
	
	$replace_video = apiGetThumbsVideo($content_item_new);

    $licenseType = "";
	if ( $showtime_identifier!=""){	
		$licenseType = isset($stLicense[$showtime_identifier]) ? $stLicense[$showtime_identifier] : "";
	}

   }
   
   
	$isfound = false;
	if ((!strstr($replace_video, 'Not Found')) || (!isset($replace_video)) || ($replace_video==""))
	  $isfound = true; 
	 $licenze_video="";
    if ($isfound!="") {
      $replace_video = '<img src="' . $replace_video . '" title="' . $title . '" class="" />';
      if ($licenseType!="") $licenze_video = '<div class="icon_licence ' . $licenseType . '"></div>';
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
	  $action .= "<td><textarea style='resize: none; width:90%;height:100%; readonly='readonly' onclick='this.focus(); this.select();'>[streamingWimtv  id='" . $content_item_new . "' width='" . get_option("wp_widthPreview") . "' height='" . get_option("wp_heightPreview")  . "']</textarea></td>";
      
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
		$href_view = wimtvpro_checkCleanUrl("embedded", "embeddedAll.php?c=" . $content_item_new. "&s=" . $showtime_identifier);
	else
    	$href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
    $title_view = __("View Video","wimtvpro");
    $play=TRUE;
    
  }
  else {
    $style_view = "";
    if ($urlPlay!="") {
      $href_view = wimtvpro_checkCleanUrl("embedded", "embeddedAll.php?c=" . $content_item_new);
      $play=TRUE;
    }
    else $play=FALSE;
    $title_view = __("Preview Video","wimtvpro");
  }
   $linkView = "";

   if($play==TRUE){
     $action .= "<td><a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
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
	$href_view = wimtvpro_checkCleanUrl("embedded", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
	$action = "<td style='display:none;'><a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a></td>";
	  
  }

	if ($playlist) $action = "";
    $my_media .= "<td class='image'>" .  $licenze_video  . $video . "<br/>";
	if ($private)
		$my_media .="<b>" . $title . "</b>";
	$my_media .= $linkView . "</td>" . $action ;
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

function downloadVideo($id, $infofileName) {
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

?>