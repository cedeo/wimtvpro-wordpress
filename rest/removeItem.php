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
curl_setopt($ch, CURLOPT_URL, $basePath . "programming/" . $progId . "/items" . $itemId . "?" . $qs);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, $credential);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');

$response = curl_exec($ch);
//echo curl_getinfo($ch, CURLINFO_HTTP_CODE)."\n";
echo $response;

curl_close($ch);
//echo $arrayjsonst->delete."\n";
/*
curl -X DELETE http://localhost/~sergio/php-wimtv/removeItem.php?
progId=1982e185-338b-4333-9262-09fc6810bc34
\&itemId=121e5352-b741-42c3-956c-727e11c5f657
*/



?>

