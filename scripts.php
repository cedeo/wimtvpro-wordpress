<?php
  global $user,$wpdb;
  include("../../../wp-blog-header.php");
  $url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
  $credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
  $table_name = $wpdb->prefix . 'wimtvpro_video';


  $function = "";
  $id="";
  $acid="";
  $ordina = "";

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
      curl_setopt($ch, CURLOPT_POST, TRUE);
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
      $url_createurl = get_option('wp_basePathWimtv') . "liveStream/uri?name=" . urlencode($_GET['titleLive']);
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL,  $url_createurl);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($ch, CURLOPT_USERPWD, $credential);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));

	  
      $response = curl_exec($ch);
      echo $response;
      curl_close($ch);
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

    
    
    default:
      echo "Non entro";
      die();
  }
    
?>