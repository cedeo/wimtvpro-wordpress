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
header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');
// chiama
$ch = curl_init();
print_r($_POST);
curl_setopt($ch, CURLOPT_URL,$basePath . "programming/".$progId."/items");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $credential);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);

curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));   
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
//echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";
echo $response;

curl_close($ch);
/*
$arrayjsonst = json_decode($response);
echo $arrayjsonst->id."\n";*/
/*
curl http://localhost/~sergio/php-wimtv/addItem.php?progId=1982e185-338b-4333-9262-09fc6810bc34 
-d "endDatetime=1377036022200&showtimeId=dbaeb3ca-227e-4f21-b2c0-be9f9f41b99b&startDatetime=1377036000000"
*/
die ();
?>

