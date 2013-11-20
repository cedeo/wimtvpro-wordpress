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
      $array_videos_new_wp = dbGetUserVideosId(get_option("wp_userwimtv"));
      foreach ($array_videos_new_wp as $record) {
        array_push($elenco_video_wp, $record -> contentidentifier);
      }
      /* Information detail videos into Showtime */
      $json_st = wimtvpro_detail_showtime(FALSE, 0);
      $arrayjson_st = json_decode($json_st);
      $values_st = $arrayjson_st->items;
      foreach ($values_st as $key => $value) {
        $array_st[$value -> {"contentId"}]["showtimeIdentifier"] = $value-> {"showtimeIdentifier"};
      }
      if ($array_all_videos) {
	      foreach ($array_all_videos as $video) {
	        $url_video = $video["actionUrl"];
	        $status = $video["status"];
	        $acquired_identifier = $video["relationId"];
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
                dbInsertVideo(get_option("wp_userwimtv"), $content_item, $pos_wimtv, $status,
                              $url_thumbs, $categories, $urlVideo, $title, $duration, $showtime_identifier);
	          } 
	          else {
                dbUpdateVideo($pos_wimtv, $status, $title, $url_thumbs, $urlVideo,
                              $duration, $showtime_identifier, $categories, $content_item);
	          }
	      }
		}
	} else {
	    _e("You aren't videos","wimtvpro");
	}

    //var_dump(array_diff($elenco_video_wp ,$elenco_video_wimtv ));
    $delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);
    foreach ($delete_into_wp as  $value) {
        dbDeleteVideo($value);
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