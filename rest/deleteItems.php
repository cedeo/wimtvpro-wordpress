<?php
global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

////TODO: A che serve itemId???
$itemId = $qs_array['itemId'];

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');

$response = apiDeleteItems($progId, $itemId, $qs); // curl_exec($ch);
echo $response;

curl_close($ch);
//echo $arrayjsonst->delete."\n";



?>

