<?php

/*
 * Written By Netsense Srl 2016
 */



include("../../../../../../wp-load.php");

//include_once("../api/api.php");
$userpeer = get_option("wp_userWimtv");

//$type = $_POST['type'];
$type = "table";
//$id = $_POST['id'];
//$onlyActive = $_POST['onlyActive'];
header('Content-type: text/html');
$timezone = $_POST['timezone_'];


$response_user = apiGetProfile();
$array_user = json_decode($response_user);
$pass_live_set = (isset($array_user->features->livePassword) || $array_user->features->livePassword != "") ? true : false;


$params = array(
    "pageSize" => "20",
    "pageIndex" => "0"
);
$response = apiSearchLiveChannels($params, $timezone);
$arrayjson_channel = json_decode($response);


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

        $name = $value->name;

        $params = array(
            "channelId" => $channelId,
            "pageSize" => "20",
            "pageIndex" => "0"
        );


        $response = apiSearchLiveEvents($params, $timezone);

        $array_live = json_decode($response);


        $image = '<img src="' . '/wp-content/plugins/wimtvpro/images/empty.jpg"' . '"  title="' . $name . '" class="wimtv-thumbnail" />';

        if (isset($thumbnailId)) {
            $image = '<img src="' . __('API_URL', 'wimtvpro') . 'asset/thumbnail/' . $thumbnailId . '"  title="' . $name . '" class="wimtv-thumbnail" />';
        }

        $height = get_option("wp_heightPreview");
        $width = get_option("wp_widthPreview");
        $embedded_code_text = '[wimlive id="' . $channelId . '" width="' . $width . '" height="' . $height . '" timezone="' . $timezone . '" ]';
        $output .= '<div class="panel">
			<div class="panel-heading">
				<h3 class="panel-title ">
                                <div class="row" style="border-style: dotted; border-width: 1px;">
					
                                        
                                        <div class="col-sm-2" style="height:100%;margin-top:30px;margin-bottom: 10px;"> 
                                        <a data-toggle="collapse" data-parent="#accordion" href="#panel-feeds-settings' . $count . '">'
                . __("Channel events", "wimtvpro") . '</a> 
                
                     </div>
                                        <div class="col-sm-6" style="height:100%;">' .
                '</br><span>' . $name . '</span></br>' .
                '<span>' . __("Streaming Base URL", "wimtvpro") . ': ' . $streamingBaseUrl . '</span></br>' .
                '<span>' . __("Streaming URL", "wimtvpro") . ': ' . $streamPath . '</span>' .
                ' </div>';
        $output .= '<div class="col-sm-2">';
        $output .= '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width:100%;margin-top:30px;">' . $embedded_code_text . '</textarea>';
        $output .= '</div>';
        $output .= '  <div class="col-xs-1">' .
                "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=modifyChannel&id=" . $channelId . "' title='" . __("Modify") . "' ><img style='margin-top:30px;' src='" . get_option('wp_wimtvPluginPath') . "images/mod.png" . "' alt='" . __("Modify") . "'/>"
                . '</a> </div>';
        $output .= '  <div class="col-xs-1">' .
                "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=deleteChannel&id=" . $channelId . "' title='" . __("Remove") . "'><img style='margin-top:30px;' src='" . get_option('wp_wimtvPluginPath') . "images/remove.png" . "' alt='" . __("Remove") . "'/>"
                . ' </a></div>';



        $output .= '</div>';

        $output .= '
                                        
                                        </div>
	                	</h3>
			</div>';

        $output .= '<div id="panel-feeds-settings' . $count . '" class="panel-collapse collapse">
                            <div class="panel-body">';


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
    }
    $output .= '</td> </tr>';
}
if ($count < 0) {
    $output = __("There are no live events", "wimtvpro");
}

echo $output;

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


            $height = get_option("wp_heightPreview");
            $width = get_option("wp_widthPreview");
            $embedded_code_text = "[wimlive id='$identifier' width=$width height=$height]";







            if ($type == "table") {
                //Check Live is now

                $liveIsNow = $value->onAir;
                $inizio = $value->eventDate->date . ' | ' . $value->eventDate->time;
                $fine = $value->endDate->date . ' | ' . $value->endDate->time;
                $output .="<tr><td>" . $name . "</td>";

                if ($liveIsNow == true)
                    $file = "live_rec.gif";
                else
                    $file = "webcam.png";

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

                $output .= "<td> ";

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