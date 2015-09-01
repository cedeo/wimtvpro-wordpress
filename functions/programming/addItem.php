<?php
global $user,$wpdb;
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
$url_include = $parse_uri[0] . 'wp-load.php';

if(@file_get_contents($url_include)){
	require_once($url_include);
}
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];

header('Access-Control-Allow-Origin: *');
header('Content-type: application/json');
header('Response: HTTP/1.1 200 OK');

$response = apiAddItem($progId, $_POST);
echo $response;

?>

