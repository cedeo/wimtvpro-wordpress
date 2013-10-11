<?php

global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

/*
$basePath =get_option("wp_basePathWimtv");
$credential = get_option("wp_userWimtv") . ":" . get_option("wp_passWimtv");;
*/
/**RIMUOVE IN PRODUZIONE PARTE SOTTO**/
$basePath ="http://peer.wim.tv/wimtv-webapp/rest/";
$credential = "simona:12345678";

// chiama
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $basePath . "programmingPool");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $credential);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
//echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";
print $response."\n";

curl_close($ch);

$arrayjsonst = json_decode($response);
echo $arrayjsonst->id."\n";

die ();
?>

