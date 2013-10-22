<?php
  global $user,$wpdb;
  include("../../../wp-load.php");
  include_once("api/api.php");

  header('Content-type: application/json');

  $url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
  $credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
  $table_name = $wpdb->prefix . 'wimtvpro_video';

  $uploadMaxFile = return_bytes(ini_get('upload_max_filesize'));
  $postmaxsize = return_bytes(ini_get('post_max_size'));
  $uploadMaxFile_mb =  number_format($uploadMaxFile / 1048576, 2) . 'MB';
  $postmaxsize_mb = number_format($postmaxsize / 1048576, 2) . 'MB';

  initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));

  $function = "";
  $id="";
  $acid="";
  $ordina = "";

  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];
  else if(isset($_POST['namefunction']))
    $function= $_POST["namefunction"];
  if (isset($_GET['id']))
    $id = $_GET['id'];
  if (isset($_GET['acquiredId']))
    $acid = $_GET['acquiredId'];
  if (isset($_GET['showtimeId']))
    $stid = $_GET['showtimeId'];
  if (isset($_GET['ordina']))
    $ordina = $_GET['ordina'];

  if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
      echo '<div class="error"><p><strong>';
      echo str_replace("%d",$postmaxsize_mb,__("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.","wimtvpro"));
      echo '</strong></p></div>';
  }

  //trigger_error($function, E_USER_NOTICE);
  switch ($function) {
    case "putST":
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
		
        //API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
        //curl -u {username}:{password} -d "license_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      $url_post_public_wimtv = get_option("wp_basePathWimtv") . str_replace( get_option("wp_replaceContentWimtv"), $id,  get_option("wp_urlPostPublicWimtv"));
      
     //This API allows posting an ACQUIRED video on the Web showtime for public streaming.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url_post_public_wimtv);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
      
      //echo $url_post_public_wimtv;
      
      $response = curl_exec($ch);
      
    
     
      
      if ($response)
      $state = "showtime";
      $array_response = json_decode($response);
      if ($array_response->result=="SUCCESS"){
	      $sql = "UPDATE " . $table_name  . " SET state='" . $state . "' ,showtimeIdentifier='" . $array_response -> showtimeIdentifier . "' WHERE contentidentifier='" . $id . "'";
	      $wpdb->query($sql);
	  }
	  
	 
      curl_close($ch);
      
      echo $response;
      
      //UPDATE PAGE MY STREAMING
	  update_page_mystreaming();
      
      
      die();
    break;
    case "putAcqST":
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
      $state="showtime";
      $sql = "UPDATE " . $table_name  . " SET state='" . $state . "' WHERE contentidentifier='" . $id . "'";
      $wpdb->query($sql);
      //Richiamo API  http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      //curl -u {username}:{password} -d "license_type=TEMPLATE_LICENSE&paymentMode=PAYPERVIEW&pricePerView=50.00&pricePerViewCurrency=EUR" http://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime
      $url_post_public_wimtv = str_replace(get_option('wp_replaceacquiredIdentifier'), $acid, get_option('wp_urlPostPublicAcquiWimtv')); 
      $url_post_public_wimtv = str_replace(get_option('wp_replaceContentWimtv'), $id, $ur_post_public_wimtv);
      $url_post_public_wimtv = get_option('wp_basePathWimtv') . $url_post_public_wimtv;

      //This API allows posting an ACQUIRED video on the my streaming for public streaming.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $ur_post_public_wimtv);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_field);
      $response = curl_exec($ch);
      echo $response;
      
      //UPDATE PAGE MY STREAMING
	  update_page_mystreaming();
      
      curl_close($ch);
      die();
    break;
    case "removeST";
      $state="";
      $sql = "UPDATE " . $table_name  . " SET position='0',state='',showtimeIdentifier='' WHERE contentidentifier='" . $id . "'";
	  $wpdb->query($sql);
      //Richiamo API 
      //https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
      //curl -u {username}:{password} -X DELETE https://www.wim.tv/wimtv-webapp/rest/videos/{contentIdentifier}/showtime/{showtimeIdentifier}
      $url_remove_public_wimtv = str_replace(get_option('wp_replaceshowtimeIdentifier'), $stid, get_option('wp_urlSTWimtv'));
      $url_remove_public_wimtv = str_replace(get_option('wp_replaceContentWimtv'), $id, $url_remove_public_wimtv);
      $url_remove_public_wimtv = get_option('wp_basePathWimtv') . $url_remove_public_wimtv;
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
      //UPDATE PAGE MY STREAMING
	  update_page_mystreaming();

      
      curl_close($ch);
      die();
    break;
    case "StateViewThumbs":
      $state = $_GET['state'];
      $sql = "UPDATE " . $table_name  . " SET viewVideoModule='" . $state . "' WHERE contentidentifier='" . $id . "'";
	  $wpdb->query($sql);

	  //UPDATE PAGE MY STREAMING
	  update_page_mystreaming();

      echo $state;
      die();
    break;
    case "ReSortable":
      $list_video = explode(",", $ordina);
      foreach ($list_video as $position => $item) {
        $position = $position + 1;
        $sql = "UPDATE " . $table_name  . " SET position ='" . $position . "' WHERE contentidentifier='" . $item . "'";
	    $wpdb->query($sql);
      }
      
      //UPDATE PAGE MY STREAMING
	  update_page_mystreaming();

      
      die();
    break;
    case "urlCreate":
      /*$url_createurl = get_option('wp_basePathWimtv') . "liveStream/uri?name=" . urlencode($_GET['titleLive']);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_createurl);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));*/
	  
      $response = apiCreateUrl(urlencode($_GET['titleLive']));  //curl_exec($ch);
      echo $response;
      //curl_close($ch);
    break;
    case "passCreate":
      $url_passcreate = get_option('wp_basePathWimtv') . "users/" . get_option("wp_userwimtv") . "/updateLivePwd";
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_passcreate);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
       curl_setopt($ch, CURLOPT_USERPWD, $credential);
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

      curl_setopt($ch, CURLOPT_POSTFIELDS,"liveStreamPwd=" . $_GET['newPass']);      
      $response = curl_exec($ch);
      echo $response;

      curl_close($ch);
	  die();
    break;
    case "getIFrameVideo":
    /*
      if (get_option('wp_nameSkin')!="") {
        $uploads_info = wp_upload_dir();
        $directory =  $uploads_info["baseurl"] .  "/skinWim";

        $skin = "&skin=" . $directory  . "/" . get_option('wp_nameSkin') . ".zip";      
      }
      else
        $skin = "";
      
      $url = get_option("wp_basePathWimtv") . get_option("wp_urlVideosWimtv") . "/" . $id . '/embeddedPlayers';
      $url .= "?get=1&width=" . $_GET['WFrame'] . "&height=" . $_GET['HFrame'] . $skin;
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      $response = curl_exec($ch);
      */
      $shortcode = "[streamingWimtv id='" . $id . "' width='" . $_GET['WFrame'] . "' height='" .  $_GET['HFrame'] . "' ]";
      echo $shortcode; 
      
      //echo $response;
       
      
    break;
    
    
    case "RemoveVideo":
		//connect at API for upload video to wimtv
		
		$ch = curl_init();
		$url_delete = get_option("wp_basePathWimtv") . 'videos';
		$url_delete .= "/" . $id;
		
		
		curl_setopt($ch, CURLOPT_URL, $url_delete);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $credential);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$response = curl_exec($ch);
		curl_close($ch);
		$arrayjsonst = json_decode($response);
		if ($arrayjsonst->result=="SUCCESS")
			$wpdb->query( 
		  		"DELETE FROM " . $table_name . " WHERE contentidentifier ='"  . $id . "'"
      		);

		echo $response;
		
		//UPDATE PAGE MY STREAMING
	    update_page_mystreaming();

    break;
    
    case "getUsers":
      $sqlVideos = $wpdb->get_results("SELECT viewVideoModule FROM " . $table_name  . " WHERE contentidentifier = '" .  $id . "'");
      //$sqlVideos = mysql_query("SELECT viewVideoModule FROM " . $table_name  . " WHERE contentidentifier = '" .  $id . "'");
      $stateView = explode ("|",$sqlVideos[0]->viewVideoModule);
      $arrayUsers = explode (",",$stateView[1]);
    
      $q_users = mysql_query("SELECT ID,user_login FROM " . $wpdb->prefix . "users");
      while($username = mysql_fetch_array($q_users)){
      	$valueOption = "U-" .  $username['ID'];
        echo "<option value='" .  $valueOption . "'";
        foreach ($arrayUsers as $typeUser){    
        	if ($valueOption == $typeUser) echo " selected='selected' ";
        }
        echo ">" . $username['user_login'] . "</option>";
      }
      die();
    break;
    
    case "getRoles":
      $sqlVideos = $wpdb->get_results("SELECT viewVideoModule FROM " . $table_name  . " WHERE contentidentifier = '" .  $id . "'");
      $stateView = explode ("|",$sqlVideos[0]->viewVideoModule);
      $arrayRoles = explode (",",$stateView[1]);
    
      global $wp_roles;
      $roles = $wp_roles->get_names();
      foreach($roles as $role=>$value) {
      	$valueOption = "R-" .  $role;
        echo "<option value='" . $valueOption . "'";
        foreach ($arrayRoles as $typeRole){    
        	if ($valueOption == $typeRole) echo " selected='selected' ";
        }
        echo ">" . $value . "</option>";
      }
      die();
    break;
	
	case "getAlls":
      $sqlVideos = $wpdb->get_results("SELECT viewVideoModule FROM " . $table_name  . " WHERE contentidentifier = '" .  $id . "'");
	  $stateView = explode ("|",$sqlVideos[0]->viewVideoModule);
      echo "<option value='All'";   
      if (($stateView[1] == "") || ($stateView[1] == "All")) echo " selected='selected' ";
      echo ">" . __('Everybody',"wimtvpro") . "</option>";

	  echo "<option value='No'";   
      if ($stateView[1] == "No") echo " selected='selected' ";
      echo ">" . __('Nobody (Administrators Only)',"wimtvpro") . "</option>";

	  
      die();
    break;
	

    case "downloadVideo":
      
		$url_download = get_option("wp_basePathWimtv") . "videos/" . $id . "/download";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,  $url_download);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERPWD, $credential);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	  
		$file = curl_exec($ch);
	
		$file_array = explode("\n\r", $file, 2);
		$header_array = explode("\n", $file_array[0]);
		foreach($header_array as $header_value) {
		  $header_pieces = explode(':', $header_value);
		  if(count($header_pieces) == 2) {
			$headers[$header_pieces[0]] = trim($header_pieces[1]);
		  }
		}
	
		header('Content-type: ' . $headers['Content-Type']);
		
		$checkHeader = explode(";",$headers['Content-Disposition']);
		//echo $checkHeader[1];
		$checkextension = explode(".",$checkHeader[1]);
		if ((!isset($checkextension[1]))  || ($checkextension[1]==""))
			header('Content-Disposition: ' . $headers['Content-Disposition'] . "mp4");
		else
			header('Content-Disposition: ' . $headers['Content-Disposition']);
		
		echo substr($file_array[1], 1);
	
		//echo "<iframe src=\"" . $url_download . "\" style=\"display:none;\" />"; 
		die();

    break;
    
	
	case "uploadFile":
		$sizefile = filesize($_FILES['videoFile']['tmp_name']);
		$urlfile = @$_FILES['videoFile']['tmp_name'];
		$uploads_info = wp_upload_dir();
		$directory = $uploads_info["basedir"] . "/videotmp";
		if (!is_dir($directory)) {
		  $directory_create = mkdir($uploads_info["basedir"] . "/videotmp");
		}
		$unique_temp_filename = $directory .  "/" . time() . '.' . preg_replace('/.*?\//', '',"tmp");
		$unique_temp_filename = str_replace("\\" , "/" , $unique_temp_filename);
		if (@move_uploaded_file( $urlfile , $unique_temp_filename)) {
			//echo "copiato";
		}else{
			//echo "non copiato";
		}
		$error = 0;
		$titlefile = $_POST['titlefile'];
		$descriptionfile = $_POST['descriptionfile'];
		$video_category = $_POST['videoCategory'];
	
		// Required
		if (strlen(trim($titlefile))==0) {  
		   echo '<div class="error"><p><strong>';
		   _e("You must write a title","wimtvpro");
		   echo '</strong></p></div>';
		   $error ++;
		}
	    
	     if ((strlen(trim($urlfile))>0) && ($error==0)) {
			global $user,$wpdb;  
	
			$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
			$table_name = $wpdb->prefix . 'wimtvpro_video';
	
			//UPLOAD VIDEO INTO WIMTV
			set_time_limit(0);
			//connect at API for upload video to wimtv
			$ch = curl_init();
			$url_upload = get_option("wp_basePathWimtv") . 'videos';
			//$url_upload = "http://192.168.31.200:8082/wimtv-webapp/rest/videos";
			//$credential = "albi:12345678";
			curl_setopt($ch, CURLOPT_URL, $url_upload);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data","Accept-Language: " . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);	            
			//add category/ies (if exist)
			$category_tmp = array();
			$subcategory_tmp = array();    
			$post= array("file" => "@" . $unique_temp_filename ,"title" => $titlefile,"description" => $descriptionfile);
			
			if (count($video_category)>0) {
			  $id=0;
			  foreach ($video_category as $cat) {
				$subcat = explode("|", $cat);
				if ($subcat[0]!=""){
					$post['category[' . $id . ']'] = $subcat[0];
					$post['subcategory[' . $id . ']'] = $subcat[1];
					$id++;
			  	}
			  }
			  
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
 
			$response = curl_exec($ch);
			curl_close($ch);
			$arrayjsonst = json_decode($response);
			
			if (isset($arrayjsonst->contentIdentifier)) {
				echo '<div class="updated"><p><strong>';
				_e("Upload successful","wimtvpro");
				$handle = opendir($directory);
				while (($file = readdir($handle)) !== false) {
					@unlink($directory . "/" . $file);
				}
				closedir($handle);
				echo  '</strong></p></div>';
				$wpdb->insert( $table_name, 
				array (
				  'uid' => get_option("wp_userwimtv"),
				  'contentidentifier' => $arrayjsonst->contentIdentifier,
				  'mytimestamp' => time(),
				  'position' => '0',
				  'state' => '',
				  'viewVideoModule' => '3',
				  'status' => 'OWNED|'  . $_FILES['videoFile']['name'],
				  'acquiredIdentifier' => '',
				  'urlThumbs' => $arrayjsonst->urlThumbs,
				  'urlPlay' => '',
				  'category' =>  '',
				  'title' => $titlefile ,
				  'duration' => '',
				  'showtimeidentifier' => ''
				 )
				);
	 		 }
	         else{
	             $error ++;
	             echo '<div class="error"><p><strong>';
	             _e("Upload error","wimtvpro");
	             echo  $response  . '</strong></p></div>';
			}
	    
		} else {
	           
			$error++;
			if ($_FILES['videoFile']['name']=="") {
			
				$error ++;
			   echo '<div class="error"><p><strong>';
			   _e("You must upload a file","wimtvpro");
			   echo '</strong></p></div>';
			} else {

				switch ($_FILES['videoFile']['error']){

					case "1":
						echo '<div class="error"><p><strong>';
						echo str_replace("%d",$uploadMaxFile_mb,__("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.","wimtvpro")) . " [upload_max_filesize] ";
						echo '</strong></p></div>';
					break;

					case "2":
						echo '<div class="error"><p><strong>';
						echo str_replace("%d",$postmaxsize_mb,__("The server where your Wordpress is installed does not support upload of files exceeding %d. If you want to upload videos larger than %d, please modify your server settings. WimTV supports up to 2GB file size per upload.","wimtvpro")) . " [MAX_FILE_SIZE] ";
						echo '</strong></p></div>';
					break;

				}

			}
 		 die();
	    }
	
		
	
	break;
	
    default:
      //echo "Non entro";
      die();
  }
    
?>
