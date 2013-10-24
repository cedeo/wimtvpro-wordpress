<?php
/**
  * @file
  * SQL operation.
  *
  */
  $function = "";
  $id="";
  $acid="";
  $ordina = "";
  $credential = variable_get("userWimtv") . ":" . variable_get("passWimtv");

  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];
  if (isset($_GET['id']))
    $id = $_GET['id'];
  if (isset($_GET['acquiredId']))
    $acid = $_GET['acquiredId'];
  if (isset($_GET['showtimeId']))
    $stid = $_GET['showtimeId'];
  if (isset($_GET['ordina']))
    $ordina = $_GET['ordina'];

  if (isset($_GET['namePlayList']))
    $name= $_GET["namePlayList"];
  
  if (isset($_GET['idPlayList']))
    $idPlayList = $_GET["idPlayList"];


  switch ($function) {
    case "putST":
      //Insert Video into mystreaming and into wim.tv streaming
      $license_type = "";
      if ($_GET['licenseType']!="")
        $license_type = "licenseType=" . $_GET['licenseType'];
      $payment_mode= "";
      if ($_GET['paymentMode']!="")
        $payment_mode = "&paymentMode=" . $_GET['paymentMode'];
      $cc_type= "";
      if ($_GET['ccType']!="")
        $cc_type= "&ccType=" . $_GET['ccType'];
      $price_per_view  = "";
      if ($_GET['pricePerView']!="")
        $price_per_view = "&pricePerView=" . $_GET['pricePerView'];
      $price_per_view_currency = "";
      if ($_GET['pricePerViewCurrency']!="")
        $price_per_view_currency = "&pricePerViewCurrency=" . $_GET['pricePerViewCurrency'];
      $post_field = $license_type . $payment_mode . $cc_type . $price_per_view . $price_per_view_currency;
      $credential = variable_get("userWimtv") . ":" . variable_get("passWimtv");
        //Call API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
        //curl -u {username}:{password} -d "license_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      $url_post_public_wimtv = variable_get("basePathWimtv") . str_replace(variable_get('replaceContentWimtv'), $id, variable_get("urlPostPublicWimtv"));
        //This API allows posting an ACQUIRED video on the Web showtime for public streaming.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url_post_public_wimtv);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_POST, TRUE);
	  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-US,en;q=0.5'));
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
      $response = curl_exec($ch);
      echo $response;
	 
      $state = "showtime";
      $array_response = json_decode($response);
      $sql = "UPDATE {wimtvpro_videos} SET state='" . $state . "' ,showtimeIdentifier='" . $array_response -> showtimeIdentifier . "' WHERE contentidentifier='" . $id . "'";
      $query = db_query($sql);
      curl_close($ch);
      die();
    break;
    case "putAcqST":
      //Insert Video into mystreaming and into wim.tv streaming, similar at putST but this is for acquired video (not Owner)
      $license_type = "";
      if ($_GET['license_type']!="")
        $license_type = "license_type=" . $_GET['licenseType'];
      $payment_mode = "";
      if ($_GET['paymentMode']!="")
        $payment_mode = "&paymentMode=" . $_GET['paymentMode'];
      $cc_type = "";
      if ($_GET['ccType']!="")
        $cc_type= "&ccType=" . $_GET['ccType'];
      $price_per_view  = "";
      if ($_GET['pricePerView']!="")
        $price_per_view  = "&pricePerView=" . $_GET['pricePerView'];
      $price_per_view_currency = "";
      if ($_GET['pricePerViewCurrency']!="")
        $price_per_view_currency = "&pricePerViewCurrency=" . $_GET['pricePerViewCurrency'];

      $post_field = $license_type . $payment_mode . $cc_type . $price_per_view . $price_per_view_currency;
      $credential = variable_get("userWimtv") . ":" . variable_get("passWimtv") ;
      $state="showtime";
      $sql = "UPDATE {wimtvpro_videos} SET state='" . $state . "' WHERE contentidentifier='" . $id . "'";
      $query = db_query($sql);
      //Call API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      //curl -u {username}:{password} -d "license_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      $url_post_public_wimtv = str_replace(variable_get('replaceacquiredIdentifier'), $acid, variable_get("urlPostPublicAcquiWimtv"));
      $url_post_public_wimtv = str_replace(variable_get('replaceContentWimtv'), $id, $ur_post_public_wimtv);
      $url_post_public_wimtv = variable_get("basePathWimtv") . $url_post_public_wimtv;

      //This API allows posting an ACQUIRED video on the my streaming for public streaming.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $ur_post_public_wimtv);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
      die();
    break;
    case "removeST";
      //Remove into Mystreaming
      $state="";
      $num_updated = db_update('{wimtvpro_videos}')
      ->fields(array(
      'position ' => '0',
      'state' => '',
      'showtimeIdentifier' => '',
      )) ->condition("contentidentifier", $id)
      ->execute();
      //Call API
      //https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
      //curl -u {username}:{password} -X DELETE https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
      $url_remove_public_wimtv = str_replace(variable_get('replaceshowtimeIdentifier'), $stid, variable_get("urlSTWimtv"));
      $url_remove_public_wimtv = str_replace(variable_get('replaceContentWimtv'), $id, $url_remove_public_wimtv);
      $url_remove_public_wimtv = variable_get("basePathWimtv") . $url_remove_public_wimtv;
      //This API allows posting an ACQUIRED video on the Web my streaming for public streaming.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url_remove_public_wimtv);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
      die();
    break;
    case "StateViewThumbs":
      //Change the state of view thumbs (into block or page)
      $state = $_GET['state'];
      $num_updated = db_update('{wimtvpro_videos}')
      ->fields(array(
      'viewVideoModule' => $state,
      )) ->condition("contentidentifier", $id)
      ->execute();
      echo $state;
      die();
    break;
    case "ReSortable":
      //Change position of videos
      $list_video = explode(",", $ordina);
      foreach ($list_video as $position => $item) {
        $position = $position + 1;
        $num_updated = db_update('{wimtvpro_videos}')
        ->fields(array(
        'position ' => $position,
        )) -> condition("contentidentifier", $item)
        ->execute();
      }
      die();
    break;
    case "urlCreate":
      //Call API for create a event live url
      $url_createurl = variable_get("basePathWimtv") . "liveStream/uri?name=" . urlencode($_GET['titleLive']);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_createurl);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
      die();
    break;
      case "passCreate":
      //Call API for create a password for create a event live
      $url_passcreate = variable_get("basePathWimtv") . "users/" . variable_get("userWimtv") . "/updateLivePwd";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_passcreate);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "liveStreamPwd=" . $_GET['newPass']);
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
      die();
    break;

    case "RemoveVideo":
      //connect at API for upload video to wimtv
      $ch = curl_init();
      $url_delete = variable_get("basePathWimtv") . 'videos';
      $url_delete .= "/" . $id;
      curl_setopt($ch, CURLOPT_URL, $url_delete);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      curl_close($ch);
      $arrayjsonst = json_decode($response);
      if ($arrayjsonst->result=="SUCCESS")
        $query = db_delete('{wimtvpro_videos}')
        ->condition("contentidentifier", $id)
        ->execute();
      echo $response;
      die();
    break;

	case  "ModifyTitleVideo":
	 
	  $num_updated = db_update('{wimtvpro_videos}')
        ->fields(array(
        'title' => $_GET["titleVideo"],
        )) -> condition("vid", $id)
        ->execute();

	  die();
	 break;
    
	
	
	case "createPlaylist":
	
	
		$sql = "INSERT INTO {wimtvpro_playlist} (uid,listVideo,name,id) VALUES ('" . variable_get("userWimtv") . "' ,'','" . $name . "','" . time() . "')";
        $query = db_query($sql);
		

		die();
		
    break;
    
    case "modTitlePlaylist":

	  $num_updated = db_update('{wimtvpro_playlist}')
        ->fields(array(
        'name ' => $name,
        )) -> condition("id", $idPlayList)
        ->execute();

      die();
    
    break;

	case "removePlaylist":

      //remove File
		$query = db_delete('{wimtvpro_playlist}')
        ->condition("id", $idPlayList)
        ->execute();
		echo $query;
      die();
    
    break;

	case "downloadVideo":
		ini_set('max_execution_time', 300);
		$directory = "public://";
		$credential = variable_get("userWimtv") . ":" . variable_get("passWimtv");
		$result = db_query("SELECT * FROM {wimtvpro_videos} WHERE contentidentifier = '" . $id . "'");
		$arrayStatusVideo = $result->fetchAll();
				
		$filename = "";
		$ext = "";
		/*if (count($arrayStatusVideo[0])>0){
			$filestatus = explode ("|",$arrayStatusVideo[0]->status);
			if ($filestatus && ($filestatus[1]!="")){
				$infoFile = explode (".",$filestatus[1]);
				$numeroCount = count($infoFile);
				$ext = $infoFile[$numeroCount-1];
				$filename = $infoFile[0];
				for ($i=1;$i<$numeroCount-1;$i++){
					$filename .= "." . $infoFile[$i];
				}
			}
		}*/
		
		$url_download = variable_get("basePathWimtv") . "videos/" . $id . "/download";
		if ($filename!=""){
			$url_download .= "?filename=" . $filename . "&ext=" . $ext;
		}
		
		try {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,  $url_download);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 0);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			$file = curl_exec($ch);
			$file_array = explode("\n\r", $file, 2);
			$header_array = explode("\n", $file_array[0]);
			foreach($header_array as $header_value) {
			  $header_pieces = explode(':', $header_value);
			  if(count($header_pieces) == 2) {
					$headers[$header_pieces[0]] = trim($header_pieces[1]);
			  }
			}
			curl_close($ch);
			
			$explodeContent = explode("filename=",$headers['Content-Disposition']);
			$filename = $explodeContent[1];
			if (is_file(drupal_realpath($directory . "/" . $filename)))
				unlink (drupal_realpath($directory . "/" . $filename));
			//header('Content-type: ' . $headers['Content-Type']);
			$checkHeader = explode(";",$headers['Content-Disposition']);
			$checkextension = explode(".",$checkHeader[1]);
			$numeroCount = count($checkHeader[1]); // se ci fosse un file che ha più di un punto
			$ext = $checkextension[$numeroCount-1];

			
			if ((!isset($ext))  || ($ext==""))
					$headers['Content-Disposition'] .= "mp4";
			//header('Content-Disposition: ' . $headers['Content-Disposition']);
			
			$fh = fopen(drupal_realpath($directory . "/" . $filename), 'w+');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,  $url_download);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_FILE, $fh);
			curl_exec($ch);
			
			$error    = curl_errno($ch);
            $length   = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
            $mime     = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
			if ($error) {
				watchdog('Download Video Wimtv', 'Download failed:' . $error);
            }
			watchdog('Download Video Wimtv', 'Lenght video:' . $length );
			watchdog('Download Video Wimtv', 'Mime video:' . $mime );
			curl_close($ch);
			fclose($fh);
			
			readfile(drupal_realpath($directory . "/" . $filename));
			
		} catch (Exception $e) {
		  header('Content-type: text/plain');
		  header('Content-Disposition: attachment; filename="error.txt"');
		  echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		die();
        
    break;

  }