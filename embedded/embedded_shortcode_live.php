<?php

/*
 * Whitten By Netsense SRL - 2016
 */

global $user;
global $wp, $wpdb, $wp_query, $wp_the_query, $wp_rewrite, $wp_did_header;
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$url_include = $parse_uri[0] . 'wp-load.php';

if (@file_get_contents($url_include)) {
require_once($url_include);
}
//include_once("api/api.php");


$name_function = $_POST['name_action'];
$eventId = $_POST['id'];
$current_url = $_POST['current_url'];

if ($name_function == "PAY") {

$pricePerView = $_POST['price'];
$params_pay = array(
"embedded" => false,
 "mobile" => false,
 "returnUrl" => $current_url,
 "cancelUrl" => $current_url
);

$response_pay = apiPayForPlayLiveEventPublic($eventId, $params_pay);

$response_json_pay = json_decode($response_pay);

$trackingId = $response_json_pay->trackingId;


//    echo "localStorage.setItem('".$vodId."','".$trackingId."')";
$elements = array(
'trackingId' => $trackingId,
 'url' => $response_json_pay->url
);
echo json_encode($elements);
//
//    if (isset($response_json_pay->url)) {
//
//        echo "
//                     <script>
//                          jQuery(document).ready(function() {
//                          localStorage.setItem('" . $vodId . "', '" . $trackingId . "')
//                         jQuery('img#icon_play_vod" . $vodId . "').click(function(){
//                         jQuery.colorbox({width: '400px',
//                            height:'100px',
//                             onComplete: function() {
//                             jQuery(this).colorbox.resize();            
//                              },
//                                onLoad: function() {
//                                    jQuery('#cboxClose').remove();
//                                },
//                               
//                            html:'<h2>" . __("The event has a cost of", "wimtvpro") . $pricePerView . "€ </br>" . __("You sure you want to pay?", "wimtvpro") . "</h2><h2><a href=\"" . $response_json_pay->url . "\">Yes</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"?payement_deny=true\">" . __("No") . "</a></h2>'
//                           
//                             
//                         });
//                           
//                         
//                         });
//                         
//                    jQuery('img#icon_thumb_play_vod" . $vodId . "').click(function(){
//                         jQuery.colorbox({width: '400px',
//                            height:'100px',
//                             onComplete: function() {
//                             jQuery(this).colorbox.resize();            
//                              },
//                                onLoad: function() {
//                                    jQuery('#cboxClose').remove();
//                                },
//                               
//                            html:'<h2>" . __("The event has a cost of", "wimtvpro") . $pricePerView . "€ </br>" . __("You sure you want to pay?", "wimtvpro") . "</h2><h2><a href=\"" . $response_json_pay->url . "\">Yes</a> | <a onClick=\"jQuery(this).colorbox.close();\" href=\"?payement_deny=true\">" . __("No") . "</a></h2>'
//                           
//                             
//                         });
//                           
//                         
//                         });
//                         
//                         });  
//                     </script>
//                      ";
//
//        return '<div id="videoPAYVod"  style="width:' . $width . 'px;height:' . $height . 'px;">'
//                . '<img id="icon_play_vod' . $vodId . '" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
//                . '<img id="icon_thumb_play_vod' . $vodId . '" src="' . __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $thumbnailId . '" style="width:' . $width . 'px;height:' . $height . 'px;z-index: -10;" />'
//                . '</div>';
//    }
} else if ($name_function == "PLAY") {
$channelId = $_POST['channelId'];
$trackingId = $_POST['trackingId'];


//        $params = array(
//            'trackingId' => $trackingId
//        );
$arrayjson = null;
if (isset($trackingId)) {
$params = array(
'trackingId' => $trackingId
);

$response = apiPlayOnAirLiveEventInChannels($channelId, $params);
$arrayjson = json_decode($response);

}
if($arrayjson->result == "PLAY"){
$pageLive = configurePlayerJSForLive($channelId, $arrayjson, $width, $height,null);
$elements = array(
'result' => 'PLAY',
 'res_html' => $pageLive
);

echo json_encode($elements);
}else{

$pricePerView = $_POST['price'];


$params_pay = array(
"embedded" => false,
 "mobile" => false,
 "returnUrl" => $current_url,
 "cancelUrl" => $current_url
);


$response_pay = apiPayForPlayLiveEventPublic($eventId, $params_pay);

$response_json_pay = json_decode($response_pay);

$trackingId = $response_json_pay->trackingId;
$url = $response_json_pay->url;



$elements = array(
'result' => "PAY_PER_VIEW"

);


$elements['url'] = $url;
$elements['trackingId'] = $trackingId;

//   echo $elements;
echo json_encode($elements);
}

}

//$elements = array(
//        'result' => $response_json_play->result,
//        'file' => $response_json_pay->file,
//        'streamer'=> $response_json_pay->streamer
//    );
//    $result_jw = null;
//    $width = $_POST['width'];
//    $height = $_POST['height'];
//    if ($response_json_play->result == "PLAY") {
//        $result_jw = configurePlayerJSByJson($response_json_play, $width, $height);
//    } 
//    else {
//        $thumbnailId = $_POST['thumbnailId'];
//        $pricePerView = $_POST['price'];
//
//
//        $params = array(
//            "embedded" => false,
//            "mobile" => false,
//            "returnUrl" => $current_url,
//            "cancelUrl" => $current_url
//        );
//
//        $response_pay = apiPayToPlayWimVodItem($params, $vodId);
//
//        $response_json_pay = json_decode($response_pay);
//
//        $trackingId = $response_json_pay->trackingId;
//
//        $url = $response_json_pay->url;
//
//        $result_jw = '<div id="pay_video' . $vodId . '" style=""><div id="videoPAYVod"  style="width:' . $width . 'px;height:' . $height . 'px;">'
//                . '<img id="icon_play_vod' . $vodId . '" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
//                . '<img id="icon_thumb_play_vod' . $vodId . '" src="' . __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $thumbnailId . '" style="width:' . $width . 'px;height:' . $height . 'px;z-index: -10;" />'
//                . '</div></div>';
////    
////     echo "<script> localStorage.removeItem('".$vodId."');"
////              ."localStorage.setItem('".$vodId."','".$trackingId."');"
////              . " jQuery('a#paga_".$vodId."').attr('href',url);" 
////                   ."</script>";
//    }
//
//    $elements = array(
//        'result' => $response_json_play->result,
//        'res_html' => $result_jw
//    );
//
//    if ($response_json_play->result != "PLAY") {
//        $elements['url'] = $url;
//        $elements['trackingId'] = $trackingId;
//    }
//   echo $elements;
//    echo json_encode($elements);

