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
$basePath ="http://peer.wim.tv:8080/wimtv-webapp/rest/";
$credential = "simona:12345678";

/*
$basePath = get_option("wp_basePathWimtv");
$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
*/
// chiama
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $basePath . "programmings" );

curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $credential);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));   

$response = curl_exec($ch);
echo $response;

//echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";
$arrayjsonst = json_decode($response);
//echo "identifier:" .   $arrayjsonst->identifier;
curl_close($ch);
?>
