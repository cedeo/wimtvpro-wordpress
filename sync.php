<?php

/**
 * Sincronizza il db interno del plugin con i dati presenti su wim.tv.
 * Viene chiamato via Http dopo l'upload di un file o al click sul pulsante "Sincronizza" in WimVod e WimBox
 *
 */
if (!isset($upload))
    include("../../../wp-load.php");
else
    include("../wp-load.php");

global $user, $wpdb;

$parameters = array(
    'pageSize' => '20',
    'pageIndex' => '0'
);

$response = apiGetVideos($parameters);

$response1 = apiGetInPrivatePage($parameters);
$items = json_decode($response1);


$array_all_videos = array();
$array_json_videos = json_decode($response);
$array_json_videos_vod = json_decode($response1)->items;

if ($array_json_videos == NULL) {
    _e("Can not establish a connection with Wim.tv. Contact the administrator.", "wimtvpro");
} else {
    //$num = (array)simplexml_load_string($response);
    foreach ($array_json_videos->items as $index => $video) {
        foreach ($video as $key => $value) {
            $array_all_videos[$index][$key] = $value;
        }
    }
    $elenco_video_wimtv = array();
    $elenco_video_wp = array();
    $elenco_video_wp_vod = array();

    $array_videos_new_wp = dbGetUserVideosId(get_option("wp_userwimtv"));

    foreach ($array_videos_new_wp as $record) {
        array_push($elenco_video_wp, $record->contentidentifier);
    }


    /* Information detail videos into Showtime */
    $json_st = wimtvpro_detail_showtime(FALSE, 0);

    $arrayjson_st = json_decode($json_st);

    $values_st = $arrayjson_st->items;
    if (isset($values_st)) {
        foreach ($values_st as $key => $value) {
            $array_st[$value->{"contentId"}]["showtimeIdentifier"] = $value->{"vodId"};
        }
    }

//    var_dump($array_all_videos);die;
    if ($array_all_videos) {
        foreach ($array_all_videos as $video) {

//            NS2016 $url_video = $video["actionUrl"];
            $status = $video["status"];
//        NS2016    $acquired_identifier = isset($video["relationId"]) ? $video["relationId"] : "";
            $title = $video["title"];
                $urlVideo = null;
            if (isset($video["streamingUrl"])) {
                $urlVideo = $video["streamingUrl"]->streamer . "$$";
                $urlVideo .= $video["streamingUrl"]->file . "$$";
                $urlVideo .= $video["streamingUrl"]->auth_token;
            }
//      NS2016      $duration = $video["duration"];
            $content_item = $video["contentId"];

            if (!isset($video["thumbnailId"])) {
                $url_thumbs = '<img src="' . '/wp-content/plugins/wimtvpro/images/empty.jpg"' . '"  title="' . $title . '" class="wimtv-thumbnail" />';
            } else {
//                apiGetThumb($video["thumbnailId"]);
                $src = __('API_URL', "wimtvpro") . "asset/thumbnail/" . $video["thumbnailId"];
                $url_thumbs = '<img src="' . $src . '"  title="' . $title . '" class="wimtv-thumbnail" />';
            }
            $boxId = $video['boxId'];
            $thumbnailId = $video['thumbnailId'];
            $source = $video['source'];
            $vodCount = $video['vodCount'];
//    NS2016        $categories = "";
//            $valuesc_cat_st = "";
//            foreach ($video["categories"] as $key => $value) {
//                $valuesc_cat_st .= $value->categoryName;
//                $categories .= $valuesc_cat_st;
//                foreach ($value->subCategories as $key => $value) {
//                    $categories .= " / " . $value->categoryName;
//                }
//                $categories .= "<br/>";
//            }
            array_push($elenco_video_wimtv, $content_item);

            if (trim($content_item) != "") {
                $trovato = FALSE;
                //controllo se il video esiste in DRUPAL ma non più in WIMTV
                foreach ($array_videos_new_wp as $record) {

                    $content_itemAll = $record->contentidentifier;
                    if ($content_itemAll == $content_item) {
                        $trovato = TRUE;
                    }
                }
                $pos_wimtv = "";
                $showtime_identifier = "";

                if ($vodCount > 0) {
                    $pos_wimtv = "showtime";
                }
//                if (isset($array_st[$content_item])) {
//                  
//                    $pos_wimtv = "showtime";
////                     $showtime_identifier = $video["vodId"];
////                    $showtime_identifier = $array_st[$content_item]["showtimeIdentifier"];
//                } else {
//                    $pos_wimtv = "";
//                }
                
                  if(!isset($duration)){
                        $duration= null;
                    }

                if (!$trovato) {

                    dbInsertVideo(get_option("wp_userwimtv"), $content_item, $pos_wimtv, $status, $url_thumbs, $boxId, $urlVideo, $title, $duration, $showtime_identifier, $thumbnailId, $source, $vodCount);
                } else {
                  
                    dbUpdateVideo($pos_wimtv, $status, $title, $url_thumbs, $urlVideo, $duration, $showtime_identifier, $boxId, $content_item, $thumbnailId, $source, $vodCount);
                }

                if (isset($values_st)) {
                    foreach ($values_st as $key => $value) {
                        $licenseType = $value->licenseType;
                        $pricePerView = $value->pricePerView;
                        $boxId = $value->boxId;
                        dbUpdateVideoVod($licenseType,$pricePerView,$boxId);
                    }
                }
            }
        }
    } else {
        echo _e("No videos found", "wimtvpro");
    }

    foreach ($array_json_videos_vod as $i) {

        dbUpdateVodId($i->vodId, $i->boxId);
    }

    $delete_into_wp = array_diff($elenco_video_wp, $elenco_video_wimtv);

    foreach ($delete_into_wp as $value) {

        dbDeleteVideo($value);
    }

    if ((isset($_GET['sync']))) {
        $showtime = $_GET['showtime'] === "TRUE" ? true : false;
        $response = wimtvpro_getVideos($showtime, TRUE);
        if (isset($_GET['getvideocount'])) {
            $videoCount = sizeof($array_json_videos->items);
            $response_array['videocount'] = $videoCount;
            $response_array['tablebody'] = $response;
            $response = json_encode($response_array);
        }

        echo $response;
    }

    //UPDATE PAGE MY STREAMING
    update_page_wimvod();
}

if (!isset($upload))
    die();