<?php


global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

$credential = $qs_array['credential'];
$basePath = $qs_array['basepath'];
/**RIMUOVE IN PRODUZIONE PARTE SOTTO**/
$basePath ="http://peer.wim.tv/wimtv-webapp/rest/";
$credential = "simona:12345678";

// chiama

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $basePath . "currentProgramming?".$qs);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $credential);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


$response = curl_exec($ch);
echo $response;

//echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";

//echo "identifier:" .   $arrayjsonst->identifier;
curl_close($ch);

?>

