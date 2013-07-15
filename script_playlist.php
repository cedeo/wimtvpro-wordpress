<?php
  global $user,$wpdb;
  include("../../../wp-blog-header.php");
  $table_name = $wpdb->prefix . 'wimtvpro_playlist';


  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];

  if (isset($_GET['namePlayList']))
    $name= $_GET["namePlayList"];
  
  if (isset($_GET['idPlayList']))
    $idPlayList = $_GET["idPlayList"];
    
    if (isset($_GET['id']))
    $id = $_GET["id"];


  switch ($function) {
  
    case "AddVideoToPlaylist":
    	$listVideo = "";
    	$playlist = $wpdb->get_results("SELECT listVideo,name FROM {$table_name} WHERE id='" . $idPlayList . "'");
		foreach ($playlist as $record) {
			$listVideo = $record->listVideo;
			$name = $record->name;
		}
		
		//Check if this file exist

		if ( strpos($listVideo,trim($id))>-1) {
			echo "This video exist into " . $name . " playlist.";
	        die ();
		
		}else {
		
	    	// UPDATE into DB (campo listVideo)
	    	if ($listVideo=="")
	    		$listVideo = $id;
	    	else
	    		$listVideo = $listVideo . "," . $id;
	    	$sql = "UPDATE " . $table_name  . " SET listVideo='" . $listVideo . "' WHERE id='" . $idPlayList . "'";
	        $wpdb->query($sql);
	
	    	
	    	// MODIFY XML playlist_## 
	    	
			$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
	  		$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
		    $url_embedded =  get_option("wp_urlShowTimeWimtv") . "/" . get_option("wp_replaceshowtimeIdentifier") . "/details";
		    $replace_content = get_option("wp_replaceContent");
		    $url_detail = str_replace(get_option("wp_replaceshowtimeIdentifier"), $id , $url_embedded);
		    $url_detail = str_replace(get_option("wp_replaceUserWimtv"), get_option("wp_userWimtv"), $url_detail);
		    $url_detail = get_option("wp_basePathWimtv") . $url_detail;
			$st = curl_init();
		 	curl_setopt($st, CURLOPT_URL, $url_detail);
		    curl_setopt($st, CURLOPT_VERBOSE, 0);
		    curl_setopt($st, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($st, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		    curl_setopt($st, CURLOPT_SSL_VERIFYPEER, FALSE);
		    $Jsonarray = curl_exec($st);
		   
		    $array_detail = json_decode($Jsonarray);
		    curl_close($st);
			
			$uploads_info = wp_upload_dir();
	        $directory = $uploads_info["basedir"] .  "/skinWim";
			$nameFile = "/playlist_" .  $idPlayList . ".xml";
			$doc = simplexml_load_file($directory . $nameFile);
			$sxe = new SimpleXMLElement($doc->asXML());
			
			$channel = $sxe->channel;
			foreach ($channel->item as $items) {
			   $position = $items->count();
			}
			$urlVideo= $array_detail->streamingUrl->streamer . "$$" . $array_detail->streamingUrl->file . "$$" . $array_detail->streamingUrl->auth_token;
			
			$item = $channel->addChild('item');
			$item->addChild('title',$array_detail->title);	
			$item->addChild('image', $array_detail->thumbnailUrl);
			$item->addChild('file',$urlVideo);	
			$item->addChild('description',$array_detail->description);	
			$item->addChild('position',$position+1);	
			
			$fh = fopen($directory . $nameFile, "w");
		    if($fh==false) {
	          echo "unable to create file";
	          die();
	        }
	        fputs ($fh,$sxe->asXML());
	        fclose ($fh);
	        echo "";
	        die ();
		}
		
    break;
  
    case "createPlaylist":
      $uploads_info = wp_upload_dir();
      $directory = $uploads_info["basedir"] .  "/skinWim";
      $wpdb->insert( $table_name, 
	  array (
	  				'uid' => get_option("wp_userwimtv"),
	            	'listVideo' => '',
	            	'name' =>  $name,
	            	)
	           	);
	           	
	  $fh = fopen($directory  . "/playlist_" .  $wpdb->insert_id . ".xml", "w");
	  if($fh==false)
        die("unable to create file");
      fputs ($fh, '<rss><channel><title>' . $name . '</title></channel></rss>');
      fclose ($fh);
	  echo $wpdb->insert_id;
      die();
    
    break;
    
    case "modTitlePlaylist":

	  $sql = "UPDATE " . $table_name  . " SET name='" . $name . "' WHERE id='" . $idPlayList . "'";
      $wpdb->query($sql);
      
      
      $uploads_info = wp_upload_dir();
        $directory = $uploads_info["basedir"] .  "/skinWim";
		$nameFile = "/playlist_" .  $idPlayList . ".xml";
		$doc = simplexml_load_file($directory . $nameFile);
		$sxe = new SimpleXMLElement($doc->asXML());
		$sxe->channel->title = $name; 
		
		$directory = $uploads_info["basedir"] .  "/skinWim";
		$nameFile = "/playlist_" .  $idPlayList . ".xml";

		
		$fh = fopen($directory . $nameFile, "w");
	    if($fh==false) {
          echo "unable to create file";
          die();
        }
        fputs ($fh,$sxe->asXML());
        fclose ($fh);

      
	  echo "OK";
      die();
    
    break;

	case "removePlaylist":
	  
	  $uploads_info = wp_upload_dir();
      $directory = $uploads_info["basedir"] .  "/skinWim";

	  $sql = "DELETE FROM " . $table_name  . " WHERE id='" . $idPlayList . "'";
      $wpdb->query($sql);
      //remove File
      unlink ($directory . "/playlist_" . $idPlayList . ".xml");
	  echo "OK";
      die();
    
    break;

    
    default:
      echo "Non entro";
      die();
  }
    
?>