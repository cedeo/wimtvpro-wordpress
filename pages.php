<?php

include_once("api/api.php");

header('Content-type: application/json');

initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));

   
//Page for view My Video streaming  
function wimtvpro_mystreaming(){
	  $paged = "wimvod"; //ALL VIDEO
	  include("pages/list_media.php");
}


function wimtvpro_playlist(){
	include("pages/list_playlist.php");
	wimtvpro_listplaylist();
}

//Page for view for UPLOAD new Video  
function wimtvpro_upload(){
	include ("pages/upload_video.php");
	wimtvpro_pagedUpload();
}


function wimtvpro_live(){
  include("pages/list_live.php");
  wimtvpro_listlive();
}


function wimtvpro_report (){
  
  include("pages/report.php");
	
}


/*TODO
function wimtvpro_programming(){

  $view_page = wimtvpro_alert_reg();

	if ($view_page==TRUE){

		

		//TODO
		//$basePath = get_option("wp_basePathWimtv");
		//$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
		$basePath ="http://peer.wim.tv/wimtv-webapp/rest/";
		$credential = "simona:12345678";


		
		 switch ($_GET['namefunction']) {
		  
		         case "addProg":
		
					echo " <div class='wrap'><h2>Programming</h2>";
					
					
					//COSTRUISCO IL CALENDARIO
					echo '
					
					<div id="progform">
				   <form>
					<label>Choose a name for the palimpsest (optional)</label>
					<input type="text" value="" id="progname">
					<input type="submit" value="Send" class="fc-button fc-state-default fc-corner-left fc-corner-right submitnow">
					<input type="submit" value="Skip" class="fc-button fc-state-default fc-corner-left fc-corner-right submitnow">
				   </form>
				  </div>
				  <!-- calendar -->
				  <div id="calendar"></div>
				  <div class="embedded">
				   <h1>Embedded code</h1>
				   <textarea id="progCode" onclick="this.focus(); this.select();"></textarea>
				  </div>
					
					';
					
					
				 
				 break;
				 
				 
				 default:
				 
					echo " <div class='wrap'><h2>Programming";
					echo " <a href='" . $_SERVER['REQUEST_URI'] . "&namefunction=addProg' class='add-new-h2'>" . __( 'Add' ) . " " . __( 'Programming' ) . "</a> ";
					echo "</h2>";
					
					/*$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, plugins_url('rest/programmingPool.php', __FILE__));
					$response = curl_exec($ch);		
					echo $response;	*/
					
					/*
					$basePath =get_option("wp_basePathWimtv");
					$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");;
					*/
					/**RIMUOVE IN PRODUZIONE PARTE SOTTO**/
					
					/*
					$basePath ="http://peer.wim.tv/wimtv-webapp/rest/";
					$credential = "simona:12345678";
					
					
					// chiama
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $basePath . "programmings" );
					
					curl_setopt($ch, CURLOPT_VERBOSE, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
					curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					curl_setopt($ch, CURLOPT_USERPWD, $credential);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-Language: " . $_SERVER["HTTP_ACCEPT_LANGUAGE"]));
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

					$response = curl_exec($ch);			
					//var_dump($response);
					$arrayjsonst = json_decode($response);
					echo "<table id='tableLive' class='wp-list-table widefat fixed pages'>";
					echo "<thead><tr><th>Name</th><th>Modifica</th><th>Elimina</th><th>Embedded</th></tr></thead>";
					echo "<tbody>";
					
					foreach ($arrayjsonst->programmings as $prog){
					
						echo "<tr><td>" . $prog->name . "</td><td>" . $prog->identifier . "</td><td></td><td></td></tr>";
						
					}
					
					
					echo "</tbody></table>";
					echo "</div>";
					

					curl_close($ch);
				 
	    		 break;
	     }
		
	}
}

*/

function wimtvpro_live(){

  $view_page = wimtvpro_alert_reg();

 	echo '<script type="text/javascript">
	function clickImg(obj){ 
		jQuery("a.clickWebProducer img").attr("src", "' . plugins_url('images/webcam.png', __FILE__) . '");
		jQuery(obj).attr("src", "' . plugins_url('images/live_rec.gif', __FILE__) . '");
		
	};
	</script>';
	//echo timezone = ;
	if ($view_page==TRUE){
	  $response = apiGetProfile();
	  $dati = json_decode($response, true);

	  $enabledLive = $dati["liveStreamEnabled"];

      if (strtoupper($enabledLive)=="TRUE"){

		  $noneElenco = FALSE;
		  $userpeer = get_option("wp_userWimtv");

          if (!isset($_GET['namefunction']))
              $_GET['namefunction'] = "";
		
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
		       $durata = "00h00m";
               $timezone = "";
		       
		     
		     break;
		     case "modifyLive":
		     
		       $noneElenco = TRUE;
		
			   if (isset($_POST["wimtvpro_live"])) {
		          wimtvpro_savelive("modify");
		       }
		
		       
		       //Recove dates live
		       $dati = apiGetLive($_GET['id']);
		       //trigger_error($dati, E_USER_NOTICE);
		       $arraydati = json_decode($dati);
		       $name = $arraydati->name;
		       if ($arraydati->paymentMode=="FREEOFCHARGE") 
		        $payperview = "0";
		       else
		        $payperview =  $arraydati->pricePerView;
		       $url = $arraydati->url;
		       $giorno = $arraydati->eventDate;
               $timezone = isset($arraydati->eventTimeZone) ? $arraydati->eventTimeZone : $arraydati->timezone;
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
              apiDeleteLive($_GET['id']);
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

		    wimtvpro_elencoLive("table", "all");
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
             <?php _e("Please, set a price for viewing your event (set 0 for free of charge). Prices are expressed in &euro;.","wimtvpro"); ?></div>
		
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


function wimtvpro_report (){
   global $user,$wpdb;  
  $view_page = wimtvpro_alert_reg();
  $user = trim(get_option("wp_userWimtv"));
		if ($view_page==TRUE){
		  
		   $table_name = $wpdb->prefix . 'wimtvpro_video';
			
			if (get_option("wp_sandbox")=="No")
				$baseReport = "http://www.wim.tv:3131/api/";
			else
				$baseReport = "http://peer.wim.tv:3131/api/";
			$megabyte = 1024*1024;
			
			$from = "";
            $to = "";
			
			if ((isset($_POST['from'])) && (isset($_POST['to'])) && (trim($_POST['from'])!="") && (trim($_POST['to'])!="")) {
				$from = $_POST['from'];
				$to = $_POST['to'];
				//convert to  (YYYY-MM-DD)
				$current_month=FALSE;
				list($day_from, $month_from, $year_from) = explode('/',$from);
				//$from_tm = mktime(0, 0, 0, $month, $day, $year);
				list($day_to, $month_to, $year_to) = explode('/',$to);
				//$to_tm = mktime(0, 0, 0,  $month, $day, $year);
			
				$from_tm = mktime (0, 0, 0, $month_from , $day_from, $year_from)*1000;
				$to_tm = mktime (0, 0, 0,  $month_to , $day_to, $year_to)*1000;
				
				
				$from_dmy =$month_from . "/" . $day_from . "/" . $year_from;
				$to_dmy= $month_to . "/" . $day_to . "/" . $year_to;
		
			} else {
				$current_month=TRUE;
				
				$d = new DateTime(date('m/d/y'));
			
		    	//$d->modify('first day of this month');
				//$from_dmy = $d->format('m/d/y');
				$from_dmy = date("m") . "/01/" . date("y");
				//$d->modify('last day of this month');
				//$to_dmy = $d->format('m/d/y');
				
				$dayMe=cal_days_in_month(CAL_GREGORIAN, date("m"), date("y"));
				$to_dmy = date("m") . "/" . $dayMe . "/" . date("y");
			}
		
            $style_date = "";

		    if ($current_month==TRUE){
		    	
				
		    	$url_view  = $baseReport . "users/" . $user . "/views";
		    	$title_views = "Views (" . __("Current month","wimtvpro") . ")";
		    	
		    	$url_stream = $baseReport . "users/" . $user . "/streams"; 	
		    	$title_streams = "Streams (" . __("Current month","wimtvpro") . ")";
		    	$url_view_single = $baseReport . "views/@";
		
		    	
		    	$url_info_user = $baseReport . "users/" . $user; 
		    	$title_user = __("Current month","wimtvpro")  . " <a href='#' id='customReport'>" . __("Change Date","wimtvpro") . "</a> ";
		    	$style_date = "display:none;";
		    	$url_packet = $baseReport . "users/" . $user . "/commercialPacket/usage";
		    	
		    } else {
		    
		    	$url_view = $baseReport . "users/" . $user . "/report?from=" . $from_tm . "&to=" . $to_tm;
		    	$title_views = "Views (" . __("From","wimtvpro") . " " .  $from  . " " . __("To","wimtvpro") . " "  . $to . ")";
		    	
		    	$url_stream = $baseReport . "users/" . $user . "/streams?from=" . $from_tm . "&to=" . $to_tm ;	
		    	$title_streams = "Streams (" . __("From","wimtvpro") . " " . $from . " " . __("To","wimtvpro") . " "  . $to . ")";
		    	$url_view_single = $baseReport . "views/@?from=" . $from_tm . "&to=" . $to_tm ;
		    	
		    	$url_info_user = $baseReport . "users/" . $user . "?from=" . $from_tm . "&to=" . $to_tm . "&format=json";
		    	
				$title_user = "<a href='?page=WimVideoPro_Report'>" . __("Current month","wimtvpro") . "</a> " . __("Change Date","wimtvpro");
				
				
		
		    }
		    
		   	echo "<div class='wrap'><h1>Report user Wimtv " . $user . "</h1>";
			
			    
		    echo ' 
		        <script type="text/javascript">
		  		jQuery(document).ready(function(){
		  		  jQuery( ".pickadate" ).datepicker({
		            dateFormat: "dd/mm/yy",     maxDate: 0,      });
		  		  jQuery("#customReport").click(function(){
					jQuery("#fr_custom_date").fadeToggle();
					jQuery("#changeTitle").html("<a href=\'?page=WimVideoPro_Report\'>' . __("Current month","wimtvpro") . '</a> ' . __("Change Date","wimtvpro") . '")});
				  
				  jQuery(".tabs span").click(function(){
				    var idSpan = jQuery(this).attr("id");
				    jQuery(".view").fadeOut();
				  	jQuery("#view_" + idSpan).fadeIn();
				  	jQuery(".tabs span").attr("class","");
				  	jQuery(this).attr("class","active");
				  });
				  
		  		});
		  		</script>
		     ';
		
			
			echo "<h3 id='changeTitle'>" . $title_user . "</h3>";
			
			echo '<div class="registration" id="fr_custom_date" style="' . $style_date . '">
			
				<form method="post">
					<fieldset>' . __("From","wimtvpro") .  ' <input  type="text" class="pickadate" id="edit-from" name="from" value="' . $from . '" size="10" maxlength="10"> 
					' . __("To","wimtvpro") .  ' <input  type="text" class="pickadate" id="edit-to" name="to" value="' . $to . '" size="10" maxlength="10">
					<input type="submit" value=">" class="button button-primary" /></fieldset>
				</form>
			
			</div>';
		
			
		   	$ch = curl_init();
			
		    curl_setopt($ch, CURLOPT_URL, $url_info_user);
		    curl_setopt($ch, CURLOPT_VERBOSE, 0);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		    $response = curl_exec($ch);
		   	curl_close($ch);
			
			$traffic_json = json_decode($response);
			$traffic = $traffic_json->traffic;
			$storage = $traffic_json->storage;
			
			
			if (isset($url_packet)) {
			
				$ch2 = curl_init();
			    curl_setopt($ch2, CURLOPT_URL, $url_packet);
			    curl_setopt($ch2, CURLOPT_VERBOSE, 0);
			    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
			    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, TRUE);
			    curl_setopt($ch2, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			    $response2 = curl_exec($ch2);
			   	curl_close($ch2);
				
				$commercialPacket_json = json_decode($response2);
				$currentPacket = $commercialPacket_json->current_packet;
				if (($currentPacket->id)>0) $namePacket =  $currentPacket->name;
				else $namePacket =  $currentPacket->error;
				echo "<p>" . __("You commercial packet","wimtvpro") . ": <b>" . $namePacket . "</b>  - <a href='?page=WimTvPro&pack=1&return=WimVideoPro_Report'>" . __("Change")  .  "</a></p> ";
		
				$traffic_of = " of " . $currentPacket->band_human;
				$storage_of = " of " . $currentPacket->storage_human;
				
				$traffic_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->traffic->percent . "%'>" . $commercialPacket_json->traffic->percent_human . "%</div></div>";
				$storage_bar = "<div class='progress'><div class='bar' style='width:" . $commercialPacket_json->storage->percent . "%'>" . $commercialPacket_json->storage->percent_human . "%</div></div>";
				
				$byteToMb = "<b>" . $commercialPacket_json->traffic->current_human . '</b>' . $traffic_of . $traffic_bar;
				$byteToMbS = "<b>" . $commercialPacket_json->storage->current_human . '</b>' . $storage_of . $storage_bar;
			
			} else {
			
				$byteToMb = "<b>" . round($traffic/ $megabyte, 2) . ' MB</b>';
				$byteToMbS = "<b>" . round($storage/ $megabyte, 2) . ' MB</b>';
		
			
			}
			
			//$commercialPacket = $traffic_json->commercialPacket;
			
			if ($traffic=="") {
				echo "<p>" .  __("You did not generate any traffic in this period","wimtvpro") . "</p>";
			} else {
				echo "<p>" .  __("Traffic","wimtvpro") . ": " . $byteToMb . "</p>";
				echo "<p>".  __("Storage space","wimtvpro") . ": " . $byteToMbS . "</p>";
			   	$ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $url_stream);
			    curl_setopt($ch, CURLOPT_VERBOSE, 0);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			    $response = curl_exec($ch);
			   	curl_close($ch);
			    $arrayStream = json_decode($response);
			
			    echo '
			    <div class="summary"><div class="tabs">
			    	<span id="stream" class="active">' . __("View Streams","wimtvpro") . '</span><span id="graph">' .  __("View graphic","wimtvpro") . '</span>
			    </div>
			    <div id="view_stream" class="view"><table class="wp-list-table widefat fixed posts" style="text-align:center;">
			     <h3>' . $title_streams . '</h3>
			      <tr>
			        <th class="manage-column column-title">Video</th>
			    	<th class="manage-column column-title">' . __("Viewers","wimtvpro") . '</th>
			    	<th class="manage-column column-title">' . __("Activate Viewers","wimtvpro") . '</th>
			    	<th class="manage-column column-title">' . __("Max viewers","wimtvpro") . '</th>
			      </tr>
			    ';
			    
			    $dateNumber = array();
			    $dateTraffic = array();
				foreach ($arrayStream as $value){
                    $thumbs = "";
                    if (isset($value->contentId)) {
                        $arrayPlay = $wpdb->get_results("SELECT * FROM {$table_name} WHERE contentidentifier='" . $value->contentId . "'");
                        $thumbs = $arrayPlay[0]->urlThumbs;
                    }
                    $thumbs = str_replace('\"','',$thumbs);
                    if ((isset($value->title))) $video = $thumbs . "<br/><b>" . $value->title . "</b><br/>" . $value->type ;
                    else $video = $thumbs . "<br/>" . $value->id;

                    $html_view_exp = "<b>" . __("Total","wimtvpro") . ": " . $value->views . " " . __("viewers","wimtvpro") . "</b><br/>";
                    $view_exp = $value->views_expanded;
                    if (count($view_exp)>0) {
                        $html_view_exp .= "<table class='wp-list-table'>
                        <tr>
                            <th class='manage-column column-title' style='font-size:10px;'>" . __("Date","wimtvpro") . "</th>
                            <th class='manage-column column-title' style='font-size:10px;'>" . __("Duration","wimtvpro") . "</th>
                            <th class='manage-column column-title' style='font-size:10px;'>" . __("Traffic","wimtvpro") . "</th>
                        </tr>
                        ";
                        foreach ($view_exp as $value_exp){
                            $value_exp->traffic =  round($value_exp->traffic / $megabyte, 2) . " MB";
                            $date_human =  date('d/m/Y', ($value_exp->end_time/1000));
                            $html_view_exp .= "<tr>";
                            $html_view_exp .= "<td style='font-size:10px;'>" . $date_human . "</td>";
                            $html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->duration . "s</td>";
                            $html_view_exp .= "<td style='font-size:10px;'>" . $value_exp->traffic  . "</td>";
                            $html_view_exp .= "</tr>";

                            if (isset($dateNumber[$date_human])) $dateNumber[$date_human] = $dateNumber[$date_human] + 1;
                            else $dateNumber[$date_human] = 1;

                            if (isset($dateTraffic[$date_human])) array_push($dateTraffic[$date_human], $value_exp->traffic);
                            else $dateTraffic[$date_human] = array($value_exp->traffic);


                        }
                        $html_view_exp .= "</table>";
                    } else
                    {
                      $html_view_exp .= "";
                    }
                    echo "
                     <tr class='alternate'>
                      <td class='image'>" .  $video . "</td>
                      <td>" .  $html_view_exp . "</td>
                      <td>" . $value->viewers . "</td>
                      <td>" .  $value->max_viewers . "</td>
                     </tr>";

				
				}
				echo "</table><div class='clear'></div></div>";
				
				
				echo "<div id='view_graph' class='view'>";
				$dateRange = getDateRange($from_dmy, $to_dmy);
				$count_date = count($dateRange);
				$count_single= 0;
				$traffic_single = 0;
				echo "<div class='cols'>";
				if (count($dateNumber)>0) {
					$number_view_max = max($dateNumber);
					$single_percent = (100/$number_view_max);
				}
				else
					$single_percent = 0;
				$single_taffic_media = array();
				foreach ($dateTraffic as $dateFormat => $traffic_number){
					$single_taffic_media[$dateFormat] = round(array_sum($dateTraffic[$dateFormat]) / count($dateTraffic[$dateFormat]),2);
				}
				if (count($single_taffic_media)>0) {
					$traffic_view_max = max($single_taffic_media);
					$single_traffic_percent = (100/$traffic_view_max);
				}
				else
					$traffic_view_max = 0;
				echo "<div class='col'><div class='date'>" . __("Date","wimtvpro") . "</div><div class='title'>" . __("Total viewers","wimtvpro") . "</div><div class='title'>" . __("Average Traffic","wimtvpro") . "</div></div>";
				for ($i=0;$i<$count_date;$i++){
				    if (isset($dateNumber[$dateRange[$i]])) $count_single = $single_percent * $dateNumber[$dateRange[$i]];
				    if (isset($single_taffic_media[$dateRange[$i]])) $traffic_single = $single_traffic_percent * $single_taffic_media[$dateRange[$i]];		    
				    
				 	echo "<div class='col' >
							<div class='date'>" . $dateRange[$i] . "</div>
							<div class='countview'><div class='bar' style='width:" . $count_single . "%'>";
					if ($dateNumber[$dateRange[$i]]>1) echo $dateNumber[$dateRange[$i]] . " " . __("viewers");
					if ($dateNumber[$dateRange[$i]]==1) echo $dateNumber[$dateRange[$i]] . "  " . __("viewer");
					echo "</div></div>
							<div class='countview'><div class='barTraffic' style='width:" . $traffic_single . "%'>";
					if ($single_taffic_media[$dateRange[$i]]>0) echo $single_taffic_media[$dateRange[$i]] . " MB";
					echo "</div></div>
							</div>";
					$count_single = 0;
					$traffic_single = 0;
				}
				
				echo "</div>";
				//print_r($dateRange);	
				echo "<div class='clear'></div></div><div class='clear'></div></div>";
			
		   		
		   	}
		   	echo "</div>";
		   	
		}
			
}



?>
