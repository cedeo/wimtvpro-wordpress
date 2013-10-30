<?php

include_once("api/wimtv_api.php");

header('Content-type: application/json');

initApi(get_option("wp_basePathWimtv"), get_option("wp_userwimtv"), get_option("wp_passwimtv"));


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



?>
