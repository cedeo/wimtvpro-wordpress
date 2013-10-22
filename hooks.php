<?php

include_once("api/api.php");

header('Content-type: application/json');

initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));

define('BASE_URL', get_bloginfo('url'));

function wimtvpro_submenu($view_page){
	$submenu = "<ul class='subsubsub'>";
	$submenu .= "<li><a href='admin.php?page=WimTvPro' class='config'>" . __("Configuration","wimtvpro") . "</a> |";
	if ($view_page) $submenu .= "<li><a href='admin.php?page=WimTvPro&pack=1' class='packet'>" . __("Pricing","wimtvpro") . "</a> |";
	if ($view_page) $submenu .= "<li><a href='admin.php?page=WimTvPro&update=1' class='payment'>" . __("Monetisation","wimtvpro") . "</a> |";
	if ($view_page) $submenu .= "<li><a href='admin.php?page=WimTvPro&update=2' class='live'>"  . __('Live',"wimtvpro") . "</a> |";
	if ($view_page) $submenu .= "<li><a href='admin.php?page=WimTvPro&update=3' class='user'>" . __("Personal Info","wimtvpro") . "</a> |";
	if ($view_page) $submenu .= "<li><a href='admin.php?page=WimTvPro&update=4' class='other'>" . __("Features","wimtvpro") . "</a> ";
	$submenu .= "</ul>";
	return $submenu;
}


function wimtvpro_configure(){
  $uploads_info = wp_upload_dir();
  echo "<div class='clear'></div>";
  if (!isset($_GET["pack"]))	{	
    if (!isset($_GET["update"])){
	    $directory = $uploads_info["basedir"] .  "/skinWim";
	    $styleReg = "display:none";
	        
	    if(isset($_POST['wimtvpro_update']) && $_POST['wimtvpro_update'] == 'Y') {
	        //Form data sent 
	
	        $error = 0;
	        //Upload Skin
	        $file = $_FILES['files']['name']["uploadSkin"];
	        $tmpfile =  $_FILES['files']['tmp_name']["uploadSkin"];
	        $arrayFile = explode(".", $file);
	        if (!empty($file)) {            
				if ($arrayFile[1] != "zip") {
				  echo '<div class="error"><p><strong>';
	              _e("This file isn't format correct for jwplayer's skin");
	              echo '</strong></p></div>';
	              $error ++;
				} else {
				  if (filesize($tmpfile) > 10485760) {
				    echo '<div class="error"><p><strong>';
	                _e("Uploaded file is ","wimtvpro") ;
					echo " " . round(filesize($tmpfile) / 1048576, 2);
					_e("Kb. It must be less than","wimtvpro");
					echo   " 10Mb.";
	                echo '</strong></p></div>';
	                $error ++;
				  } else {
				  	if ( false === @move_uploaded_file( $tmpfile, $uploads_info["basedir"] .  "/skinWim" . "/" . $file) ) {
				  	  echo '<div class="error"><p><strong>';
	                  _e("Internal error.");
	                  echo $uploads_info["basedir"] .  "/skinWim/" . $file;
	                  echo '</strong></p></div>';
	                  $error ++;
		            }
		            
					//$return = wimtvpro_unzip($directory . "/" . get_option('wp_nameSkin') . ".zip", $directory);
					require_once(ABSPATH .'/wp-admin/includes/file.php'); //the cheat

					WP_Filesystem();
					$return = unzip_file($directory . "/" . get_option('wp_nameSkin') . ".zip", $directory);
					
					if ($return) {
						update_option('wp_nameSkin', $arrayFile[0]);
						
					} else{
						$error++;
						echo '<div class="error"><p><strong>';
						  _e("Internal error.");
						  var_dump( $return);
						  echo '</strong></p></div>';
					}
					
					
					
				  }
				}
	        } else {
	          update_option('wp_nameSkin', $_POST['nameSkin']);
	        }
	        
	        // Required
	        if (strlen(trim($_POST['userWimtv']))==0) {        
	        	echo '<div class="error"><p><strong>';
	            _e("The username is required","wimtvpro");
	            echo '</strong></p></div>';
	            $error ++;
	        }
	        // Required
	        if (strlen(trim($_POST['passWimtv']))==0) {
	        	echo '<div class="error"><p><strong>';
	            _e("The password is required","wimtvpro");
	            echo '</strong></p></div>';
	            $error ++;     
	        }
	
	        
	        if ($error==0) {
	          
	          if ($_POST['sandbox']=="No") {
	          	update_option( 'wp_basePathWimtv','https://www.wim.tv/wimtv-webapp/rest/');
	          } else {
	          	update_option( 'wp_basePathWimtv','http://peer.wim.tv/wimtv-webapp/rest/');
	          }
	          
	          if (($_POST['sandbox']!=get_option('wp_sandbox')) && (($_POST['userWimtv']=="username") && ($_POST['passWimtv']=="password"))){
	            update_option('wp_registration', 'FALSE'); 
	            update_option('wp_userwimtv', 'username');
	            update_option('wp_passwimtv', 'password');
	
	          } else {
	              
				  	//Call API controll user
				  	$urlUpdate = get_option("wp_basePathWimtv") . "profile";
				 	 $credential = $_POST['userWimtv'] . ":" . $_POST['passWimtv'];
				  
				  	$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $urlUpdate);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_USERPWD, $credential);
	
					$response = curl_exec($ch);
					
					$arrayjsonst = json_decode($response);
			
					curl_close($ch);
				  
				    if (count($arrayjsonst)> 0){
						  
				
						
		           	 	update_option('wp_userwimtv', $_POST['userWimtv']);
		            	update_option('wp_passwimtv', $_POST['passWimtv']);
						echo '<div class="updated"><p><strong>';
	          			_e('Update successful' ,"wimtvpro");
	          			echo '</strong></p></div>'; 
						
					} else {
						update_option('wp_userwimtv', "username");
		            	update_option('wp_passwimtv', "password");
						echo '<div class="error"><p><strong>';
	          _e('Can not establish a connection with Wim.tv. Username and/or Password are not correct.' ,"wimtvpro");
	          echo '</strong></p></div>'; 
					}
		      }    
		      
	          update_option('wp_heightPreview', $_POST['heightPreview']);
	          update_option('wp_widthPreview', $_POST['widthPreview']);
	
	          
	          update_option('wp_sandbox', $_POST['sandbox']);
	          update_option( 'wp_urlVideosWimtv','videos');
	          update_option( 'wp_urlVideosDetailWimtv','videos?details=true&incomplete=true');
	          update_option( 'wp_urlThumbsWimtv','videos/{contentIdentifier}/thumbnail');
	          update_option( 'wp_urlEmbeddedPlayerWimtv','videos/{contentIdentifier}/embeddedPlayers?get=1');
	          update_option( 'wp_urlPostPublicWimtv','videos/{contentIdentifier}/showtime');
	          update_option( 'wp_urlPostPublicAcquiWimtv','videos/{contentIdentifier}/acquired/{acquiredIdentifier}/showtime');
	          update_option( 'wp_urlSTWimtv','videos/{contentIdentifier}/showtime/{showtimeIdentifier}');
	          update_option( 'wp_urlShowTimeWimtv','users/{username}/showtime');
	          update_option( 'wp_urlShowTimeDetailWimtv','users/{username}/showtime?details=true');
	          update_option( 'wp_urlUserProfileWimtv','users/{username}/profile'); 
	          update_option( 'wp_replaceContentWimtv','{contentIdentifier}'); 
	          update_option( 'wp_replaceUserWimtv','{username}'); 
	          update_option( 'wp_replaceacquiredIdentifier','{acquiredIdentifier}');
	          update_option( 'wp_replaceshowtimeIdentifier','{showtimeIdentifier}'); 
	          update_option( 'wp_publicPage', $_POST['publicPage']);
	         
			  update_page_mystreaming();
			  
			  
			  
			  
	
	   
	        }
	  	}
	   
	   // If directory skinWim don't exist, create the directory (if change Public file system path into admin/config/media/file-system after installation of this module or is the first time)

       $elencoSkin = [];

       if (!is_dir($directory)) {
	      $directory_create = mkdir($uploads_info["basedir"] . "/skinWim");
	   }
	   
	   if (is_dir($directory)) {
	     if ($directory_handle = opendir($directory)) {
	     //Read directory for skin JWPLAYER
	   	 $elencoSkin[""] = "-- Base Skin --";
	     while (($file = readdir($directory_handle)) !== FALSE) {
	       if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
	         $explodeFile = explode("." , $file);
	         if ($explodeFile[1]=="zip")
	           $elencoSkin[$explodeFile[0]] = $explodeFile[0];
	         }
	       }
	       closedir($directory_handle);
	     }
	   }
	   //Create option select form Skin
	   $createSelect = "";
	   foreach ($elencoSkin as $key => $value){
	     $createSelect .= "<option value='" . $key . "'";
	     if ($value==get_option("wp_nameSkin"))  $createSelect .= " selected='selected' ";
	     $createSelect .= ">" . $value . "</option>";
	   }
		
		
	$uploads_info = wp_upload_dir();	
	?>
	  <div class="wrap">
	         <h2><?php _e("Configuration","wimtvpro");?></h2>
			
<?php
	$view_page = wimtvpro_alert_reg();
	$submenu = wimtvpro_submenu($view_page);

?>
				
				<?php echo str_replace("config","current",$submenu) ; ?>
				
	            <div>
					<div class="empty"></div>
                    <h4><?php _e("Connect to your account on WimTV","wimtvpro");?></h4>
                    						
					<form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
					
						<table class="form-table">
					
			              	<tr>
			              		<th><label for="edit-userwimtv"><?php _e("Username","wimtvpro"); ?><span class="form-required" title="">*</span></label></th>
								<td><input type="text" id="edit-userwimtv" name="userWimtv" value="<?php echo get_option("wp_userwimtv");?>" size="100" maxlength="200"/></td>
							</tr>
							
							<tr>
								<th><label for="edit-passwimtv">Password<span class="form-required" title="">*</span></label></th>
								<td><input value="<?php echo get_option("wp_passwimtv");?>" type="password" id="edit-passwimtv" name="passWimtv" size="100" maxlength="200" class="form-text required" /></td>
							</tr>
						</table>
						
						<h4><?php _e("Upload and/or choose the skin for your player","wimtvpro");?> 
                        . <?php _e("Download it from","wimtvpro")?> <a target='new' href='http://www.longtailvideo.com/addons/skins'>Jwplayer skin</a></h4>
	
						<table class="form-table">	
							<tr>
								<th><label for="edit-nameskin"><?php _e("Skin Name","wimtvpro");?></label></th>
								<td><select id="edit-nameskin" name="nameSkin" class="form-select"><?php echo $createSelect; ?></select></td>
							</tr>
							<tr>
								<th><label for="edit-uploadskin"><?php _e("upload a new skin for your player","wimtvpro");?></label></th>
								<td><input type="file" id="edit-uploadskin" name="files[uploadSkin]" size="100" class="form-file" />
									<div class="description"><?php echo __("Only .zip files are supported Save to a public URL","wimtvpro") .  " wp-content/uploads/skinWim <br/>" . 
									__("To use the skin of your choice, copy the","wimtvpro") .  " <a href='http://plugins.longtailvideo.com/crossdomain.xml' target='_new'>crossdomain.xml</a> " . __("file to the root directory (e.g. http://www.mysite.com). You can do it all via FTP  (e.g. FileZilla, Classic FTP, etc). Open your FTP client and identify the root directory of your site. This is the folder titled or beginning with www - and this is where you need to move the crossdomain.xml file","wimtvpro") . ".<br/><a href='http://www.adobe.com/devnet/adobe-media-server/articles/cross-domain-xml-for-streaming.html'>" . __("More mation","wimtvpro") . "</a>"; ?>
									</div>
								</td>
							</tr>
						</table>
		
						<h4><?php _e("Size of the player for your videos","wimtvpro" ); ?></h4>
		
						<table class="form-table">	
							<tr>
								<th><label for="edit-heightpreview"><?php _e("Height");?> (default: 280px)</label></th>
								<td><input type="text" id="edit-heightpreview" name="heightPreview" value="<?php echo get_option("wp_heightPreview");?>" size="100" maxlength="200" class="form-text" /></td>
							</tr>
							<tr>
								<th><label for="edit-widthpreview"><?php _e("Width");?> (default: 500px) </label></th>
								<td><input type="text" id="edit-widthpreview" name="widthPreview" value="<?php echo get_option("wp_widthPreview");?>" size="100" maxlength="200" class="form-text" /></td>
							</tr>
							
						</table>
						
						<h4><?php _e("Other information" ,"wimtvpro"); ?></h4>
						
						
						
						<input type="hidden" value="No" name="sandbox"> 
			<table class="form-table"> 
				<!--tr>	 
					<th><label for="edit-sandbox">Please select "no" to use the plugin on the WimTV server. Select "yes" to try the service only on test server</label></th>
					<td>
						<select id="edit-sandbox" name="sandbox" class="form-select">
						<option value="No" <?php if (get_option("wp_sandbox")=="No") echo "selected='selected'" ?>>No</option>
						<option value="Yes" <?php if (get_option("wp_sandbox")=="Yes") echo "selected='selected'" ?>>Yes, for Developer or Test</option>
						</select>
					</td>
				</tr-->
			
            				<!--tr>
								<th><label for="edit-publicPage"><?php _e('Would you like to add the "Share" button in the video player?',"wimtvpro");?></label></th>
								<td>
									<select id="edit-publicPage" name="publicPage" class="form-select">
										<option value="No" <?php if (get_option("wp_shareVideo")=="No") echo "selected='selected'" ?>>No</option>
										<option value="Yes" <?php if (get_option("wp_shareVideo")=="Yes") echo "selected='selected'" ?>><?php _e("Yes"); ?></option>
									</select>

								</td>
							</tr-->
            
							<tr>
								<th><label for="edit-publicPage"><?php _e("Would you like to add a public WimVod Page to your site?","wimtvpro");?></label></th>
								<td>
									<select id="edit-publicPage" name="publicPage" class="form-select">
										<option value="No" <?php if (get_option("wp_publicPage")=="No") echo "selected='selected'" ?>>No</option>
										<option value="Yes" <?php if (get_option("wp_publicPage")=="Yes") echo "selected='selected'" ?>><?php _e("Yes"); ?></option>
									</select>

								</td>
							</tr>
						
						</table>
						
						<input type="hidden" name="wimtvpro_update" value="Y" />
						<?php submit_button(__("Save changes","wimtvpro")); ?>
					</form> 
				</div>	
			
		</div>
	
	<?php
	}
	
	else {
	
		echo "<div class='wrap'>";
	
		// https://www.wim.tv/wimtv-webapp/rest/users/{username}/profile
		$urlUpdate = get_option("wp_basePathWimtv") . "profile";
		$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		
		if (isset($_POST['wimtvpro_update']) && $_POST['wimtvpro_update'] == 'Y'){
			//UPDATE INFORMATION
			
			foreach ($_POST as $key=>$value){		
				if ($value=="")  unset($_POST[$key]);
				//$key = str_replace("Uri","URI",$key);
				$dati[$key] = $value;
			}
  
			unset($dati['wimtvpro_update']);
			unset($dati['submit']);
			
			$jsonValue = json_encode($dati);	
			//var_dump  ($jsonValue);
			$ch = curl_init();
	        curl_setopt($ch, CURLOPT_URL, $urlUpdate);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json","Accept: application/json","Accept-Language: " . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	        curl_setopt($ch, CURLOPT_USERPWD, $credential);
	        curl_setopt($ch, CURLOPT_POST, TRUE);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonValue); 
	        $response = curl_exec($ch);
	        
	        $arrayjsonst = json_decode($response);
	        curl_close($ch);
			 if ($dati['paypalEmail']!="") 
				update_option('wp_activePayment', "true");
			  else
				update_option('wp_activePayment', "false");
			  
			
			if ($arrayjsonst->result=="SUCCESS") {
			
				 echo '<div class="updated"><p><strong>';
	              _e("Update successful","wimtvpro");
	              echo  '</strong></p></div>';
	
			
			} else {
			
				foreach ($arrayjsonst->messages as $message){
	            		$testoErrore .=  $message->field . " : " .  $message->message . "<br/>";         	
	            }
	            $error++;
	
				echo '<div class="error"><p><strong>' . $testoErrore . '</strong></p></div>';
	
			}
			
			foreach ($dati as $key=>$value){		
				$key = str_replace("URI","Uri",$key);
			}

			

		} 
		
		//Read
	        $response = apiGetProfile();
			$dati = json_decode($response, true);
			//var_dump ($dati);
		
		switch ($_GET['update']){
		
			case "1": //Payment
			
			  echo '<h2>' . __("Monetisation","wimtvpro") . '</h2>';
			  $view_page = wimtvpro_alert_reg();
			  $submenu = wimtvpro_submenu($view_page);

			  echo str_replace("payment","current",$submenu);

			  echo '<div class="clear"></div>
			  <p>';
			  _e("Please complete the following fields if you wish to make financial transactions on Wim.tv (e.g. buy or sell videos, post pay per view videos or bundles). You may wish to fill your data now or do it later by returning in this section of your Settings.","wimtvpro");
			  echo '</p>';

			  echo '
			  
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
				 <h4>' . __("PayPal") . '</h4>
						<table class="form-table">
					
			              	<tr>
			              		<th><label for="paypalEmail">' . __("Paypal Email","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-paypalEmail" name="paypalEmail" value="' . $dati['paypalEmail'] . '" size="100" maxlength="100"/></td>
							</tr>
							
							
						
						</table>

				 
				 <h4>' . __("Tax Info","wimtvpro") . '</h4>
						<table class="form-table">
							<tr>
			              		<th><label for="vatCode">' . __("Tax Code","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-taxCode" name="taxCode" value="' . $dati['taxCode'] . '" size="80" maxlength="20"/></td>
							</tr>
							<tr>
								<th><label for="vatCode">' . __("VAT Code","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-vatCode" name="vatCode" value="' . $dati['vatCode'] . '" size="80" maxlength="20"/></td>
							</tr>

						
						</table>
						
				<h4>' . __("Billing address","wimtvpro") . '</h4>
						<table class="form-table">
					
			              								
							<tr>
			              		<th><label for="billingAddress[street]">' . __("Street","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-billingAddressStreet" name="billingAddress[street]" value="' . $dati['billingAddress']['street'] . '" size="100" maxlength="100"/></td>
							</tr>

							<tr>
			              		<th><label for="billingAddress[city]">' . __("City","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[city]" value="' . $dati['billingAddress']['city'] . '" size="100" maxlength="100"/></td>
							</tr>

							<tr>
			              		<th><label for="billingAddress[state]">' . __("State","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[state]" value="' . $dati['billingAddress']['state'] . '" size="100" maxlength="100"/></td>
							</tr>
							
							<tr>
			              		<th><label for="billingAddress[zipCode]">' . __("Zip/Postal Code","wimtvpro") . '</label></th>
								<td><input type="text" id="edit-billingAddressCity" name="billingAddress[zipCode]" value="' . $dati['billingAddress']['zipCode'] . '" size="100" maxlength="100"/></td>
							</tr>

						
						</table>';
						
						echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
						submit_button(__("Update","wimtvpro"));

						
			  echo '</form>';
 	
			  /*
			  "paypalEmail": "-- indirizzo email account Pay Pal --",
			  "companyName": "-- nome azienda --",
			  "affiliateConfirm": "-- hai i diritti legali per operare come affiliato dell&#65533;azienza --",
			  "vatCode": "-- P. iva --",
			  "taxCode": "-- CF --",
			  "billingAddress": {
			  	"street": "-- via  --",
			  	"city": "-- citt&#65533; --",
			  	"state": "-- provincia --",
			  	"zipCode": "-- cap --"
			  	}
			  */
			break;
			
			case "2": //Live
			
			  echo '<h2>' . __('Live configuration',"wimtvpro") . '</h2>';
			  $view_page = wimtvpro_alert_reg();
			  $submenu = wimtvpro_submenu($view_page);

			  echo str_replace("live","current",$submenu);


			  if (!isset($dati['liveStreamPwd'])) $dati['liveStreamPwd']= "";
			  if ($dati['liveStreamPwd']=="null") $dati['liveStreamPwd']= "";
				
			  echo '<div class="clear"></div>
			  <p>In ' . __('this section you can enable live streaming settings to better match your specific needs. Choose between "Live streaming" to stream your own events, or use the features reserved for Event Organisers and Event Resellers to play the role of organiser or distributor (on behalf of Event Organiser) of live events.',"wimtvpro"). '</p>';
			  echo '
			  
			  <script>
			  
			  	jQuery(document).ready(function() {
			    
			    	jQuery("#edit-liveStreamEnabled,#edit-eventResellerEnabled,#edit-eventOrganizerEnabled").click(
			    	
			    	function() {
			    		var name = jQuery(this).attr("name");
			    		if (jQuery(this).attr("checked")=="checked") {
			    			jQuery("." + name).remove();
			    		}
			    		else {
			    		
			    			jQuery("<input>").attr({
							    type: "hidden",
							    value: "false",
							    name: name ,
							    class: name ,
							}).appendTo(".hidden_value");
	
			    		}
			    	})
			    
			    });
			  
			  </script>
			  
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
				 <table class="form-table">
					
			              								
							<tr>
			              		<th><label for="liveStreamEnabled">' . __("Live streaming","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-liveStreamEnabled" name="liveStreamEnabled" value="true"
								  ';
								  if (strtoupper($dati['liveStreamEnabled'])=="TRUE") {
									  echo ' checked="checked"';
								  	  update_option('wp_activeLive', "true");
								  } else {
							       	  update_option('wp_activeLive', "false");
								  }
								 echo  ' 
								  />
								  <div class="description">'  . __("Enables you to live stream your events with WimTV","wimtvpro")  . '</div>
								</td>
							</tr>
							
							<tr> 
			              		<th><label for="liveStreamPwd">' . __("Password") . '</label></th>
								<td>
								  <input type="password" id="edit-liveStreamPwd" name="liveStreamPwd" value="' . $dati['liveStreamPwd'] .  '"/>
								  <div class="description">' . __("A password is required for live streaming (for authenticating yourself with the streaming server).","wimtvpro") .  '</div>
								</td>
							</tr>

							
							<tr>
			              		<th><label for="eventResellerEnabled">' . __("Live stream events resale","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventResellerEnabled" name="eventResellerEnabled" value="true"
								  ';
								  if (strtoupper($dati['eventResellerEnabled'])=="TRUE") echo ' checked="checked"';
								 echo '
								  />
								  <div class="description">' . __("Enables you to distribute live events organised by other parties (Event Organisers).","wimtvpro") . '</div>
								</td>
							</tr>
							
							<tr>
			              		<th><label for="eventOrganizerEnabled">' . __("Live stream events organisation","wimtvpro") . '</label></th>
								<td>
								  <input type="checkbox" id="edit-eventOrganizerEnabled" name="eventOrganizerEnabled" value="true"
								  ';
								  if (strtoupper($dati['eventOrganizerEnabled'])=="TRUE") echo ' checked="checked"';
								 echo '
								  />
								  <div class="description">' . __("Select if you want to organise live evants and collaborate with an Event Reseller for their distribution.","wimtvpro") . '</div>
								</td>
							</tr>


						
							
						
						</table>';
						echo '<div class="hidden_value"></div>';
						echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
						submit_button(__("Update","wimtvpro")); 

						
			  echo '</form>';

			
				//"liveStreamPwd": "-- pwd per il live di wim.tv --",
  				//"liveStreamEnabled": "-- abilita live true|false --"
  				//eventResellerEnabled": "-- abilita event reselling true|false --",
  				//"eventOrganizerEnabled": "-- abilita event organizing true|false --",
			
			break;
			
			case "3": //Update 
			 	echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( ".pickadate" ).datepicker({
		            dateFormat: "dd/mm/y",     });
		  		  				  
		  		});
		  		</script>
		     	';

			
			  echo '<h2>' . __("Personal Info","wimtvpro") . '</h2>';
			  $view_page = wimtvpro_alert_reg();
			  $submenu = wimtvpro_submenu($view_page);

			  echo str_replace("user","current",$submenu);
			  
			  echo '<div class="clear"></div>
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
			  <h4>' . __("Personal Info","wimtvpro") . '</h4>
				<table class="form-table">			
					<tr>
						<th><label for="edit-name">' . __("First Name","wimtvpro") . '<span class="form-required" title="">*</span></label></th>
						<td><input type="text" id="edit-name" name="name" value="' . $dati['name'] . '" size="40" maxlength="200"/></td>
					</tr>
					<tr><th><label for="edit-Surname">' . __("Last Name","wimtvpro") . '<span class="form-required" title="">*</span></label></th>				
						<td><input type="text" id="edit-Surname" name="surname" value="' . $dati['surname'] . '" size="40" maxlength="200"/></td>
					</tr>
					<tr>
						<th><label for="edit-Email">Email<span class="form-required" title="">*</span></label></th>
						<td><input type="text" id="edit-email" name="email" value="' . $dati['email'] . '" size="80" maxlength="200"/></td>
					</tr>
					
					<tr>
						<th><label for="sex">' . __("Gender","wimtvpro") . '<span class="form-required" title="">*</span></label></th>
						<td>
							<select id="edit-sex" name="sex" class="form-select">
								<option value="M"';
								if ( $dati['sex']=="M") echo 'selected="selected"';
								echo '>M</option>
								<option value="F"';
								if ( $dati['sex']=="F") echo 'selected="selected"';
								echo '>F</option>
							</select>
	
						</td>
						</tr>
						<tr>
						<th><label for="dateOfBirth">' . __("Date of Birth","wimtvpro") . '</label></th>
						<td>
							<input  type="text" class="pickadate" id="edit-giorno" name="dateOfBirth" value="' . $dati['dateOfBirth'] . '" size="10" maxlength="10">		     				
							<div class="description">dd/mm/yy</div>
						</td>

						
					</tr>
	
					
				</table>';

			  echo '
			  
			  			  
			  
				 
				 <h4>' . __("Social networks","wimtvpro") . '</h4>
				 
				 <table class="form-table">
					
			              								
						<tr>
							<th><label for="facebookUri">Facebook http://</label></th>
							<td>
								<input  type="text"  id="edit-facebookURI" name="facebookUri" value="' . $dati['facebookURI'] . '" size="100" maxlength="100">	
							</td>
						</tr>
						
						<tr>
						
						<tr>
						
						<th><label for="twitterUri">Twitter http://</label></th>
							<td>
								<input  type="text"  id="edit-twitterURI" name="twitterUri" value="' . $dati['twitterURI'] . '" size="100" maxlength="100">	
							</td>

						
						</tr>

						
						<th><label for="linkedInUri">LinkedIn http://</label></th>
							<td>
								<input  type="text"  id="edit-LinkedInUri" name="linkedInUri" value="' . $dati['linkedInURI'] . '" size="100" maxlength="100">	
							</td>

						
						</tr>
						
						
	

						
							
						
				  </table>';
				echo '<div class="hidden_value"></div>';
				echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
				submit_button(__("Update","wimtvpro")); 

						
			  echo '</form>';

			
			break;
			
			
			case "4": //Features
			 	echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( "#edit-hidePublicShowtimeVideos" ).change( function(){

		          	if  (jQuery(this).val()=="false") {
				      	jQuery("#viewPage").fadeIn();
				      }else{		      
				      	jQuery("#viewPage").fadeOut();
				      
				      }
 
		          });
		  		  				  
		  		});
		  		</script>
		     	';
$view_page = wimtvpro_alert_reg();
			  $submenu = wimtvpro_submenu($view_page);
echo "<h2>" . __("Features","wimtvpro") . "</h2>";
			  echo str_replace("other","current",$submenu);
			  
			  
			  echo '<div class="clear"></div>
			  <form enctype="multipart/form-data" action="#" method="post" id="configwimtvpro-group" accept-charset="UTF-8">
		
				<table class="form-table">			
					<tr>
						<th><label for="edit-name">' . __("Index and show public videos on WimTV","wimtvpro") . ' (<a href="http://www.wim.tv" target="new">www.wim.tv</a>)</label></th>
						<td>
							<select id="edit-hidePublicShowtimeVideos" name="hidePublicShowtimeVideos" class="form-select">
								<option value="false"';
								if ( $dati['hidePublicShowtimeVideos']=="false") echo 'selected="selected"';
								echo '>' . __("Yes") . '</option>
								<option value="true"';
								if ( $dati['hidePublicShowtimeVideos']=="true") echo 'selected="selected"';
								echo '>No</option>
							</select>
	
						</td>
						
												
					</tr>
	
					
				</table>';

              $page_name = "";
              if (isset($dati['pageName'])) {
                  $page_name = $dati['pageName'];
              }

                $page_description = "";
                if (isset($dati['pageDescription'])) {
                    $page_description = $dati['pageDescription'];
                }

			  echo '
			  
				 
				 <table id="viewPage"';
				 
				 if ( $dati['hidePublicShowtimeVideos']=="true") echo ' style="display:none; "';
				 
				 echo ' class="form-table">
					
					<tr><td colspan="2"><h4>' . __("WimTV Page","wimtvpro") . '</h4></td></tr>			              
								
						<tr>
							<th><label for="pageName">' . __("Page Name","wimtvpro") . '</label></th>
							<td>
								<input  type="text"  id="edit-pageName" name="pageName" value="' . $page_name . '" size="100" maxlength="100">
							</td>
						</tr>
						

						
						<tr>
						
						<th><label for="pageDescription">' . __("Page Description","wimtvpro") . '</label></th>
							<td>
								<textarea  type="text" style="width:260px; height:90px;" id="edit-pageDescription" name="pageDescription">' . $page_description . '</textarea>
							</td>

						
						</tr>
	
						
				  </table>';
				echo '<div class="hidden_value"></div>';
				echo '<input type="hidden" name="wimtvpro_update" value="Y" />';
				submit_button(__("Update","wimtvpro")); 

						
			  echo '</form>';

			
				//"liveStreamPwd": "-- pwd per il live di wim.tv --",
  				//"liveStreamEnabled": "-- abilita live true|false --"
  				//eventResellerEnabled": "-- abilita event reselling true|false --",
  				//"eventOrganizerEnabled": "-- abilita event organizing true|false --",

			
			break;

			
		
		}
	
		echo "</div>";
	
	}	
  }
  
  else {
  	
		
echo "<div class='wrap'>";
		echo "<h2>" . __("Pricing","wimtvpro");
		if (isset($_GET['return']))  echo "<a href='?page=WimVideoPro_Report' class='add-new-h2'>" . __("Back") . "</a>";
		echo "</h2>";
		
		$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		$uploads_info = wp_upload_dir();
		$directoryCookie = $uploads_info["basedir"] .  "/cookieWim";
		if (!is_dir($directoryCookie)) {
		  $directory_create = mkdir($uploads_info["basedir"] . "/cookieWim");
		}
	
		if (isset($_GET['upgrade'])){
		    		    
		    $fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['upgrade'] . ".txt";
		    
            if (!is_file($directoryCookie. "/" . $fileCookie)) {
            	$f = fopen($directoryCookie. "/" . $fileCookie,"w");
				fwrite($f,"");
				fclose($f);
		    }
			//Update Packet
			$data = array("name" => $_GET['upgrade']);                                                                    
			$data_string = json_encode($data);
			
			// chiama
			$ch = curl_init();
			$my_page = admin_url() . "?page=WimTvPro&pack=1&success=" . $_GET['upgrade']; 
		    if (isset($_GET['return'])) $my_page .= "&return=true";

			curl_setopt($ch, CURLOPT_URL,  get_option("wp_basePathWimtv") . "userpacket/payment/pay?externalRedirect=true&success=" . urlencode ($my_page));
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			    'Content-Type: application/json', 'Accept-Language: ' . $_SERVER["HTTP_ACCEPT_LANGUAGE"],'Content-Length: ' . strlen($data_string))                                                                       
			);  
	
	 		// salva cookie di sessione
			curl_setopt($ch, CURLOPT_COOKIEJAR, $directoryCookie . "/" . $fileCookie);	
			$result = curl_exec($ch);
		
			curl_close($ch);
			$arrayjsonst = json_decode($result);
			
			if ($arrayjsonst->result=="REDIRECT") {
			  echo "
			  	 <script>
					  jQuery(document).ready(function() {
						jQuery.colorbox({
						    onLoad: function() {
				        		jQuery('#cboxClose').remove();
				        	},
				        	html:'<h2>" . $arrayjsonst->message . "</h2><h2><a href=\"" . $arrayjsonst->successUrl . "\">Yes</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"#\">" . __("No") . "</a></h2>'
				        })
				     });   		
                 </script> 
                  ";
                 
			}  else {
			
				//var_dump($arrayjsonst);
			
			}

		
		}
		
		
		if (isset($_GET['success'])) {	

			//controlla stato pagamento
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, get_option("wp_basePathWimtv") . "userpacket/payment/check");
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			 curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $credential);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			
			$fileCookie = "cookies_" . get_option("wp_userWimtv") . "_" . $_GET['success'] . ".txt";
			
			// Recupera cookie sessione
			curl_setopt($ch, CURLOPT_COOKIEFILE,  $directoryCookie . "/" . $fileCookie);
			
			$result = curl_exec($ch);
			curl_close($ch);
			$arrayjsonst = json_decode($result);
			
			
			
			
		
		}
		
		if (!isset($_GET['return'])){
		$view_page = wimtvpro_alert_reg();
			  $submenu = wimtvpro_submenu($view_page);
	
			echo str_replace("packet","current",$submenu) ; 
		}
		$url_packet_user = get_option("wp_basePathWimtv") . "userpacket/" . get_option("wp_userWimtv");

		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url_packet_user);
	   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:' . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
	    curl_setopt($ch, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	
	    $response = curl_exec($ch);
	    $packet_user_json = json_decode($response);
	    //var_dump ($response);
    	$id_packet_user = $packet_user_json->id;
    	$createDate_packet_user = $packet_user_json->createDate;
		$updateDate_packet_user = $packet_user_json->updateDate;
		
		$createDate = date('d/m/Y', $createDate_packet_user/1000);
		$updateDate = date('d/m/Y', $updateDate_packet_user/1000);
		$dateRange = getDateRange($createDate , $updateDate );
		
		$count_date = $packet_user_json->daysLeft;
		//$count_date = count($dateRange)-1;
 		
	    curl_close($ch);


		$url_packet = get_option("wp_basePathWimtv") . "commercialpacket"; 

	  	$header = array("Accept-Language: "  . $_SERVER["HTTP_ACCEPT_LANGUAGE"]);


		$ch2 = curl_init();
	    curl_setopt($ch2, CURLOPT_URL, $url_packet);
	    curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch2, CURLOPT_VERBOSE, 0);
	    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	    $response2 = curl_exec($ch2);	    
	    //$info = curl_getinfo($ch2);
	    $packet_json = json_decode($response2);
	    //var_dump ($packet_json);
	    curl_close($ch2);
	    //var_dump ($response2);
	echo "<div class='empty'></div>";
		echo "<h4>"  . __("Use of WimTV requires subscription to a monthly storage and bandwidth package","wimtvpro") . "</h4>";

		echo "<table class='wp-list-table widefat fixed pages'>";
	    echo "<thead><tr><th></th>";
	    foreach ($packet_json -> items as $a) {
	    	
	    	echo "<th><b>" . $a->name . "</b></th>";	
	    
	    }

	    echo "</thead>";
	    echo "<tbody>";
		echo "<tr class='alternate'>";
			echo "<td>" . __("Bandwidth","wimtvpro") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->band . " GB</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr>";
			echo "<td>" . __("Storage","wimtvpro") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->storage . " GB</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr class='alternate'>";
			echo "<td>" . __("Support","wimtvpro") . "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . $a->support . "</td>";		    
	    	}

		echo "</tr>";

	
		echo "<tr>";
			echo "<td>";
			printf( __( 'Price/mo. for %d Mo', 'wimtvpro' ), "1" );
			echo "</td>";
			foreach ($packet_json -> items as $a) {
	    	echo "<td>" . number_format($a->price,2) . " &euro; / " . __("m","wimtvpro") . "</td>";		    
	    	}

		echo "</tr>";
		
		echo "<tr class='alternate'>";
			echo "<td></td>";
			foreach ($packet_json -> items as $a) {
	    	//echo "<td>" . $a->dayDuration . " - " . $a->id . "</td>";
	    	echo "<td>";
	    	if ($id_packet_user==$a->id) {
	    		
	    		echo "<img  src='" . plugins_url('images/check.png', __FILE__) . "' title='Checked'><br/>";
	    		if ($a->name!="Free")
					echo $count_date . " " . __("day left","wimtvpro");
	    	}
	    	else {
	    		echo "<a href='?page=WimTvPro&pack=1";
	    		if (isset($_GET['return'])) echo "&return=true";
		    	echo "&upgrade=" . $a->name;
		    	echo "'><img class='icon_upgrade' src='" . plugins_url('images/uncheck.png', __FILE__) . "' title='Upgrade'>";
		    	echo "</a>";
			}
			echo "</td>"; 		    
	    }

		echo "</tr>";


	
		echo "</tbody>";
		echo "</table>";
		
		echo "<h4>(**) " . __("VAT to be added","wimtvpro") . "</h4>";
		
		echo "<p>" .
		__("If, before the end of the month, you","wimtvpro") . 
		"<ol><li>" . 
		__("reach 80% level you will be notified","wimtvpro") .  "</li><li>" .
		__("exceed 100% level you will be asked to upgrade to another package.","wimtvpro")
		 . "</li></ol></p>";
		
		echo "<h3>" . __("Note that, if you stay within the usage limits of the Free Package, use of WimTV is free","wimtvpro") . "</h3>";
		
		
		echo "<h3>" . __("If you license content and/or provide services in WimTV, revenue sharing will apply","wimtvpro") . "</h3>";
		
		echo "<h3>" . __("Enjoy your WimTVPro video plugin!","wimtvpro") . "</h3>";

		
		
		echo "</div>";
	}
	
}

 

function media_wimtvpro_process() {
  media_upload_header();
  
  $videos .= "<h3 class='media-title'>WimVod</h3><table class='itemsInsert'>" . wimtvpro_getThumbs(TRUE, FALSE, TRUE) . "</table><div class='empty'></div>";
  
  global $wpdb; 
  $table_name = $wpdb->prefix . 'wimtvpro_playlist';
  $array_playlist = $wpdb->get_results("SELECT * FROM {$table_name} WHERE uid='" . get_option("wp_userwimtv") . "'  ORDER BY name ASC");
  $numberPlaylist=count($array_playlist);
  if ($numberPlaylist>0) {
    $videos .= "<h3 class='media-title'>PlayList</h3><ul class='itemsInsert'>";
    foreach ($array_playlist as $record_new) {
	    $listVideo = $record_new->listVideo;
		$title = $record_new->name;
	    $arrayVideo = explode(",", $listVideo);
	    if ($listVideo=="") $countVideo = 0;
	    else $countVideo = count($arrayVideo);
	    
	    $uploads_info = wp_upload_dir();
    	$directory = $uploads_info["baseurl"] .  "/skinWim";
    	$array_videos_new_drupal = array();
		for ($i=0;$i<count($videoList);$i++){
		 foreach ($array_videos as $record_new) {
			if ($videoList[$i] == $record_new->contentidentifier){
				array_push($array_videos_new_drupal, $record_new);
			}
		 }
		}
		
		$playlist = "";
		foreach ($array_videos_new_drupal as $videoT){
			$videoArr[0] = $videoT;
			$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
			
			$configFile  = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'],$videoT->contentidentifier,$videoArr,$dirJwPlayer);
			if (!isset($videoT->urlThumbs)) $thumbs[1] = "";
			else $thumbs = explode ('"',$videoT->urlThumbs);
			
			$playlist .= "{" . $configFile . " 'image':'" . $thumbs[1]  . "','title':'" . str_replace ("+"," ",urlencode($videoT->title)) . "'},";
		
		}
  		$videos .= $playlist;
  		$videos .= "<li>" . $title . "(" .  $countVideo . ")";
  		$videos .= '<input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';
        $send = get_submit_button( __( 'Insert into Post',"wimtvpro" ), 'buttonInsertPlayList', $record_new->id, false );

  		
  		$videos .= $send . '</li>';

  			}
}

  $videos .= "</ul><div class='empty'></div>";
  echo $videos;

}
function wimtvpro_media_menu_handle() {
    return wp_iframe( 'media_wimtvpro_process');
}
add_action('media_upload_wimtvpro', 'wimtvpro_media_menu_handle');


?>
