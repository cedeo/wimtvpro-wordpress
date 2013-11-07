<?php


global $user,$wpdb;
include("../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];


$response = apiGetCurrentProgrammings($qs);
echo $response;


//echo "identifier:" .   $arrayjsonst->identifier;

?>

