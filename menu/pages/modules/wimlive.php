<?php

/**
 * Written by walter at 28/10/13
 * Updated By Netsense SRL 2016
 */
function wimtvpro_getChannels() {
    global $user, $wpdb, $wp_query;

    $params = array(
        "pageSize" => "20",
        "pageIndex" => "0"
    );
    $response = apiSearchLiveChannels($params);
    return $response;
}

function wimtvpro_elencoChannel($type) {

    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';


    echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '

    var timezone = -(new Date().getTimezoneOffset())*60*1000;
    ';
    // NS: We POST the param "cliTimezoneName" to let CMS server know the client timezone.
    echo '
//        var cliTimeOffset=new Date().getTimezoneOffset()*(-60);
//        if (isDaylightSavings()){
//            cliTimeOffset -= 3600;
//        }
// data: "type=' . $type . '&timezone =" + timezone  + "&id=' . $identifier . '&onlyActive=' . $onlyActive . '&cliTimeOffset="+ cliTimeOffset + "&cliTimezoneName="+ cliTimezoneName,        
  
	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "channelList.php",
			type: "POST",
			dataType: "html",
			async: false,
                        data: "timezone =" + timezone,
			success: function(response) {
';

    if ($type == "table") {

        echo 'jQuery("#tableLive tbody").html(response)';
    }

    echo '
			},
	});
});
</script>
';
}

/**
 * Ritorna la tabella degli eventi live, prendendola da liveList.php
 */
function wimtvpro_elencoLive($type, $identifier, $onlyActive = true, $array_live) {

    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';


    echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '
    var cliTimezoneName = jstz.determine().name();    
    var timezone = -(new Date().getTimezoneOffset())*60*1000;
    console.log(timezone);
	//window.location.assign(window.location + "&timezone="+timezone);';
    // NS: We POST the param "cliTimezoneName" to let CMS server know the client timezone.
    echo '
//        var cliTimeOffset=new Date().getTimezoneOffset()*(-60);
//        if (isDaylightSavings()){
//            cliTimeOffset -= 3600;
//        }
// data: "type=' . $type . '&timezone =" + timezone  + "&id=' . $identifier . '&onlyActive=' . $onlyActive . '&cliTimeOffset="+ cliTimeOffset + "&cliTimezoneName="+ cliTimezoneName,        
    
	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "liveList.php",
			type: "POST",
			dataType: "html",
			async: false,
			data: "type=' . $type . '&timezone =" + timezone  + "&id=' . $identifier . '&array=' . $array_live . '&onlyActive=' . $onlyActive . '&cliTimezoneName="+ cliTimezoneName,
			success: function(response) {
';

    if ($type == "table") {

        echo 'jQuery("#tableLive tbody").html(response)';
    } else {

        echo 'jQuery(".live_' . $type . '").html(response)';
    }

    echo '
			},
	});
});
</script>
';
}

/**
 * Crea o modifica un live esistente. I parametri devono essere passati attraverso POST.
 */
function wimtvpro_savelive($function) {

//    $time = strtotime('10/16/2003');
//
//$newformat = date('Y-m-d',$time);
//$time2 = $_POST['eventDate']['time'];
//$time2_parts = explode(":", $time2);
//
//$date = DateTime::createFromFormat("m/d/Y",$_POST['eventDate']['date']);
//$newformat2 = date('Y-m-d',strtotime($date->date));
//var_dump($_POST['eventDate']['date']);
//$t = new DateTime('16/10/2003');
//$date->setTime($time2_parts[0],$time2_parts[1]);
//setlocale(LC_TIME, $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
//$DATAAA = strftime($date);
//var_dump($DATAAA,$date,$_SERVER["HTTP_ACCEPT_LANGUAGE"]);die;
//var_dump($date->toLo);
//var_dump($date->format('H'));
//var_dump($date->format('i'));
//var_dump($date->format('s'));
//die;

    if (isset($_POST["wimtvpro_live"])) {
        //Modify new event live
        $error = 0;
        //Check fields required

        if (strlen(trim($_POST['name'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set a title.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }
        if (strlen(trim($_POST['payPerView'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set a price for your event (or free of charge).", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }

        if (strlen(trim($_POST['eventDate']['date'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set a day for your event.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }
        if (strlen(trim($_POST['endDate']['date'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set a day for your event.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }
        if (strlen(trim($_POST['eventDate']['time'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set an hour for your event.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }

        if (strlen(trim($_POST['endDate']['time'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set an hour for your event.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }


        if (!isset($_POST['publicEvent'])) {
            echo '<div class="error"><p><strong>';
            _e("You must check if your event is public or private.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }

        if ($error == 0) {
            $name = $_POST['name'];
            $payperview = $_POST['payPerView'];
//            if ($payperview == "0") {
//                $typemode = "FREEOFCHARGE";
//            } else {
//                $paymentCode = apiGetUUID();
//                $typemode = "PAYPERVIEW&pricePerView=" . $payperview . "&ccy=EUR&paymentCode=" . $paymentCode;
//            }


            if ($_POST['eventDate']['date'] != "") {
                $eventDate_date = $_POST['eventDate']['date'];
            }
            if ($_POST['eventDate']['time'] != "") {
                $eventDate_time = $_POST['eventDate']['time'];
            }

            if ($_POST['endDate']['date'] != "") {
                $endDate_date = $_POST['endDate']['date'];
            }

            if ($_POST['endDate']['time'] != "") {
                $endDate_time = $_POST['endDate']['time'];
            }


            if ($_POST['publicEvent'] != "") {
                $public = $_POST['publicEvent'];
            }

            if ($_POST['recordEvent'] != "") {
                $record = $_POST['recordEvent'];
            }

            if ($_POST['channelId'] != "") {
                $channelId = $_POST['channelId'];
            }

            if ($_POST['eventId'] != "") {
                $eventId = $_POST['eventId'];
            }

            if ($_POST['timezone'] != "") {
                $timezone = $_POST['timezone'];
            }




            $pricePerView = "0";

            if ($payperview == "0") {
                $payementemode = "FREE";
                $pricePerView = "0";
            } else {
                $payementemode = "PAY_PER_VIEW";
                $pricePerView = $payperview;
            }

            // GET A PAYMENT CODE FROM SERVER
//            $paymentCodeResponse = apiGetUUID();
//            $paymentCode = isset($paymentCodeResponse->body) ? $paymentCodeResponse->body : "";
            $eventDate_time_parts = explode(":", $eventDate_time);

          $eventDate = array(
                "date" => $eventDate_date,
                "time" => $eventDate_time_parts[0] . ":" . $eventDate_time_parts[1] . ":00"
            );


            $endDate_time_parts = explode(":", $endDate_time);
            $endDate = array(
                "date" => $endDate_date,
                "time" => $endDate_time_parts[0] . ":" . $endDate_time_parts[1] . ":00"
            );

            $parameters = array(
                "name" => $name,
//                "description" => "Description 1",
//                "tags" => [ "tag1", "tag2"],
                "eventDate" => $eventDate,
                "endDate" => $endDate,
                "publicEvent" => $public,
                "recordEvent" => $record,
                "paymentMode" => $payementemode
            );
            if ($payementemode == "PAY_PER_VIEW") {
                $parameters['pricePerView'] = $payperview;
            }

//            $parameters = array(
//                'name' => $name,
//                'eventDate' => $giorno,
//                'paymentMode' => $typemode,
//                'eventHour' => $ora[0],
//                'eventMinute' => $ora[1],
//                'duration' => $duration,
//                'durationUnit' => 'Minute',
//                'publicEvent' => $public,
//                'eventTimeZone' => $_POST['eventTimeZone'],
//                'recordEvent' => $record,
//                "paymentCode" => $paymentCode,
//                "pricePerView" => $pricePerView,
//                "ccy" => $ccy
//            );


            if ($function == "modify") {
                $response = apiUpdateLiveEvent($eventId, $parameters, $timezone);
            } else {
                $response = apiCreateLiveEvent($channelId, $parameters, $timezone);
            }

            if ($response->code == 200 || $response->code == 201) {
                echo '<script language="javascript">
            <!--
            //window.location = "admin.php?page=WimLive";
            window.location = "admin.php?page=' . __("WIMLIVE_urlLink", "wimtvpro") . '"</script>';
                //-->
                //</script>';


                echo '<div class="updated"><p><strong>';

                if ($function == "modify")
                    _e("Update successful", "wimtvpro");
                else
                    _e("Insert successful", "wimtvpro");
                echo '</strong></p></div>';
            } else {
                echo '<div class="error"><p><strong>' . json_decode($response)->message . '</strong></p></div>';
            }
        }
    }
}

/**
 * Crea o modifica un canale esistente. I parametri devono essere passati attraverso POST.
 */
function wimtvpro_savechannel($function) {

    if (isset($_POST["wimtvpro_live"])) {
        //Modify new event live
        $error = 0;
        //Check fields required

        if (strlen(trim($_POST['name'])) == 0) {
            echo '<div class="error"><p><strong>';
            _e("You must set a title.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }


        if (!isset($_POST['public'])) {
            echo '<div class="error"><p><strong>';
            _e("You must check if your event is public or private.", "wimtvpro");
            echo '</strong></p></div>';
            $error++;
        }

        if ($error == 0) {
            $name = $_POST['name'];


            if ($_POST['description'] != "") {
                $description = $_POST['description'];
            }
            if ($_POST['channelId'] != "") {
                $channelId = $_POST['channelId'];
            }

            if ($_POST['public'] != "") {
                $public = $_POST['public'];
            }


            if ($_POST['streampath'] != "") {
                $streampath = $_POST['streampath'];
            }

            $channel_tags = $_POST['tags'];


            $parameters = array(
                "name" => $name,
                "description" => $description,
                "public" => $public,
                "streamPath" => $streampath
            );

//            if (sizeof($channel_tags) >= 1 && $channel_tags[0] != "") {
//
//                $tags = array();
//                if (isset($channel_tags)) {
//                    foreach ($channel_tags as $tag) {
//                        if ($tag != "") {
//                            array_push($tags, $tag);
//                        }
//                    }
//                }
//
//                $parameters['tags'] = $tags;
//            }


            if ($function == "modify") {
                $response = apiUpdateLiveChannel($channelId, $parameters);
//                var_dump("SONO QUI",$response->code,json_decode($response));die;
            } else {

                $response = apiCreateLiveChannel($parameters);
            }
            if ($response->code == 200 || $response->code == 201) {
                echo '<script language="javascript">
            <!--
            //window.location = "admin.php?page=WimLive";
            window.location = "admin.php?page=' . __("WIMLIVE_urlLink", "wimtvpro") . '"</script>';
                //-->
                //</script>';


                echo '<div class="updated"><p><strong>';

                if ($function == "modify")
                    _e("Update successful", "wimtvpro");
                else
                    _e("Insert successful", "wimtvpro");
                echo '</strong></p></div>';
            } else {
                $json = json_decode($response);
                if(isset($json->message)){
                echo '<div class="error"><p><strong>' . $json->message . '</strong></p></div>';
                }else if(isset($json->error)){
                echo '<div class="error"><p><strong>' . $json->error . '</strong></p></div>';
                }else{
                     echo '<div class="error"><p><strong>' . "ERROR" . '</strong></p></div>';
                }
            }
        }
    }
}
