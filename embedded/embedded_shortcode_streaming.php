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
$vodId = $_POST['id'];
$current_url = $_POST['current_url'];

if ($name_function == "PAY") {

    $pricePerView = $_POST['price'];

    $params = array(
        "embedded" => false,
        "mobile" => false,
        "returnUrl" => $current_url,
        "cancelUrl" => $current_url
    );


    $response_pay = apiPayToPlayWimVodItem($params, $vodId);

    $response_json_pay = json_decode($response_pay);

    $trackingId = $response_json_pay->trackingId;
//    echo "localStorage.setItem('".$vodId."','".$trackingId."')";
    $elements = array(
        'trackingId' => $trackingId,
        'url' => $response_json_pay->url
    );
    echo json_encode($elements);

} else if ($name_function == "PLAY") {
    $trackingId = $_POST['trackingId'];

    $params = array(
//        'trackingId' => "19057224-60ba-416e-8f25-bf472f7352d8"
        'trackingId' => $trackingId
    );

    $response = apiPlayWimVodItemPublic($vodId, $params);
    $response_json_play = json_decode($response);

//$elements = array(
//        'result' => $response_json_play->result,
//        'file' => $response_json_pay->file,
//        'streamer'=> $response_json_pay->streamer
//    );
    $result_jw = null;
    $width = $_POST['width'];
    $height = $_POST['height'];
    if ($response_json_play->result == "PLAY") {
        $result_jw = configurePlayerJSByJson($response_json_play, $width, $height,null,null);
    } else {
        $thumbnailId = $_POST['thumbnailId'];
        $pricePerView = $_POST['price'];


        $params = array(
            "embedded" => false,
            "mobile" => false,
            "returnUrl" => $current_url,
            "cancelUrl" => $current_url
        );


        $response_pay = apiPayToPlayWimVodItem($params, $vodId);

        $response_json_pay = json_decode($response_pay);

        $trackingId = $response_json_pay->trackingId;

        $url = $response_json_pay->url;

        $result_jw = '<div id="pay_video' . $vodId . '" style=""><div id="videoPAYVod"  style="width:' . $width . 'px;height:' . $height . 'px;">'
                . '<img id="icon_play_vod' . $vodId . '" src="' . site_url() . '/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
                . '<img id="icon_thumb_play_vod' . $vodId . '" src="' . __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $thumbnailId . '" style="width:' . $width . 'px;height:' . $height . 'px;z-index: -10;" />'
                . '</div></div>';
//    
//     echo "<script> localStorage.removeItem('".$vodId."');"
//              ."localStorage.setItem('".$vodId."','".$trackingId."');"
//              . " jQuery('a#paga_".$vodId."').attr('href',url);" 
//                   ."</script>";
    }

    $elements = array(
        'result' => $response_json_play->result,
        'res_html' => $result_jw
    );

    if ($response_json_play->result != "PLAY") {
        $elements['url'] = $url;
        $elements['trackingId'] = $trackingId;
    }

//   echo $elements;
    echo json_encode($elements);
}