<?php
// EMBEDDED.PHP
// NS: THIS FILE CONCERN WIMBOX e "WIMVOD" PLAY (WIMVOD PAYPERVIEW MODE IS IMPLEMENTED IN embeddedAll.php) 

global $user;
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$url_include = $parse_uri[0] . 'wp-load.php';

if (@file_get_contents($url_include)) {
    require_once($url_include);
}

// NS: NOT USED
//$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
//$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");
//
//$urlEmbedded = get_option("wp_urlEmbeddedPlayerWimtv");
//$replaceContent = get_option("wp_replaceContentWimtv");
$code = $_GET['c'];

if (strlen($code) > 0) {
    $contentItem = $_GET['c'];
    $streamItem = $_GET['s'];
    $wimbox = $_GET['box'];
    $showtime = json_decode(wimtvpro_detail_showtime(true, $streamItem));

    $insecureMode = "&insecureMode=on";
    $skin = "";
    $logo = "";
    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $skin = "&skin=" . htmlentities($skinData['styleUrl']);
    }

    if ($skinData['logoUrl'] != "") {
        $logo = "&logo=" . htmlentities($skinData['logoUrl']);
    }


    $height = get_option("wp_heightPreview");
    $width = get_option("wp_widthPreview");

    $parametersGet = "get=1&width=" . $width . "&height=" . $height . $insecureMode . $skin . $logo;
//    $response = apiGetPlayerShowtime($showtime->{"contentId"}, $parametersGet);
    
    if(isset($wimbox)){
         $response = apiPlayWimboxItem($streamItem);
    }else {
//     $response = apiPlayWimVodItem($streamItem);
     $response = apiPreviewWimVodItem($streamItem);
  
    }
    
         $response_jw = configurePlayerJSByJson(json_decode($response),$width,$height);

    ?>

    <div style='text-align:center;'>
        <?php echo $response_jw ?>
             <?php 
             if(isset($wimbox)){
                 $arrayjson = json_decode($response);
             ?> 
             <h3><?php  echo $arrayjson->resource->title ?></h3>

             <?php } else {
               ?>
        <h3><?php  echo $showtime->{"title"} ?></h3>
        <p>[<?php echo $showtime->{"duration"} ?>] <?php echo $showtime->{"description"} ?></p>
       </div>
             <?php }
             }
?>

