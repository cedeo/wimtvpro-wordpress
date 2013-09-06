<?php
/**
  * @file
  * This file is use for the function and utility.
  *
  */

function wimtvpro_getThumbs($showtime=FALSE, $private=TRUE, $insert_into_page=FALSE, $type_public="", $sql_where="",$sql_order="") {
  global $user,$wpdb;
  $upload = true;
  include ("sync.php");
  
  $table_name = $wpdb->prefix . 'wimtvpro_video';
  $my_media= "";
  $response_st = "";
  if (($showtime) && ($showtime=="TRUE")) $sql_where .= " AND state='showtime'";
  if (!$private) {
    if ($type_public == "block") {
      $sql_where .= " AND ((viewVideoModule like '1%') OR (viewVideoModule like '3%')) ";
    }
    if ($type_public == "page") {
      $sql_where .= " AND ((viewVideoModule like '2%') OR (viewVideoModule like '3%')) ";
    }
  }

  

  $array_videos_new_wp = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND position<>0 " . $sql_where . " ORDER BY Position,Title ASC");


  if ($sql_order == "") $sql_order = "Title ASC";
	

  $array_videos_new_wp0 = $wpdb->get_results("SELECT * FROM  {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "' AND  position=0 " . $sql_where . " ORDER BY " . $sql_order);

  //Select Showtime
  $param_st = get_option("wp_basePathWimtv") . "users/" . get_option("wp_userWimtv") . "/showtime?details=true";
  $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
  $ch_st = curl_init();
  curl_setopt($ch_st, CURLOPT_URL, $param_st);
  curl_setopt($ch_st, CURLOPT_VERBOSE, 0);
  curl_setopt($ch_st, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch_st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch_st, CURLOPT_USERPWD, $credential);
  curl_setopt($ch_st, CURLOPT_SSL_VERIFYPEER, FALSE);
  $details_st  =curl_exec($ch_st);
  $arrayjSonST = json_decode( $details_st);
  $stLicense = array();
  foreach ($arrayjSonST->items as $st){
  	$stLicense[$st->showtimeIdentifier] = $st->licenseType;
  }
  $position_new=1;
  //Con posizione
  if (count($array_videos_new_wp  )>0) {
    foreach ($array_videos_new_wp   as $record_new) {
      $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense);
    }
  }
  //Position 0
  if (count($array_videos_new_wp0)>0) {
    foreach ($array_videos_new_wp0 as $record_new) {
      $my_media .= wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense);
    }
  }
  return $my_media;
}

function wimtvpro_listThumbs($record_new, $position_new, $replace_content, $showtime, $private, $insert_into_page,$stLicense) {
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
  
   if ((!isset($replace_video)) || ($replace_video == "")) {
    $param_thumb = get_option("wp_basePathWimtv") . str_replace(get_option("wp_replaceContentWimtv"), $content_item_new, get_option("wp_urlThumbsWimtv"));
    $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
    $ch_thumb = curl_init();
    curl_setopt($ch_thumb, CURLOPT_URL, $param_thumb);
    curl_setopt($ch_thumb, CURLOPT_VERBOSE, 0);
    curl_setopt($ch_thumb, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_thumb, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_thumb, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_thumb, CURLOPT_SSL_VERIFYPEER, FALSE);
    $replace_video  =curl_exec($ch_thumb);

	if ( $showtime_identifier!=""){	
		$licenseType = $stLicense[$showtime_identifier];	
	}
	
	$isfound = false;
	if (!strstr($replace_video, 'Not Found'))
	  $isfound = true; 
	
    if ($isfound) {
      $replace_video = '<img src="' . $replace_video . '" title="' . $title . '" class="" />';
      if ($licenseType!="") $replace_video .= '<div class="icon_licence ' . $licenseType . '"></div>';
	} 
	
   }
   
   $wimtvpro_url = "";
   //For Admin
   if ($isfound) {
      if ((!$private) && (!$insert_into_page)) 
        $wimtvpro_url = wimtvpro_checkCleanUrl("pages", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
      if (($private) && ($insert_into_page))
        $wimtvpro_url = wimtvpro_checkCleanUrl("pages", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
	  $video  = "<a class='wimtv-thumbnail' href='" . $wimtvpro_url . "'>" . $replace_video . "</a>";
   } else {
      $replace_video = false;
   }
   if ($replace_video) {
   
   
   
    $form_st = '
		<div class="free">' . __("FREE OF CHARGE","wimtvpro") . '</div>
		
		<div class="cc">CREATIVE COMMONS</div>
		
		<div class="ppv">PAY PER VIEW</div>
    ';
   if (!$insert_into_page) {
    $my_media .= "<li id='" . $content_item_new . "'>";
   }
   else
     $my_media .= "<li>";
   $form = "";
   if ($private)
     $my_media .= "<div class='thumb ui-state-default'>";
   else 
     $my_media .= "<div class='thumbPublic'>";
   
   if ($state!="showtime") 
     $my_media .= "<span title='" . __("Remove") . "' class='icon_remove' " . $id . " ></span>";
   
   if ($private) {
   
   	$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosWimtv") . "/" . $content_item_new . "?details=true";
	$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		    
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,  $url_video);
	curl_setopt($ch, CURLOPT_USERAGENT,$userAgent); 
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $credential);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
	$arrayjson   = json_decode($response);
	
	//var_dump ($response);

   
     $my_media .= "<div class='headerBox'><div class='icon'>";
   
   if ((!$showtime) || (trim($showtime)=="FALSE")) {
    $id  = "";
    $title_add = __("Add") .  " My Streaming";
    $title_remove = __("Remove") .  " My Streaming";
    if ($state!="") {
      //The video is into My Streaming
      $id= "id='" . $showtime_identifier . "'";
      if ($status=="ACQUIRED") {
        $class_r = "AcqRemoveshowtime";
        $class_a = "AcqPutshowtime";
      }
      else{ 
        $class_r = "Removeshowtime";
        $class_a = "Putshowtime";
      }
      if ($user->roles[0] == "administrator"){
        $my_media .= "<span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . "></span>";
        $my_media .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " style='display:none;'></span>";
      }
    } 
    else {
      //The video isn't into showtime	
    		
      $id = "id='" . $acquider_id . "'";
      if ($status=="ACQUIRED") {
        $class_r = "AcqRemoveshowtime";
        $class_a = "Acqputshowtime";
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
          $my_media .= "<span title='" . $title_remove . "' class='icon_" . $class_r . "' " . $id . " style='display:none;'></span>";
          $my_media .= "<span title='" . $title_add . "' class='add icon_" . $class_a . "' " . $id . " ></span>";

        }
      }
    }
    
    $form = "<div class='formVideo'>" . $form_st . "</div>";
   }
   else {
    if ($user->roles[0] == "administrator"){
      $my_media .= "<span class='icon_RemoveshowtimeInto' title='Remove to My Streaming' id='" . $showtime_identifier . "'></span>";
      $my_media .= "<span class='icon_moveThumbs' title='Change Position'></span>";
      $my_media .= "<span class='icon_viewVideo' rel='" . $view_video_state . "' title='Video Privacy'></span>";
      
      if ($licenseType!="PAYPERVIEW") $my_media .= "<span class='icon_playlist' rel='" . $showtime_identifier . "' title='Add to Playlist selected'></span>";
    }
   }
  
  $my_media .= "<span class='icon_download' id='" . $content_item_new . "|" . $status_array[1] . "' title='Download'></span>";
  
  if ($showtime_identifier!="") {
    $style_view = "";
    $href_view = wimtvpro_checkCleanUrl("pages", "embedded.php?c=" . $content_item_new . "&s=" . $showtime_identifier);
    $title_view = "View Video";
    $play=TRUE;
    
  }
  else {
    $style_view = "";
    if ($urlPlay!="") {
      $href_view = wimtvpro_checkCleanUrl("pages", "embeddedAll.php?c=" . $content_item_new);
      $play=TRUE;
    }
    else $play=FALSE;
    $title_view = "Preview Video";
  }
 
   if($play==TRUE)
     $my_media .= "<a class='viewThumb' " . $style_view . " title='" .  $title_view . "' href='#' id='" . $href_view . "'><span class='icon_view'></span></a>";
    
   $my_media .= "	</div>" . $form . "<div class='loader'></div></div>"; 


 
  }
 
    if ($insert_into_page) {
      if ($showtime_identifier!="")
        //$replace_video = str_replace('#thumbnail-' . $content_item_new. '"' , 'embedded/
        $my_media .= "<div class='headerBox'>";//<div class='icon'><a class='addThumb' href='#' id='" . $showtime_identifier . "'>" . __("Add") . "</a>  <a class='removeThumb' href='#' id='" . $showtime_identifier . "'>" . __("Remove") . "</a></div>";
    }
    $my_media .= $video . "<div class='title'>" . $title . "</div>";
    if ($insert_into_page) {
      $my_media .= '<input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';
      $my_media .= "W <input style='width:30px;' maxweight='3' class='w' type='text' value='" . get_option("wp_widthPreview") . "'>px  -  H <input style='width:30px;' maxweight='3' class='h' type='text' value='" . get_option("wp_heightPreview") . "'>px";
      $send = get_submit_button( __( 'Insert into Post',"wimtvpro" ), 'buttonInsert', $content_item_new, false );
    }  
    $my_media .= $send .  "</div> </li>";
    $position_new = $position;
  }
 }
  return $my_media;
}

//MY STREAMING: This API allows to list videos in my streaming public area. Even details may be returned
function wimtvpro_detail_showtime($single, $st_id) {
  if (!$single) {
    $url_detail =  get_option("wp_basePathWimtv") . str_replace(get_option("wp_replaceUserWimtv"), get_option("wp_userWimtv"), get_option("wp_urlShowTimeDetailWimtv")) ;
  } 
  else {
    $showtime_item = $st_id;
    $url_embedded =  get_option("wp_urlShowTimeWimtv") . "/" . get_option("wp_replaceshowtimeIdentifier") . "/details";
    $replace_content = get_option("wp_replaceContent");
    $url_detail = str_replace(get_option("wp_replaceshowtimeIdentifier"), $showtime_item , $url_embedded);
    $url_detail = str_replace(get_option("wp_replaceUserWimtv"), get_option("wp_userWimtv"), $url_detail);
    $url_detail = get_option("wp_basePathWimtv") . $url_detail;
  }
  $st = curl_init();
  //echo $url_detail;
  curl_setopt($st, CURLOPT_URL, $url_detail);
  curl_setopt($st, CURLOPT_VERBOSE, 0);
  curl_setopt($st, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($st, CURLOPT_SSL_VERIFYPEER, FALSE);
  $array_detail = curl_exec($st);
  curl_close($st);
  //var_dump ($array_detail );
  return $array_detail;
}

function wimtvpro_elencoLive($type, $identifier,$onlyActive=true){
echo '
<script type="text/javascript"> 

jQuery(document).ready(function(){
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


function wimtvpro_savelive ($function){


if (isset($_POST["wimtvpro_live"])) {
  //Modify new event live
  $error = 0;
  //Check fields required
    
  if (strlen(trim($_POST['name']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a wimlive's name.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  if (strlen(trim($_POST['payperview']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a price for your event (or free of charge).","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  if (strlen(trim($_POST['Url']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a url.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  if (strlen(trim($_POST['Giorno']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a day of your event.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  if (strlen(trim($_POST['Ora']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a hour of your event.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  if (strlen(trim($_POST['Duration']))==0) {        
      echo '<div class="error"><p><strong>';
      _e("You must write a duration of your event.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  
  if (!isset($_POST['Public'])) {        
      echo '<div class="error"><p><strong>';
      _e("You must check if you event is public or private.","wimtvpro");
      echo '</strong></p></div>';
      $error ++;
  }
  
  if ($error==0) {
     $name = $_POST['name'];
     $payperview = $_POST['payperview'];
     if ($payperview=="0") 
       $typemode = "FREEOFCHARGE";
     else {
     
       $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, get_option("wp_basePathWimtv") . "uuid");

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $paymentCode= curl_exec($ch);
      curl_close($ch);
           
       $typemode = "PAYPERVIEW&pricePerView=" . $payperview . "&ccy=EUR&paymentCode=" . $paymentCode;
     }
     $url = $_POST['Url'];
     
     if ($_POST['Giorno']!="") {
       $giorno = $_POST['Giorno'];
     } 
     else
       $giorno = "";
     
     if ($_POST['Ora']!="") 
       $ora = explode(":", $_POST['Ora']);
     }
    }
    else {
       $ora[0] = "";
       $ora[1] = "";
    }
    if ($_POST['Duration']!="") {
      $separe_duration = explode("h", $_POST['Duration']);
      $duration = ($separe_duration[0] * 60) + $separe_duration[1];
    }
    else {
      $duration = 0;
    }
    
    if ($_POST['Public']!="") {
    	$public = $_POST['Public'];
    }
    
    if ($_POST['Record']!="") {
    	$record = $_POST['Record'];
    }

    $userpeer = get_option("wp_userWimtv");
    $fields_string = "name=" . $name . "&url=" . $url . "&eventDate=" . $giorno . "&paymentMode=" . $typemode;
    $fields_string .= "&eventHour=" . $ora[0] . "&eventMinute=" . $ora[1] . "&duration=" . $duration . "&durationUnit=Minute&publicEvent=" . $public;
    
    if ($_POST['eventTimeZone']!="")
      $fields_string .= "&eventTimeZone=" . $_POST['eventTimeZone'];
    else
      $fields_string .= "&timezone=" . $_POST['timelivejs'];
    
    $fields_string .= "&recordEvent=" . $record;
    
    $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
    $url_live = get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts";
    if ($function=="modify")  $url_live .= "/" . $_GET['id'];
    if ($_POST['eventTimeZone']=="")
      $url_live .= "?timezone=" . $_POST['timelivejs'];
	
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_live);
    curl_setopt($ch, CURLOPT_USERPWD, $credential);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response!=""){
      $message = json_decode($response);
      $result = $message->{"result"};
      if ($result=="SUCCESS") {
         echo '<script languaguage="javascript"> 
		<!-- 
		window.location = "admin.php?page=WimVideoPro_WimLive";  
		//--> 
		</script>';

        echo '<div class="updated"><p><strong>';
        if ($function=="modify") _e("Update successfully.","wimtvpro");
        else _e("Insert successfully.");
        echo '</strong></p></div>';
               
      }
      else {
        $formset_error = "";
        foreach ($message->messages as $value) {
          if ($value->message!="")
            $formset_error .= $value->field . "=" . $value->message;
        }
        echo '<div class="error"><p><strong>API wimtvpro error: ' . $formset_error . '</strong></p>' . $result  . '</div>';
      }
  }
}

function update_page_mystreaming(){
  if (get_option("wp_publicPage")=="Yes"){
	  global $user,$wpdb;  
	  $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name LIKE 'my_streaming_wimtv%'");
      $my_streaming_wimtv= array();
      $my_streaming_wimtv['ID'] = $post_id;
      $my_streaming_wimtv['post_content'] = "<ul class='itemsPublic'>" . wimtvpro_getThumbs(TRUE, FALSE, FALSE, "page") . "</ul>";
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


function getDateRange($startDate, $endDate, $format="d/m/Y"){

    //Create output variable

    $datesArray = array();

    //Calculate number of days in the range

    $total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;

    if($days<0) { return false; }

    //Populate array of weekdays and counts

    for($day=0; $day<$total_days; $day++)

    {

        $datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));

    }

    //Return results array

    return $datesArray;

}


function wimtvpro_alert_reg(){

	//If user isn't register or not inser user and password
	if ((get_option("wp_registration")=='FALSE') && ((get_option("wp_userwimtv")=="username") && get_option("wp_passwimtv")=="password")){
	
		echo "<div class='error'>" . __("If you are not a WIMTV's member yet","wimtvpro") . " <a href='?page=WimTvPro_Registration'>" . __("REGISTER","wimtvpro") . "</a> " .  __("or You have not insert the credentials","wimtvpro") . "  <a href='?page=WimTvPro'>" . __("SIGN IT","wimtvpro") . "</a></div>";
		
		return FALSE;
	
	} else {
	
	
		return TRUE;
	
	}
}


function wimtvpro_viever_jwplayer($userAgent,$contentId,$video,$dirJwPlayer){

		$isiPad = (bool) strpos($userAgent,'iPad');
        $urlPlay = explode("$$",$video[0]->urlPlay); 
		$isiPhone = (bool) strpos($userAgent,'iPhone');
		$isiAndroid = (bool) strpos($userAgent,'Android');
		
		if ($isiPad  || $isiPhone || $isiAndroid) {
		
			$contentId = $video[0]->contentidentifier;
			$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosWimtv") . "/" . $contentId . "?details=true";
			$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,  $url_video);
			curl_setopt($ch, CURLOPT_USERAGENT,$userAgent); 
		    curl_setopt($ch, CURLOPT_VERBOSE, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		    curl_setopt($ch, CURLOPT_USERPWD, $credential);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		    $response = curl_exec($ch);
			$arrayjson   = json_decode($response);
			
		}
		
		if ($isiPad  || $isiPhone) {
			$urlPlayIPadIphone = "";
			
			
			$urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
			$configFile = "'file': '" . $urlPlayIPadIphone . "',";	
		
		}
		else if ($isiAndroid){
 			$urlPlayAndroid =$arrayjson->streamingUrl->streamer;
			$filePlayAndroid =$arrayjson->streamingUrl->file;
			var_dump ($arrayjson);
			$configFile = "modes: [ { type: 'html5', config: { file: '" . $arrayjson->url . "','provider': 'video' } }],";
		}
		
		else {
		
			$configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $urlPlay[1] . "','streamer':'" . $urlPlay[0] . "',";
		
		}
  return $configFile;
}

