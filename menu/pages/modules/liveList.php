<?php

include("../../../../../../wp-load.php");
//include_once("../api/api.php");
$userpeer = get_option("wp_userWimtv");
$timezone = isset($_POST['timezone_']) ? $_POST['timezone_'] : "";
$cliTimezoneName = isset($_POST['cliTimezoneName']) ? $_POST['cliTimezoneName'] : "";
$channelId = isset($_POST['array']) ? $_POST['array'] : "";
//var_dump($cliTimezoneName);die;
$type = $_POST['type'];
$id = $_POST['id'];
$onlyActive = $_POST['onlyActive'];
header('Content-type: text/html');

//$json = apiGetLiveEvents($timezone, $onlyActive);
//$arrayjson_live = json_decode($json);

$params = array(
    "channelId" => $channelId,
    "pageSize" => "20",
    "pageIndex" => "0"
);
$response = apiSearchLiveEvents($params);

$arrayjson_live = json_decode($response);

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
        if ($payment_mode == "FREEOFCHARGE")
            $payment_mode = "Free";
        else {
            $payment_mode = $value->pricePerView . " &euro;";
        }
        if ($value->durationUnit == "Minute") {
            $tempo = $value->duration;
            $ore = floor($tempo / 60);
            $minuti = $tempo % 60;
            $durata = $ore . " h ";
            if ($minuti < 10)
                $durata .= "0";
            $durata .= $minuti . " min";
        }
        else {
            $durata = $value->duration . " " . $value->durationUnit;
        }

        $identifier = $value->identifier;

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
        $embedded_code_text = "[wimlive id='$identifier' zone='$timezone' width=$width height=$height]";

        $details_live = apiGetLive($identifier, $timezone);
        $livedate = json_decode($details_live);

        $data = $livedate->eventDateMillisec;
        $remote_timezoneOffset = intval($livedate->timezoneOffset) / 1000;
        $remote_timestamp = floor($data / 1000);

        $start = new DateTime("@$remote_timestamp");

        // NS: We use the POSTed value "cliTimeOffset" to calculate local
        // client timezone taking into account whether the client is in daylight 
        // savings or not. We do that in js by using a custom function (isDaylightSavings())
        // (see wimtvpro.js)
//        $cliTimeOffset = $_POST['cliTimeOffset'];
//        $timezoneName = timezone_name_from_abbr("", $cliTimeOffset, 0);
//        $timezoneName = get_offset_to_name($cliTimeOffset);
//        if ($timezoneName != false) {
        $cliTimezone = new DateTimeZone($cliTimezoneName);
        $start->setTimezone($cliTimezone);
//        }


        $oraMin = $start->format('H') . ":" . $start->format('i');
        $timeToStart = $livedate->timeToStart;
        $timeLeft = $livedate->timeLeft;

        //$urlPeer = "http://peer.wim.tv:8080/wimtv-webapp/rest";
        //$embedded_code = htmlentities(curl_exec($ch_embedded));
        //$embedded_code_text = '<iframe id="com-wimlabs-player" name="com-wimlabs-player" src="' . $urlPeer . '/liveStreamEmbed/' . $identifier . '/player?width=692&height=440" style="min-width: 692px; min-height: 440px;"></iframe>';

        $embedded_code = '<textarea readonly="readonly" onclick="this.focus(); this.select();" style="width: 100%">' . $embedded_code_text . '</textarea>';
        if ($type == "table") {
            //Check Live is now

            $liveIsNow = false;
            if (intval($timeToStart) < 0 && intval($timeLeft) > 0) {
                $liveIsNow = true;
            }

            $output .="<tr><td>" . $name . "</td>";

            if ($identifier == get_option("wp_liveNow"))
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
            $output .= "<td>" . $url . "</td>";
            $output .= "<td>" . $start->format('d/m/Y H:i') . "<br/>" . $durata . "</td>";
            $output .= "<td>" . $embedded_code . "</td>";
            $output .= "<td> ";

//            $output .="<a href='?page=WimLive&namefunction=modifyLive&id=" . $identifier . "&timezone=" . $timezoneOffset . "' alt='" . __("Modify")
            $output .="<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=modifyLive&id=" . $identifier . "&timezone=" . $remote_timezoneOffset . "&cliTimezoneName=" . $cliTimezoneName . "' alt='" . __("Modify")
                    . "'   title='" . __("Modify", "wimtvpro") . "'><img src='" . get_option('wp_wimtvPluginPath') . "images/mod.png"
                    . "'  alt='" . __("Modify", "wimtvpro") . "'></a>";
            $output .="</td>";
            $output .= "<td> ";
            $output .= "<a href='?page=" . __("WIMLIVE_urlLink", "wimtvpro") . "&namefunction=deleteLive&id=" . $identifier . "' title='" . __("Remove") . "'><img src='" . get_option('wp_wimtvPluginPath') . "images/remove.png" . "' alt='" . __("Remove") . "'></a>";

            $output .="</td>

      </tr>";
        } elseif ($type == "list") {
            if ($count == 0)
                $output .= "";
            elseif ($count > 0)
                $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $start->format('d/m/Y H:i') . " - " . $durata . "</li>";
            else
                $output .="<li><b>" . $name . "</b> " . $payment_mode . " - " . $start->format('d/m/Y H:i') . " - " . $durata . "</li>";
        }
        else {
            if ($count == 0) {
                $name = "<b>" . $name . "</b>";
                $day = __("Begins to ", "wimtvpro") . $day;
                $output = $name . "<br/>";
                $output .= $data . " " . $oraMin . "<br/>" . $durata . "<br/>";
                $output .= $embedded_code_text;
            }
        }
    }
}
if ($count < 0) {
    $output = __("There are no live events", "wimtvpro");
}

echo $output;

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
