<?php

/**
 * Written by walter at 28/10/13
 */

/**
 * Ritorna la tabella degli eventi live, prendendola da liveList.php
 */
function wimtvpro_elencoLive($type, $identifier, $onlyActive = true) {
    echo '
        <script type="text/javascript">

        jQuery(document).ready(function() {
    ';


    echo 'var url_pathPlugin ="' . plugin_dir_url(__FILE__) . '";';

    echo '
    var timezone = -(new Date().getTimezoneOffset())*60*1000;
	//window.location.assign(window.location + "&timezone="+timezone);';
    // NS: We POST the param "clitimestamp" to let CMS server calculate whether 
    // or no tthe client is in "daylight saving".
    echo '
        var clitimestamp=new Date().getTime();
	jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "liveList.php",
			type: "POST",
			dataType: "html",
			async: false,
			data: "type=' . $type . '&timezone =" + timezone  + "&id=' . $identifier . '&onlyActive=' . $onlyActive . '&timestamp="+ clitimestamp,
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

    if (isset($_POST["wimtvpro_live"])) {
        //Modify new event live
        $error = 0;
        //Check fields required

        if (strlen(trim($_POST['name'])) == 0) {
            /* echo '<div class="error"><p><strong>';
              _e("You must write a wimlive's name.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }
        if (strlen(trim($_POST['payperview'])) == 0) {
            /* echo '<div class="error"><p><strong>';
              _e("You must write a price for your event (or free of charge).","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }
        if (strlen(trim($_POST['Url'])) == 0) {
            /*  echo '<div class="error"><p><strong>';
              _e("You must write a url.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }
        if (strlen(trim($_POST['Giorno'])) == 0) {
            /*  echo '<div class="error"><p><strong>';
              _e("You must write a day of your event.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }
        if (strlen(trim($_POST['Ora'])) == 0) {
            /* echo '<div class="error"><p><strong>';
              _e("You must write a hour of your event.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }
        if (strlen(trim($_POST['Duration'])) == 0) {
            /* echo '<div class="error"><p><strong>';
              _e("You must write a duration of your event.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }

        if (!isset($_POST['Public'])) {
            /* echo '<div class="error"><p><strong>';
              _e("You must check if you event is public or private.","wimtvpro");
              echo '</strong></p></div>'; */
            $error++;
        }

        if ($error == 0) {
            $name = $_POST['name'];
            $payperview = $_POST['payperview'];
            if ($payperview == "0") {
                $typemode = "FREEOFCHARGE";
            } else {
                $paymentCode = apiGetUUID();
                $typemode = "PAYPERVIEW&pricePerView=" . $payperview . "&ccy=EUR&paymentCode=" . $paymentCode;
            }
            $url = $_POST['Url'];

            if ($_POST['Giorno'] != "") {
                $giorno = $_POST['Giorno'];
            } else {
                $giorno = "";
            }
            if ($_POST['Ora'] != "") {
                $ora = explode(":", $_POST['Ora']);
            } else {
                $ora[0] = "";
                $ora[1] = "";
            }
            if ($_POST['Duration'] != "") {
                $separe_duration = explode("h", $_POST['Duration']);
                $duration = ($separe_duration[0] * 60) + $separe_duration[1];
            } else {
                $duration = 0;
            }

            if ($_POST['Public'] != "") {
                $public = $_POST['Public'];
            }

            if ($_POST['Record'] != "") {
                $record = $_POST['Record'];
            }

            $parameters = array('name' => $name,
                'url' => $url,
                'eventDate' => $giorno,
                'paymentMode' => $typemode,
                'eventHour' => $ora[0],
                'eventMinute' => $ora[1],
                'duration' => $duration,
                'durationUnit' => 'Minute',
                'publicEvent' => $public,
                'eventTimeZone' => $_POST['eventTimeZone'],
                'recordEvent' => $record);

            if ($_POST['eventTimeZone'] != "")
                $timezone = $_POST['eventTimeZone'];
            else
                $timezone = $_POST['timelivejs'];
            if ($function == "modify") {
                $response = apiModifyLive($_GET['id'], $parameters, $timezone);
            } else {
                $response = apiAddLive($parameters, $timezone);
            }
            if ($response != "") {
                $message = json_decode($response);
                $result = $message->{"result"};
            }
            if ($result == "SUCCESS") {
                echo '<script language="javascript">
            <!--
            window.location = "admin.php?page=WimLive";
            //-->
            </script>';


                echo '<div class="updated"><p><strong>';
                if ($function == "modify")
                    _e("Update successful", "wimtvpro");
                else
                    _e("Insert successful", "wimtvpro");
                echo '</strong></p></div>';
            } else {
                $formset_error = "";
                foreach ($message->messages as $value) {
                    if ($value->message != "")
                        $formset_error .= $value->message . "<br/>";
                }
                echo '<div class="error"><p><strong>' . $formset_error . '</strong></p></div>';
                echo '<div><strong>' . $response . '</strong></div>';
            }
        }
    }
}