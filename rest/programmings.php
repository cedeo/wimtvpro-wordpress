<?php

global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

/*
$basePath = get_option("wp_basePathWimtv");
$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
*/
// chiama
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');

$response = apiGetProgrammings($_POST);
echo $response;

$arrayjsonst = json_decode($response);
//echo "identifier:" .   $arrayjsonst->identifier;
curl_close($ch);
?>
