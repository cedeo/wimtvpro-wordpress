<?php

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
			$arrayPlay = $wpdb->get_results("SELECT * FROM {$table_name} WHERE contentidentifier='" . $value->contentId . "'");
			$thumbs = $arrayPlay[0]->urlThumbs;
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