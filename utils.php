<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_unzip($location,$newLocation) {
    /**
     * Dezippa un file.
     */
    require_once(ABSPATH .'/wp-admin/includes/file.php'); //the cheat
    WP_Filesystem();
    return unzip_file($location, $newLocation);
}

function wimtvpro_searchFile($mainDir, $ext) {
    if ($directory_handle = @opendir($mainDir)) {
        //Read directory for skin JWPLAYER
        while (($file = readdir($directory_handle)) !== FALSE) {
            if ((!is_dir($file)) && ($file!=".") && ($file!="..")) {
                $explodeFile = explode("." , $file);
                if ($explodeFile[1]==$ext){
                    closedir($directory_handle);
                    return $file;
                }
            }
        }
    }
    else {
        $uploads_info = wp_upload_dir();
        if (wimtvpro_unzip($mainDir .".zip", $uploads_info["basedir"] .  "/skinWim")==TRUE) {
            return wimtvpro_searchFile($mainDir, $ext);
        }
    }
    return null;
}

function return_bytes($val) {
    /**
     * Ritorna il numero di byte corrispondenti alla stringa passata.
     * Es. 2k => 2048
     */
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch ($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function getDateRange($startDate, $endDate, $format="d/m/Y") {

    //Create output variable

    $datesArray = array();

    //Calculate number of days in the range

    $total_days = round(abs(strtotime($endDate) - strtotime($startDate)) / 86400, 0) + 1;

    if($total_days<0) {
        return false;
    }

    //Populate array of weekdays and counts

    for($day=0; $day<$total_days; $day++) {
        $datesArray[] = date($format, strtotime("{$startDate} + {$day} days"));
    }

    //Return results array

    return $datesArray;

}


function wimtvpro_checkCleanUrl($base, $url) {
    /**
     * Ritorna la url corretta in base alla directory in cui è installato il plugin.
     * Utile per chiamare i file presenti in functions, o scripts.php.
     */
    return plugins_url($base . "/" . $url, __FILE__);
}

function lastURLComponent($string) {
    /**
     * Ritorna l'ultimo componente di una url.
     * Es. www.google.com/ciao/a/tutti ritorna "tutti".
     */
    $parts = explode("/", $string);
    return $parts[count($parts) - 1];
}


function wimtvpro_link_help() {
    /**
     * Ritorna il markup del pulsante che rimanda al sito di supporto, presente quasi in ogni pagina del plugin.
     */
    return '<div class="help" style="float:right"><a href="' .  get_option("wp_supportLink") . '" target="_new">' . __("Help") . '</a></div>';
}

function timezoneList(){
    /**
     * Ritorna la lista delle Timezone selezionabili quando si crea o si modifica un evento live.
     */
	$arrayTimeZone ["Kwajalein"]="(GMT -12:00) Eniwetok, Kwajalein";
	$arrayTimeZone ["Pacific/Pago_Pago"]="(GMT -11:00) Midway Island, Samoa";
	$arrayTimeZone ["US/Hawaii"]="(GMT -10:00) Hawaii";
	$arrayTimeZone ["US/Alaska"]="(GMT -9:00) Alaska";
	$arrayTimeZone ["America/Los_Angeles"]="(GMT -8:00) Pacific Time (US &amp; Canada)";
	$arrayTimeZone ["America/Denver"]="(GMT -7:00) Mountain Time (US &amp; Canada)";
	$arrayTimeZone ["America/Chicago"]="(GMT -6:00) Central Time (US &amp; Canada), Mexico City";
	$arrayTimeZone ["America/New_York"]="(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima";
	$arrayTimeZone ["America/Halifax"]="(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz";
	$arrayTimeZone ["Canada/Newfoundlan"]="(GMT -3:30) Newfoundland";
	$arrayTimeZone ["America/Sao_Paulo"]="(GMT -3:00) Brazil, Buenos Aires, Georgetown";
	$arrayTimeZone ["Atlantic/South_Georgia"]="(GMT -2:00) Mid-Atlantic";
	$arrayTimeZone ["Atlantic/Cape_Verde"]="(GMT -1:00 hour) Azores, Cape Verde Islands";
	$arrayTimeZone ["Europe/London"]="(GMT) Western Europe Time, London, Lisbon, Casablanca";
	$arrayTimeZone ["Europe/Rome"]="(GMT +1:00 hour) Rome, Madrid, Paris, Copenhagen";
	$arrayTimeZone ["Europe/Istanbul"]="(GMT +2:00) Helsinki, Istanbul, Kaliningrad, South Africa";
	$arrayTimeZone ["Europe/Moscow"]="(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg";
	$arrayTimeZone ["Asia/Tehran"]="(GMT +3:30) Tehran";
	$arrayTimeZone ["Asia/Dubai"]="(GMT +4:00) Abu Dhabi, Dubai, Muscat, Baku, Tbilisi";
	$arrayTimeZone ["Asia/Kabul"]="(GMT +4:30) Kabul";
	$arrayTimeZone ["Indian/Maldives"]="(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent";
	$arrayTimeZone ["Asia/Calcutta"]="(GMT +5:30) Bombay, Calcutta, Madras, New Delhi";
	$arrayTimeZone ["Asia/Katmandu"]="(GMT +5:45) Kathmandu";
	$arrayTimeZone ["Asia/Dacca"]="(GMT +6:00) Almaty, Dhaka, Colombo";
	$arrayTimeZone ["Asia/Bangkok"]="(GMT +7:00) Bangkok, Hanoi, Jakarta";
	$arrayTimeZone ["Asia/Hong_Kong"]="(GMT +8:00) Beijing, Perth, Singapore, Hong Kong";
	$arrayTimeZone ["Asia/Tokyo"]="(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk";
	$arrayTimeZone ["Australia/Adelaide"]="(GMT +9:30) Adelaide, Darwin";
	$arrayTimeZone ["Australia/Sydney"]="(GMT +10:00) Sydney, Melbourne, Brisbane, Vladivostok";
	$arrayTimeZone ["Asia/Magadan"]="(GMT +11:00) Magadan, Solomon Islands, New Caledonia";
	$arrayTimeZone ["Australia/Auckland"]="(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka";
	return  $arrayTimeZone ;
}