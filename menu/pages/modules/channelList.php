<?php

/*
 * Written By Netsense Srl 2016
 */



include("../../../../../../wp-load.php");

//include_once("../api/api.php");
$userpeer = get_option("wp_userWimtv");
//$timezone = isset($_POST['timezone_']) ? $_POST['timezone_'] : "";
//$cliTimezoneName = isset($_POST['cliTimezoneName']) ? $_POST['cliTimezoneName'] : "";
//var_dump($cliTimezoneName);die;
$type = $_POST['type'];
$id = $_POST['id'];
$onlyActive = $_POST['onlyActive'];
header('Content-type: text/html');
$timezone = $_POST['timezone_'];


$response_user = apiGetProfile();
$array_user = json_decode($response_user);
$pass_live_set = (isset($array_user->features->livePassword) || $array_user->features->livePassword != "") ? true : false;


$params = array(
    "pageSize" => "20",
    "pageIndex" => "0"
);
$response = apiSearchLiveChannels($params,$timezone);
$arrayjson_channel = json_decode($response);

//$json = apiGetLiveEvents($timezone, $onlyActive);
//$arrayjson_live = json_decode($json);
//var_dump($arrayjson_live);die;
$count = -1;
$output = "";
if ($arrayjson_channel) {
    $output .= '<tr> <td>';
    $output .= "<div class='panel-group' id='accordion'>";
    foreach ($arrayjson_channel->{"items"} as $key => $value) {

        $count++;
        $name = $value->name;




        $public = $value->public;
        $channelId = $value->channelId;
        $streamingBaseUrl = $value->streamingBaseUrl;
        $streamPath = $value->streamPath;
        $description = $value->description;
        $name = $value->name;
        $thumbnailId = $value->thumbnailId;
        $params = array(
            "channelId" => $channelId,
            "pageSize" => "20",
            "pageIndex" => "0"
        );


        $response = apiSearchLiveEvents($params,$timezone);

        $array_live = json_decode($response);

//        $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width: 100%">' . $embedded_code_text . '</textarea>';


        $image = '<img src="' . '/wp-content/plugins/wimtvpro/images/empty.jpg"' . '"  title="' . $name . '" class="wimtv-thumbnail" />';

        if (isset($thumbnailId)) {
            $image = '<img src="' . __('API_URL', 'wimtvpro') . 'asset/thumbnail/' . $thumbnailId . '"  title="' . $name . '" class="wimtv-thumbnail" />';
        }

         $height = get_option("wp_heightPreview");
            $width = get_option("wp_widthPreview");
      $embedded_code_text = '[wimlive id="'.$channelId.'" width="'.$width.'" height="' .$height.'" timezone="'.$timezone.'" ]';
        $output .= '<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
                                <div class="row" style="border-style: dotted; border-width: 1px;">
					
                                        
                                        <div class="col-sm-2" style="height:100%;margin-top:30px;margin-bottom: 10px;"> 
                                        <a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings' . $count . '">'
                 . __("Channel events","wimtvpro") . '</a> 
                
                     </div>
                                        <div class="col-sm-6" style="height:100%;">' .
               
                '</br><span>' . $name . '</span></br>' .
                '<span>' . __("Streaming Base URL","wimtvpro").': ' .  $streamingBaseUrl .'</span></br>' .
                '<span>' . __("Streaming URL","wimtvpro").': ' .    $streamPath . '</span>' .
                
                ' </div>';
              $output .= '<div class="col-sm-2">';
               $output .=  '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width:100%;margin-top:30px;">' . $embedded_code_text . '</textarea>';
               $output .= '</div>';
              $output .=  '  <div class="col-xs-1">' .
               
                "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=modifyChannel&id=" . $channelId . "' title='" . __("Modify") . "' ><img style='margin-top:30px;' src='" . get_option('wp_wimtvPluginPath') . "images/mod.png" . "' alt='" . __("Modify") . "'/>"
                . '</a> </div>';
               $output .=  '  <div class="col-xs-1">' .
                "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=deleteChannel&id=" . $channelId . "' title='" . __("Remove") . "'><img style='margin-top:30px;' src='" . get_option('wp_wimtvPluginPath') . "images/remove.png" . "' alt='" . __("Remove") . "'/>" 
               
                . ' </a></div>';
               

                  
              $output .= '</div>';
                      
                           $output .=  '
                                        
                                        </div>
	                	</h3>
			</div>';
                  
			$output .= '<div id="panel-feeds-settings' . $count . '" class="panel-collapse collapse">
                            <div class="panel-body">';
        
//                                 <th> echo __("Embed Code", "wimtvpro") </th>  
// $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width: 100%">' . $embedded_code_text . '</textarea>';

        if ($array_live) {

            $output.= '<table id="table" class="wp-list-table widefat fixed pages">
            <thead>
                <tr>
                    <th><?php echo __("Title") ?></th>
                    <th>Live Now</th>
                    <th>Pay-Per-View</th>
                    <th>Inizio </th>
                    <th>Fine </th>
         
                    <th><?php echo __("Edit") ?></th>
                    <th><?php echo __("Remove") ?></th>
                </tr>
            </thead>
            <tbody>';
            $output.= getListLiveEvents($array_live);

            $output.= ' </tbody>
        </table>
          ';
        }
        $output .='   </div>';


        $wp_page_qs = __('SETTINGS_urlLink', "wimtvpro");
        if ($pass_live_set) {
            $output .= "<a class='add-new-h2' href='" . "?page=liveevent&namefunction=addLive&channelId=" . $channelId . "' >Add Event</a>";
        } else {
            $output .= "<a  href='admin.php?page=" . $wp_page_qs . "&update=1'>" . __("Set live password") . "</a>";
        }
        $output .='</div>';

//            $output .= '</div>';
    }
    $output .= '</td> </tr>';
}
if ($count < 0) {
    $output = __("There are no live events", "wimtvpro");
}

echo $output;

/* Takes a GMT offset (in hours) and returns a timezone name */

//function get_offset_to_name($offset) {
////    $offset *= 3600; // convert hour offset to seconds
//    $abbrarray = timezone_abbreviations_list();
//    foreach ($abbrarray as $abbr) {
//        foreach ($abbr as $city) {
//            if ($city['offset'] == $offset) {
//                return $city['timezone_id'];
//            }
//        }
//    }
//
//    return FALSE;
//}

function getListLiveEvents($arrayjson_live) {

    $userpeer = get_option("wp_userWimtv");
    $type = 'table';


    $count = -1;
    $output = "";

    if ($arrayjson_live->items) {
        foreach ($arrayjson_live->{"items"} as $key => $value) {
            $count++;
            $name = $value->name;
            if (isset($value->url))
                $url = $value->url;
            else
                $url = "";
            $day = $value->eventDate;
            $payment_mode = $value->paymentMode;

            if ($payment_mode != "FREE") {
                $payment_mode = $value->pricePerView . " &euro;";
            }


            $identifier = $value->eventId;
            $channelId = $value->channel->channelId;
//        $skin = "";
//        if (get_option('wp_nameSkin') != "") {
//            $uploads_info = wp_upload_dir();
//            $directory = $uploads_info["baseurl"] . "/skinWim";
//
//            $nomeFilexml = wimtvpro_searchFile($uploads_info["basedir"] . "/skinWim/" . get_option('wp_nameSkin') . "/wimtv/", "xml");
//            $skin = "&skin=" . $directory . "/" . get_option('wp_nameSkin') . "/wimtv/" . $nomeFilexml;
//        }
//
//        $params = "timezone=" . $timezone;
//        if ($skin != "") {
//            $params.="&amp;skin=" . $skin;
//        }

            $insecureMode = "&insecureMode=on";
            $skin = "";
            $logo = "";

            $params.=$insecureMode;
            // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
            $skinData = wimtvpro_get_skin_data();
            if ($skinData['styleUrl'] != "") {
                $skin = "&skin=" . htmlentities($skinData['styleUrl']);
                $params.= $skin;
            }

            if ($skinData['logoUrl'] != "") {
                $logo = "&logo=" . htmlentities($skinData['logoUrl']);
                $params.= $logo;
            }

//        if ($id == "all") {
//            $embedded_code_text = "[wimlive id='$identifier' zone='$timezone']";
//        } else {
//            $embedded_code_text = apiGetLiveIframe($identifier, $params);
//        }
            $height = get_option("wp_heightPreview");
            $width = get_option("wp_widthPreview");
            $embedded_code_text = "[wimlive id='$identifier' width=$width height=$height]";






            // NS: We use the POSTed value "cliTimeOffset" to calculate local
            // client timezone taking into account whether the client is in daylight 
            // savings or not. We do that in js by using a custom function (isDaylightSavings())
            // (see wimtvpro.js)
//        $cliTimeOffset = $_POST['cliTimeOffset'];
//        $timezoneName = timezone_name_from_abbr("", $cliTimeOffset, 0);
//        $timezoneName = get_offset_to_name($cliTimeOffset);
//        if ($timezoneName != false) {
//        $cliTimezone = new DateTimeZone($cliTimezoneName);
//        $start->setTimezone($cliTimezone);
//        }
            //$urlPeer = "http://peer.wim.tv:8080/wimtv-webapp/rest";
            //$embedded_code = htmlentities(curl_exec($ch_embedded));
            //$embedded_code_text = '<iframe id="com-wimlabs-player" name="com-wimlabs-player" src="' . $urlPeer . '/liveStreamEmbed/' . $identifier . '/player?width=692&height=440" style="min-width: 692px; min-height: 440px;"></iframe>';

//            $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width: 100%">' . $embedded_code_text . '</textarea>';

            if ($type == "table") {
                //Check Live is now

                $liveIsNow = $value->onAir;
                $inizio = $value->eventDate->date . ' | ' . $value->eventDate->time;
                $fine = $value->endDate->date . ' | ' . $value->endDate->time;
                $output .="<tr><td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .="<td>" . $name . "</td>";
//                $output .= '</tr>';
//                if ($identifier == get_option("wp_liveNow"))
                if ($liveIsNow == true)
                    $file = "live_rec.gif";
                else
                    $file = "webcam.png";
//
                if ($liveIsNow) {
                    $output .= "<td><a  target='page_newTab' href='" . get_option('wp_wimtvPluginPath')
                            . "embedded/live_webproducer.php?id=" . $identifier . "' class='clickWebProducer' id='"
                            . $identifier . "'><img  onClick='clickImg(this)' src='"
                            . get_option('wp_wimtvPluginPath') . "images/" . $file . "' /></a></td>";
                } else {
                    $output .="<td></td>";
                }
                $output .= "<td>" . $payment_mode . "</td>";
                $output .= "<td>" . $inizio . "</td>";
                $output .= "<td>" . $fine . "</td>";
//                $output .= "<td>" . $embedded_code . "</td>";
                $output .= "<td> ";

//            $output .="<a href='?page=WimLive&namefunction=modifyLive&id=" . $identifier . "&timezone=" . $timezoneOffset . "' alt='" . __("Modify")
                $output .="<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=modifyLive&id=" . $identifier . "&channelId=" . $channelId . "' alt='" . __("Modify")
                        . "'   title='" . __("Modify", "wimtvpro") . "'><img src='" . get_option('wp_wimtvPluginPath') . "images/mod.png"
                        . "'  alt='" . __("Modify", "wimtvpro") . "'></a>";
                $output .="</td>";
                $output .= "<td> ";
                $output .= "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=deleteLive&id=" . $identifier . "' title='" . __("Remove") . "'><img src='" . get_option('wp_wimtvPluginPath') . "images/remove.png" . "' alt='" . __("Remove") . "'></a>";

                $output .="</td>
      </tr>";
            }
        }
    }

    if ($count < 0) {
        $output = __("There are no live events", "wimtvpro");
    }

    return $output;
}

/* Takes a GMT offset (in hours) and returns a timezone name */

function get_offset_to_name($offset) {
//    $offset *= 3600; // convert hour offset to seconds
    $abbrarray = timezone_abbreviations_list();
    foreach ($abbrarray as $abbr) {
        foreach ($abbr as $city) {
            if ($city['offset'] == $offset) {
                return $city['timezone_id'];
            }
        }
    }

    return FALSE;
}

?>