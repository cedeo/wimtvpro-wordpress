<?php
include("../../../../wp-load.php");
$userpeer = get_option("wp_userWimtv");
$timezone = isset($_POST['timezone']) ? $_POST['timezone'] : "";
$type = $_POST['type'];
$id =  $_POST['id'];
$onlyActive = $_POST['onlyActive'];

   
  $url_live_select = get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts?timezone=" . $timezone;
  //$url_live_select = "http://peer.wim.tv:8080/" . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts?timezone=" . $timezone;
  
  if ($onlyActive)  $url_live_select .= "&active=true";

  $credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");
  $ch_select = curl_init();
  curl_setopt($ch_select, CURLOPT_URL, $url_live_select);
  curl_setopt($ch_select, CURLOPT_VERBOSE, 0);
  curl_setopt($ch_select, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch_select, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  curl_setopt($ch_select, CURLOPT_USERPWD, $credential);
  curl_setopt($ch_select, CURLOPT_SSL_VERIFYPEER, FALSE);
  $json  =curl_exec($ch_select);
  $arrayjson_live = json_decode($json);
  //var_dump ($json);
  //$arrayST["showtimeIdentifier"] = $arrayjson_live->{"showtimeIdentifier"};
  $count = -1;
  $output = "";
  if ($arrayjson_live ){
   foreach ($arrayjson_live->{"hosts"} as $key => $value) {
    $count ++;  
    $name = $value -> name;
    if (isset($value -> url))
      $url =  $value -> url;
    else
      $url = "";
    $day =  $value -> eventDate;
    $payment_mode =  $value -> paymentMode;
    if ($payment_mode=="FREEOFCHARGE") $payment_mode="Free";
    else {
      $payment_mode=  $value->pricePerView . " &euro;";
    }
    if ( $value -> durationUnit=="Minute") {
   		$tempo = $value->duration;
		$ore = floor($tempo / 60);
		$minuti = $tempo % 60;
		$durata = $ore . " h ";
		if ($minuti<10)
		  $durata .= "0";
		$durata .= $minuti . " min";
	}
	else
		 $durata =  $value->duration . " " . $value -> durationUnit;	
    
    $identifier = $value -> identifier;
    $url_live_embedded = get_option("wp_basePathWimtv") . "liveStream/" . $userpeer . "/" . $userpeer . "/hosts/" . $identifier . "/embed?timezone=" . $timezone;
    $ch_embedded = curl_init();

    //read iframe
    $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,";
    curl_setopt($ch_embedded, CURLOPT_URL, $url_live_embedded);
    curl_setopt($ch_embedded, CURLOPT_VERBOSE, 0);
    curl_setopt($ch_embedded, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch_embedded, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_embedded, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_embedded, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_embedded, CURLOPT_SSL_VERIFYPEER, FALSE);
    $embedded_iframe = curl_exec($ch_embedded);
    
    $ch_details= curl_init();

    //read iframe

    curl_setopt($ch_details, CURLOPT_URL, $url_live_embedded);
    curl_setopt($ch_details, CURLOPT_VERBOSE, 0);
    curl_setopt($ch_details, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch_details, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_details, CURLOPT_USERPWD, $credential);
    curl_setopt($ch_details, CURLOPT_SSL_VERIFYPEER, FALSE);
    $details_live = curl_exec($ch_details);
    $livedate = json_decode($details_live);
	$data = $livedate->eventDate;
	if (intval($livedate->eventMinute)<10) $livedate->eventMinute = "0" .  $livedate->eventMinute;
	$oraMin = $livedate->eventHour . ":" . $livedate->eventMinute;
	$timeToStart= $livedate->timeToStart;
	$timeLeft = $livedate->timeLeft;

   // $urlPeer = "http://peer.wim.tv:8080/wimtv-webapp/rest";
    //$embedded_code = htmlentities(curl_exec($ch_embedded));
    //$embedded_iframe = '<iframe id="com-wimlabs-player" name="com-wimlabs-player" src="' . $urlPeer . '/liveStreamEmbed/' . $identifier . '/player?width=692&height=440" style="min-width: 692px; min-height: 440px;"></iframe>';
    
    $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();">' . $embedded_iframe . '</textarea>'; 
    if ($type=="table") {
      
      //Check Live is now
      $dataNow = date("d/m/Y"); 
      $arrayData = explode ("/",$data);
	  $arrayOra = explode (":",$oraMin);
     
      $timeStampInizio =  mktime($livedate->eventHour,$livedate->eventMinute,0,$arrayData[1],$arrayData[0],$arrayData[2]);
      
      $secondiDurata = 60 * $durata;
      $ora= date("H:i:s", $secondiDurata);
      $arrayDurata = explode (":",$ora);
      $startSeconds = isset($arrayOra[2]) ? $arrayOra[2] : 0;
      $timeStampFine =  mktime($arrayOra[0]+$arrayDurata[0],$arrayOra[1]+$arrayDurata[1],$startSeconds+$arrayDurata[2],$arrayData[1],$arrayData[0],$arrayData[2]);

      $timeStampNow =  mktime(date("H"),date("i"),date("s"));
		
      $liveIsNow = false;
      if ($dataNow == $data){
      	//if (($timeStampNow>=$timeStampInizio) && ($timeStampNow<$timeStampFine )) {
			 $liveIsNow = true;
		//}
      }
     
      $output .="<tr>
      <td>" . $name . "</td>";
	  
	  if ($identifier==get_option("wp_liveNow"))  $file= "live_rec.gif";
	  else $file= "webcam.png";
	  
      if ($liveIsNow)  $output .= "<td><a  target='page_newTab' href='" .  plugin_dir_url(dirname(__FILE__))  . "pages/live_webproducer.php?id=" . $identifier . "' class='clickWebProducer' id='" . $identifier . "'><img  onClick='clickImg(this)' src='" . plugins_url('images/' . $file, dirname(__FILE__)) . "' /></a></td>";
      else $output .="<td></td>";
      
      $output .=  "<td>" . $payment_mode . "</td>
      <td>" . $url . "</td>
      <td>"  . $data . " " . $oraMin . "<br/>" . $durata . "</td>
      <td>" . $embedded_code . "</td>
      <td> 
      <a href='?page=WimVideoPro_WimLive&namefunction=modifyLive&id=" . $identifier . "' alt='" . __("Remove") . "'   title='" . __("Modify","wimtvpro") . "'><img src='" . plugins_url('images/mod.png',dirname(__FILE__)) . "'  alt='" . __("Modify","wimtvpro") . "'></a>   
       <a href='?page=WimVideoPro_WimLive&namefunction=deleteLive&id=" . $identifier . "' title='" . __("Remove") . "'><img src='" . plugins_url('images/remove.png',dirname(__FILE__)) . "' alt='" . __("Remove") . "'></a></td>
      </tr>";
    }
    elseif ($type=="list") {
      if ($count==0) $output .= "";
      elseif ($count>0) $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $data . " " . $oraMin . " - " . $durata . "</li>";
      else $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $data . " " . $oraMin   . " - " . $durata . "</li>";
    }
    else {
      if ($count==0) {
        $name = "<b>" . $name . "</b>";
        $day =  __("Begins to ","wimtvpro") . $day;
        $output = $name . "<br/>";
        $output .= $data . " " . $oraMin  . "<br/>" . $durata . "<br/>";
        $output .= $embedded_iframe;
      }
    }
   }
  }
  if ($count<0)
    $output = __("There are no live events","wimtvpro");

echo $output;
die();

?>


