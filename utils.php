<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_unzip($location,$newLocation) {
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
    return plugins_url($base . "/" . $url, __FILE__);
}