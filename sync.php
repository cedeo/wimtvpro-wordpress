<?php
/**
  * @file
  * Synchronize the video with wim.tv.
  *
  */
  

if (!isset($upload))
	include("../../../wp-load.php");
  else
	include("../wp-load.php");

  global $user,$wpdb;
	
	$table_name = $wpdb->prefix . 'wimtvpro_video';
	$response= apiGetVideos();

  	$array_json_videos = json_decode($response);

  if ($array_json_videos==NULL) {
    _e("Can not establish a connection with Wim.tv. Contact the administrator.", "wimtvpro");
  } 
  else {
  //$num = (array)simplexml_load_string($response);
    $i=0;
    foreach ($array_json_videos -> items as $a) {
      foreach ($a as $key => $value) {
        $array_all_videos[$i][$key] = $value;
      }
      $i++;
    }
    $num = count($array_json_videos);
    if ($num > 0 ) {
      $elenco_video_wimtv = array();
      $elenco_video_wp = array();
      $array_videos_new_wp = $wpdb->get_results("SELECT contentidentifier FROM " . $table_name . " WHERE uid = '" . get_option("wp_userwimtv") . "'");
      foreach ($array_videos_new_wp as $record) {
        array_push($elenco_video_wp, $record -> contentidentifier);
      }
      /* Information detail videos into Showtime */
      $json_st   = wimtvpro_detail_showtime(FALSE, 0);
      $arrayjson_st   = json_decode($json_st);
      $values_st = $arrayjson_st->items;
      foreach ($values_st as $key => $value) {
        $array_st[$value -> {"contentId"}]["showtimeIdentifier"] = $value-> {"showtimeIdentifier"};
      }
      if ($array_all_videos) {
	      foreach ($array_all_videos as $video) {
            //echo json_encode($video);
	        $url_video = $video["actionUrl"];
	        $status = $video["status"];
	        //$acquired_identifier = $video["acquired_identifier"];
	        $title= $video["title"];
            if (isset($video["streamingUrl"])) {
                $urlVideo = $video["streamingUrl"]->streamer . "$$";
                $urlVideo .= $video["streamingUrl"]->file . "$$";
                $urlVideo .= $video["streamingUrl"]->auth_token;
            }
	        $duration= $video["duration"];
	        $content_item =  $video["contentId"];
	        $url_thumbs = '<img src="' . $video["thumbnailUrl"] . '"  title="' . $title . '" class="wimtv-thumbnail" />';
	        $categories  = "";
	        $valuesc_cat_st = "";
	        foreach ($video["categories"] as $key => $value) {
	          $valuesc_cat_st .= $value->categoryName;
	          $categories .= $valuesc_cat_st;
	          foreach ($value -> subCategories as $key => $value) {
	            $categories .= " / " . $value -> categoryName;
	          }
	          $categories .= "<br/>";
	        }
	        array_push($elenco_video_wimtv, $content_item);
	        if (trim($content_item)!="") {
	          //controllo se il video esiste
	          $trovato = FALSE;
	          //controllo se il video eiste in DRUPAL ma non pi&#65533; in WIMTV
	          foreach ($array_videos_new_wp as $record) {
	            $content_itemAll = $record -> contentidentifier;
	            if ($content_itemAll == $content_item) {
	              $trovato = TRUE;
	            }
	          }
	          $pos_wimtv="";
	          $showtime_identifier ="";
	          if (isset($array_st[$content_item])) {
	            $pos_wimtv="showtime";
	            $showtime_identifier = $array_st[$content_item]["showtimeIdentifier"];
	          } 
	          else {
	            $pos_wimtv="";
	          }
	          
	          if (!$trovato) {
	            $wpdb->insert( $table_name, 
	            	array (
	            	'uid' => get_option("wp_userwimtv"),
	            	'contentidentifier' => $content_item,
	            	'mytimestamp' => time(),
	            	'position' => '0',
	            	'state' => $pos_wimtv,
	            	'viewVideoModule' => '3',
	            	'status' => $status,
	            	//'acquiredIdentifier' => $acquired_identifier,
	            	'urlThumbs' => mysql_real_escape_string($url_thumbs),
	            	'category' =>  $categories,
	            	'urlPlay' =>  mysql_real_escape_string($urlVideo),
	            	'title' =>  mysql_real_escape_string($title),
	            	'duration' => $duration,
	            	'showtimeidentifier' => $showtime_identifier
	            	)
	           	);
	            
	          } 
	          else {
	          	$query = "UPDATE " . $table_name . 
	            " SET state = '" . $pos_wimtv . "'," . 
	            " status = '" . $status . "'," . 
	            " title = '" . mysql_real_escape_string($title) . "'," .             
	            " urlThumbs = '" . mysql_real_escape_string($url_thumbs) . "'," .
	            " urlPlay = '" . mysql_real_escape_string($urlVideo) . "'," .
	            " duration = '" . $duration . "'," .
	            " showtimeidentifier = '" . $showtime_identifier . "'," .
	            " category = '" . $categories . "'" .
	            " WHERE contentidentifier = '"  . $content_item . "' ";
	            $wpdb->query($query);
	          }
	      }
		}
	} else {
		
	_e("You aren't videos","wimtvpro");
		
	}

    //var_dump(array_diff($elenco_video_wp ,$elenco_video_wimtv ));
    $delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);
    foreach ($delete_into_wp as  $value) {
      $wpdb->query( 
		  "DELETE FROM " . $table_name . " WHERE contentidentifier ='"  . $value . "'"
      );
    }
	
    if ((isset($_GET['sync']))) {
      echo wimtvpro_getVideos($_GET['showtime'], TRUE);
    }
    
    //UPDATE PAGE MY STREAMING
	update_page_wimvod();
  }
  else {
    _e("You aren't videos","wimtvpro");
  }
}

if (!isset($upload))
  die();