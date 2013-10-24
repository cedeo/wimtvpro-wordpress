<?php
	
function wimtvpro_listlive() {
	$view_page = wimtvpro_alert_reg();
	
	if ($view_page==TRUE){
				
		echo '<script type="text/javascript">
		function clickImg(obj){ 
			jQuery("a.clickWebProducer img").attr("src", "' . plugins_url('images/webcam.png', __FILE__) . '");
			jQuery(obj).attr("src", "' . plugins_url('images/live_rec.gif', __FILE__) . '");
			
		};
		</script>';
		//echo timezone = ;
     
	  $urlProfile = get_option("wp_basePathWimtv") . "profile";
	  $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $urlProfile);

	  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json","Accept: application/json","Accept-Language: " . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	  curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	  curl_setopt($ch, CURLOPT_USERPWD, $credential);
	  $response = curl_exec($ch);

	  $dati = json_decode($response, true);

	  $enabledLive = $dati["liveStreamEnabled"];
	  curl_close($ch);
		
      if (strtoupper($enabledLive)=="TRUE"){

		  $noneElenco = FALSE;
		  $userpeer = get_option("wp_userWimtv");
		  $url_live =  get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts";
		  $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		
		   
		
		  switch ($_GET['namefunction']) {
		  
		         case "addLive":
		     
		       $noneElenco = TRUE;
		       //aggiungere script per pickdata e pickhour
		       if (isset($_POST["wimtvpro_live"])) {
		          wimtvpro_savelive("insert");
		       }
		       $name = "";
		       $payperview = "0";
		       $url = "";
		       $giorno = "";
		       $ora = "00:00";
		       $tempo = "00:00";
		       
		     
		     break;
		     case "modifyLive":
		     
		       $noneElenco = TRUE;
		
			   if (isset($_POST["wimtvpro_live"])) {
		          wimtvpro_savelive("modify");
		       }
		
		       
		       //Recove dates live
		       $url_live .= "/" . $_GET['id'] . "/embed";
		       $ch_embedded = curl_init();
		       curl_setopt($ch_embedded, CURLOPT_URL, $url_live);
		       curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);
		       curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
		       curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		       curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
		       curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
		       $dati = curl_exec($ch_embedded);
		
		       $arraydati = json_decode($dati);
		       $name = $arraydati->name;
		       if ($arraydati->paymentMode=="FREEOFCHARGE") 
		        $payperview = "0";
		       else
		        $payperview =  $arraydati->pricePerView;
		       $url = $arraydati->url;
		       $giorno = $arraydati->eventDate;
		       $giorno = $arraydati->eventDate;
		       $timezone = $arraydati->eventTimeZone;
		       if (intval($arraydati->eventMinute)<10) $arraydati->eventMinute = "0" .  $arraydati->eventMinute;
		       $ora = $arraydati->eventHour . ":" . $arraydati->eventMinute;
		       $tempo = $arraydati->duration;
		       $ore = floor($tempo / 60);
		       $minuti = $tempo % 60;
		       
		       $durata = $ore . "h";
		       if ($minuti<10)
		  			$durata .= "0";
			   $durata .= $minuti;
		       
		 
		    break;
		     
		    case "deleteLive":
		      $url_live .= "/" . $_GET['id'];
		      $ch = curl_init();
		      curl_setopt($ch, CURLOPT_URL, $url_live);
		      curl_setopt($ch, CURLOPT_VERBOSE, 0);
		      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		      curl_setopt($ch, CURLOPT_USERPWD, $credential);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: " . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
		      curl_setopt($ch, CURLOPT_POST, TRUE);
		      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		      $response = curl_exec($ch);
		      //echo $response;
		      curl_close($ch);   
		     break;	
			
		     default:
		      break;
		  }
		  /*
		  global $wpdb;
		  $post_id  = $wpdb->get_var("SELECT max(ID) FROM $wpdb->posts WHERE post_name = 'wimlive_wimtv'");
		  $my_streaming_wimtv= array();
		  $my_streaming_wimtv['ID'] = $post_id;
		  $my_streaming_wimtv['post_content'] = wimtvpro_elencoLive("video", "0") . "<br/>UPCOMING EVENT<br/>" . wimtvpro_elencoLive("list", "0");
		  wp_update_post($my_streaming_wimtv);
			*/
		  
		  if ($noneElenco==FALSE) {
			  
			  
			  
		    global $post_type_object;
		    $screen = get_current_screen();
		    
		    echo " <div class='wrap'><h2>WimLive";
		   	echo " <a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=addLive' class='add-new-h2'>" . __( 'New','wimtvpro' ) . "</a>";
		    echo "</h2>";
		     echo "<p>";
			_e("Here you can create and post live streaming events to your website.","wimtvpro");
			echo "<br/>";
			_e("This service can be used in one of these two modalities:","wimtvpro");
			echo "<ol>";
			echo "<li>";
			_e("Install a third party video encoding software (e.g. Adobe Flash Media Live Encoder, Wirecast etc.) on your pc: this solution is recommended if you want to connect an external video camera to your pc","wimtvpro"); 
			echo "</li>";
			echo "<li>";
			_e('Use WimTV encoding software. Broadcast directly from your webcam, by simply clicking the icon "Live now". By clicking “Live now” icon, the producer will open in a new browser tab. Keep it open during the whole transmission.',"wimtvpro"); 
			echo "</li>";
			echo "</ol>";

			echo "</p>";


		
		    echo "<table id='tableLive' class='wp-list-table widefat fixed pages'>";
		    echo "<thead><tr><th>" . __("Title") . "</th><th>Live Now</th><th>Pay-Per-View</th><th>URL</th><th>" . __("Schedule") . "</th><th>" . __("Embed Code","wimtvpro") . "</th><th>" . __("Tools") . "</th></tr></thead>";
		    echo "<tbody>";

		    echo wimtvpro_elencoLive("table", "all");
		    echo "</thead></table>";
		    echo "</div>";
		
		    
		  } else {
		     //aggiungere script per richiamare la CREATE URL
		      echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery(document).ready(function(){
		  		    var timezone = -(new Date().getTimezoneOffset())*60*1000;
		  		    jQuery("#timelivejs").val(timezone);
                  });    
		  		  jQuery(document).ready(function(){jQuery( ".pickatime" ).timepicker({  defaultTime:"00:00"  });});
		  		  jQuery(document).ready(function(){jQuery( ".pickaduration" ).timepicker({   defaultTime:"00h05",showPeriodLabels: false,timeSeparator: "h", });});});
		  		  jQuery(document).ready(function(){jQuery( ".pickadate" ).datepicker({
		            dateFormat: "dd/mm/yy",
		            autoSize: true,
		            minDate: 0,
		          });});
	
		          jQuery(".edit-eventTimeZone[value=\"' . $timezone . '\"]").attr("selected", "selected");
		          
		  		</script>
		     ';
		     echo "<div class='wrap'><h2>WimLive";
		   	 echo "<a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=listLive' class='add-new-h2'>" . __( 'Return to list', 'wimtvpro') . "</a> ";
			 echo "</h2>";
			 echo "<p>";
			_e("Please complete all the fields marked with an *","wimtvpro");
		
			 ?>
			 <form action="#" method="post" id="wimtvpro-wimlive-form" accept-charset="UTF-8">
			 
			 <p><label for="edit-name"><?php _e("Title"); ?> <span>*</span></label>
		     <input type="text" id="edit-name" name="name" value="<?php echo $name;?>" size="100" maxlength="200"></p>
		     <div class="description"><?php _e("Please insert the title of the live event","wimtvpro"); ?>*</div>
		     
		     <p><label for="edit-payperview"><?php _e("Set the price to access the event","wimtvpro"); ?> *</label>
		     <input type="text" id="edit-payperview" name="payperview" value="<?php echo $payperview;?>" size="10" maxlength="5" class="form-text required"></p>
		     <div class="description">
             <?php _e("Please, set a price for viewing your event (set 0 for free of charge). Prices are expressed in &euro;","wimtvpro"); ?></div>
		
		     <p><label for="edit-url">Url *</label>
		     <input type="text" id="edit-url" name="Url" value="<?php echo $url;?>" size="100" maxlength="800" class="form-text required">
		     </p>
		     
		     
		     <div class="description"><p id="urlcreate"><?php _e('You need the streaming server URL. Click “Obtain URL" button to get one',"wimtvpro"); ?> 
		       <b class="button createUrl"><?php _e("Obtain URL","wimtvpro"); ?></b></p>
		       <b id="'<?php echo get_option("wp_userWimtv");?>'" class="removeUrl"><?php _e("Remove");?> Url</b>
		       <div class="passwordUrlLive">
			     <?php _e("Password Live is missing, insert a password for live streaming:","wimtvpro"); ?> 
                 <input type="password" id="passwordLive" value="" />
                 <b class="button  createPass"><?php _e("Save");?></b>
               </div>
		     </div>
		
			 <p> <label for="edit-url"><?php _e("Event status","wimtvpro"); ?> * </label><br/>
		     	<?php _e("Public","wimtvpro"); ?> <input type="radio" name="Public" value="true" checked="checked"/> |
		     	<?php _e("Private","wimtvpro"); ?> <input type="radio" name="Public" value="false"/>
		     	<div class="description">
				
                <?php
                sprintf(_e('If you want to index your event on %d, and in WimView app, select "Public"','wimtvpro'),'<a target="_blank" href="http://wimlive.wim.tv">wimlive.wim.tv</a>')
				?>
                </div>
		     </p>
		     	
		     	 <p> <label for="edit-record"><?php _e("Record event","wimtvpro"); ?></label><br/>
		     	<?php _e("Yes");?> <input type="radio" name="Record" value="true" checked="checked"/> |
		     	<?php _e("No","wimtvpro");?> <input type="radio" name="Record" value="false"/>
                <div class="description"><?php _e("Select “Yes” if you want to record your event. The recorded video will be listed among your videos in WimBox","wimtvpro"); ?></div>
                
		     </p>
		
		
		<?php 
			$currentTimeZone =ini_get('date.timezone');
		?>
		     <p><label for="edit-giorno"><?php _e("Start date","wimtvpro");?> <?php _e("dd/mm/yy","wimtvpro");?> *</label>
		     <input  type="text" class="pickadate" id="edit-giorno" name="Giorno" value="<?php echo $giorno;?>" size="10" maxlength="10"></p>
			
		     <p><label for="edit-ora"><?php _e("Start time","wimtvpro");?> *</label>
		     <input class="pickatime" type="text" id="edit-ora" name="Ora" value="<?php echo $ora;?>" size="10" maxlength="10">
		     <label for="edit-eventTimeZone"><?php _e("Time zone","wimtvpro");?></label>
		 <select id="edit-eventTimeZone" name="eventTimeZone">
		           
		           <option value="">----------------------------------</option>
		           <option value="Kwajalein">(GMT -12:00) Eniwetok, Kwajalein</option>
		           <option value="Pacific/Pago_Pago">(GMT -11:00) Midway Island, Samoa</option>
		           <option value="US/Hawaii">(GMT -10:00) Hawaii</option>
		           <option value="US/Alaska">(GMT -9:00) Alaska</option>
		           <option value="America/Los_Angeles">(GMT -8:00) Pacific Time (US &amp; Canada)</option>
		           <option value="America/Denver">(GMT -7:00) Mountain Time (US &amp; Canada)</option>
		           <option value="America/Chicago">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>
		           <option value="America/New_York">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>
		           <option value="America/Halifax">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>
		           <option value="Canada/Newfoundlan">(GMT -3:30) Newfoundland</option>
		           <option value="America/Sao_Paulo">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>
		           <option value="Atlantic/South_Georgia">(GMT -2:00) Mid-Atlantic</option>
		           <option value="Atlantic/Cape_Verde">(GMT -1:00 hour) Azores, Cape Verde Islands</option>
		           <option value="Europe/London">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>
		           <option value="Europe/Rome">(GMT +1:00 hour) Rome, Madrid, Paris, Copenhagen</option>
		           <option value="Europe/Istanbul">(GMT +2:00) Helsinki, Istanbul, Kaliningrad, South Africa</option>
		           <option value="Europe/Moscow">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>
		           <option value="Asia/Tehran">(GMT +3:30) Tehran</option>
		           <option value="Asia/Dubai">(GMT +4:00) Abu Dhabi, Dubai, Muscat, Baku, Tbilisi</option>
		           <option value="Asia/Kabul">(GMT +4:30) Kabul</option>
		           <option value="Indian/Maldives">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>
		           <option value="Asia/Calcutta">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>
		           <option value="Asia/Katmandu">(GMT +5:45) Kathmandu</option>
		           <option value="Asia/Dacca">(GMT +6:00) Almaty, Dhaka, Colombo</option>
		           <option value="Asia/Bangkok">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>
		           <option value="Asia/Hong_Kong">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>
		           <option value="Asia/Tokyo">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>
		           <option value="Australia/Adelaide">(GMT +9:30) Adelaide, Darwin</option>
		           <option value="Australia/Sydney">(GMT +10:00) Sydney, Melbourne, Brisbane, Vladivostok</option>
		           <option value="Asia/Magadan">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>
		           <option value="Australia/Auckland">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>
		      </select>
		     </p>
				
				
				
		     <p><label for="edit-duration"><?php _e("Duration","wimtvpro");?> *</label>
		     <input class="pickaduration" type="text" id="edit-duration" name="Duration" value="<?php echo $durata;?>" size="10" maxlength="10">
		     </p>
		     <input type="hidden" name="wimtvpro_live" value="Y" />
		     <input type="hidden" id="timelivejs" name="timelivejs" value="" />
		     <?php submit_button(__("Create","wimtvpro")); ?>
		
		  </form>
			 
		  <?php
	  		
	  	 } 
	  	 
	  }	 
	  
	  else echo "<div class='error'>" . __("To use WimLive, you need to enable live streaming in Live Configuration in your Settings","wimtvpro") . " <a href='admin.php?page=WimTvPro&update=2'>Live streaming</a></div>";
  }
 
}