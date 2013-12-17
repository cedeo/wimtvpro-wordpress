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
$response = apiGetCurrentProgrammings($qs);
echo $response;
echo "identifier:" .   $arrayjsonst->identifier;

?>

