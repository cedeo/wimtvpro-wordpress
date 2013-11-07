<?php
global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');

$response = apiAddItem($progId, $_POST);
echo $response;

?>

