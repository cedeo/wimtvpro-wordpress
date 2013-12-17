<?php

global $user,$wpdb;
include("../../../../../wp-load.php");
$qs=$_SERVER['QUERY_STRING'];
parse_str($qs, $qs_array);
$progId = $qs_array['progId'];
$response = apiProgrammingPool();
echo $response."\n";

$arrayjsonst = json_decode($response);
echo $arrayjsonst->id."\n";

die ();
?>

