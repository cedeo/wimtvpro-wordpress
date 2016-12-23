<?php

/**
 * Written by walter at 06/11/13
 */
function wimtvpro_viever_jwplayer($userAgent, $contentId, $dirJwPlayer) {
    $isApple = (bool) strpos($userAgent, 'Safari') && !(bool) strpos($userAgent, 'Chrome');
    $isiPad = (bool) strpos($userAgent, 'iPad');
    $isiPhone = (bool) strpos($userAgent, 'iPhone');
    $isAndroid = (bool) strpos($userAgent, 'Android');

    $response = apiGetDetailsVideo($contentId);
    $arrayjson = json_decode($response);

    $streamer = $arrayjson->streamingUrl->streamer;
    $url = $arrayjson->url;

    if ($isiPad || $isiPhone || $isApple) {
        $configFile = "'file': '" . $streamer . "',";
    } else if ($isAndroid) {
        $configFile = "file: '" . $url . "',";
    } else {
        $url = lastURLComponent($url);
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $url . "','streamer':'" . $streamer . "',";
    }

//    // NS: JWPLAYER
//    $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $streamer . ":" . $url . "',";

    return $configFile;
}

function getConfFromDataArray($dataArray) {
    $conf = "";
    foreach ($dataArray as $key => $value) {
        if ($value != "") {
            if ($key != "rtmp" && $key != "skin" && $key != "logo" && $key != "modes" && $key != "playlist" && $key != "listbar") {
                $value = "'" . $value . "'";
            }
            $conf.=$key . ": " . $value . ",";
        }
    }
    return $conf;
}

function wimtvpro_get_skin_data() {
    $skinData = array();
    $skinData["skinName"] = "";
    $skinData["styleUrl"] = "";
    $skinData["logoUrl"] = "";

    if (get_option('wp_nameSkin') != "") {
        // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH       
        $skinData["skinName"] = get_option('wp_nameSkin');
        $uploads_info = wp_upload_dir();

        $skinBaseUrl = $uploads_info["baseurl"] . "/skinWim";
        $skinBaseDir = $uploads_info["basedir"] . "/skinWim";
    } else {
        $skinData["skinName"] = "wimtv";
        $skinBaseDir = WIMTV_BASEPATH . DIRECTORY_SEPARATOR . "script/skinDefault";
        $skinBaseUrl = plugin_dir_url(dirname(__FILE__)) . "script/skinDefault";
    }

    $skinCssFile = $skinBaseDir . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".css";
    if (file_exists($skinCssFile)) {
        $skinUrl = $skinBaseUrl . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".css";
        $skinData["styleUrl"] = htmlentities($skinUrl);
    }
    $logoUrl = $skinBaseUrl . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".png";
    if (file_is_displayable_image($logoUrl)) {
        $skinData["logoUrl"] = $logoUrl;
    }
    return $skinData;
}

function configurePlayerJS($contentItem) {
    $player = array();

    $response = apiGetDetailsVideo($contentItem);
    $arrayjson = json_decode($response);


    $player['file'] = $arrayjson->file;
    $player['streamer'] = $arrayjson->streamer;
    $player['type'] = "rtmp";
    $player['primary'] = "flash";
    $player['rtmp'] = "{tunnelling: false, fallback: false}";
    $player['width'] = get_option("wp_widthPreview");
    $player['height'] = get_option("wp_heightPreview");
    $player['image'] = __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $arrayjson->resource->thumbnailId;

    $player['skin'] = "";
    $player['logo'] = "";

    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $player['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $player['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }

    $playerScript = "
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('container').setup({";
    $playerScript .= getConfFromDataArray($player);
    $playerScript .= "});</script>";
    return $playerScript;
}

function configurePlayerJSByJson($arrayjson, $width, $height,$id = null,$thumbnailId = null) {
    
    $player = array();
//    $response = apiGetDetailsVideo($contentItem);
//    $arrayjson = json_decode($response);
    
    $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
    $player['file'] = $arrayjson->file;
    $player['streamer'] = $arrayjson->streamer;
    $player['type'] = "rtmp";
    $player['primary'] = "flash";
    $player['rtmp'] = "{tunnelling: false, fallback: false}";
    if (isset($width)) {
        $player['width'] = $width;
    } else {
        $player['width'] = get_option("wp_widthPreview");
    }
    if (isset($height)) {
        $player['height'] = $height;
    } else {
        $player['height'] = get_option("wp_heightPreview");
    }
//    $player['width'] = get_option("wp_widthPreview");
//    $player['height'] = get_option("wp_heightPreview");
   
    $player['image'] = __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $arrayjson->resource->thumbnailId;
   
    $player['flashplayer'] = $dirJwPlayer;

    $player['skin'] = "";
    $player['logo'] = "";

    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $player['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $player['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }


//    $player['width'] = isset($width) ? $width : get_option('wp_widthPreview');
//    $player['height'] = isset($height) ? $height : get_option('wp_heightPreview');

    $divContainerID = "container-" . rand();
    $playerScript = "
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('" . $divContainerID . "').setup({";


//    $playerScript['modes'] = "[{type:'flash',src:'" . $dirJwPlayer . "'}]";

    $playerScript .= getConfFromDataArray($player);


    $playerScript .= "});</script>";

//if(!isset($arrayjson)){
//    return         '<div id="pay_video'.$id.'" style="display:none;margin:0px 0px 10px 0px;" ><div id="videoPAYVod'.$id.'"  style="width:'.$width.'px;height:'.$height.'px;">'
//             . '<img id="icon_play_vod'.$id.'" src="'.site_url().'/wp-content/plugins/wimtvpro/images/play.png" style="max-width:10%;z-index: 10;display: block;position: relative;top: 55%;left: 45%;" />'
//            . '<img id="icon_thumb_play_vod'.$id.'" src="'.__("API_URL","wimtvpro").'asset/thumbnail/'.$thumbnailId.'" style="width:'.$width.'px;height:'.$height.'px;z-index: -10;" />'
//           
//            . '</div>'
//                      . '</div>';
//       
//   
//}else{

    return "<div style='width: " . $width . "; height: " . $height . ";' id='$divContainerID' ></div>" . $playerScript;
//}
//    return "<div style='width: " . "458px" . "; "  . ";' id='$divContainerID' ></div>" . $playerScript;
}

function configurePlayerJSForLive($channelId, $arrayjson = null, $width, $height, $trackingId = null) {

    $response = null;
    if (isset($trackingId)) {
        $params = array(
            'trackingId' => $trackingId
        );

        $response = apiPlayOnAirLiveEventInChannels($channelId, $params);
        $arrayjson = json_decode($response);
       
    }




    $player = array();
//    $response = apiGetDetailsVideo($contentItem);
//    $arrayjson = json_decode($response);

    $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
    $player['file'] = $arrayjson->file;
    $player['streamer'] = $arrayjson->streamer;
    $player['type'] = "rtmp";
    $player['primary'] = "flash";
    $player['rtmp'] = "{tunnelling: false, fallback: false}";
    if (isset($width)) {
        $player['width'] = $width;
    } else {
        $player['width'] = get_option("wp_widthPreview");
    }
    if (isset($height)) {
        $player['height'] = $height;
    } else {
        $player['height'] = get_option("wp_heightPreview");
    }
//    $player['width'] = get_option("wp_widthPreview");
//    $player['height'] = get_option("wp_heightPreview");
    $player['image'] = __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $arrayjson->thumbnailId;
    $player['flashplayer'] = $dirJwPlayer;

    $player['skin'] = "";
    $player['logo'] = "";

    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $player['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $player['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }


//    $player['width'] = isset($width) ? $width : get_option('wp_widthPreview');
//    $player['height'] = isset($height) ? $height : get_option('wp_heightPreview');

    $divContainerID = "container-" . rand();
    $playerScript = "
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('" . $divContainerID . "').setup({";


//    $playerScript['modes'] = "[{type:'flash',src:'" . $dirJwPlayer . "'}]";

    $playerScript .= getConfFromDataArray($player);


    $playerScript .= "});</script>";



    return "<div style='width: " . $width . "; height: " . $height . ";'id='$divContainerID' >" . $playerScript . "</div>";
}

function configurePlayer_PlaylistJS($playlist_id, $width = null, $height = null) {

//Check if browser is mobile
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $isApple = (bool) strpos($user_agent, 'Safari') && !(bool) strpos($user_agent, 'Chrome');
    $isiPad = (bool) strpos($user_agent, 'iPad');
    $isiPhone = (bool) strpos($user_agent, 'iPhone');
    $isAndroid = (bool) strpos($user_agent, 'Android');
//    var_dump($user_agent);
//    var_dump($isiPad, $isiPhone, $isAndroid, $isApple);die;
//    return configurePlayer_PlaylistJS_HLS($playlist_id, $width, $height);

    if ($isiPad || $isiPhone || $isAndroid || $isApple) {
        return configurePlayer_PlaylistJS_HLS($playlist_id, $width, $height);
    } else {
        return configurePlayer_PlaylistJS_FLASH($playlist_id, $width, $height);
    }
}

function configurePlayer_PlaylistJS_FLASH($playlist_id, $width, $height) {

    $playlistConf = array();
//    if (isset($_GET["isAdmin"])) {
//        $is_admin = true;
//    } else {
//        $is_admin = false;
//    }

    $playlistDBData = dbExtractSpecificPlayist($playlist_id);

    if (sizeof($playlistDBData) < 1) {
        return;
    }
    $playlistDBData = $playlistDBData[0];

    $listVideo = $playlistDBData->listVideo;
    $title = $playlistDBData->name;
    //Read Data videos
    $videoList = explode(",", $listVideo);

    $playlist_videos = dbGetUserVideosIn(get_option("wp_userWimtv"), $videoList);

    $sorted_videos = array();

    for ($i = 0; $i < count($videoList); $i++) {
        foreach ($playlist_videos as $record_new) {
            if ($videoList[$i] == $record_new->boxId) {
                array_push($sorted_videos, $record_new);
            }
        }
    }
    $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";

    $playlistConf["playlist"] = "";
    if (count($sorted_videos) > 0) {
        $playlistConf["playlist"].="[";

        foreach ($sorted_videos as $video) {
            if (!isset($video->urlThumbs)) {
                $thumbs[1] = "";
            } else {
                $thumbs = explode('"', $video->urlThumbs);
            }
            // $thumb_url = str_replace("\\", "", $thumbs[1]);
            $thumb_url = isset($thumbs[1]) ? str_replace("\\", "", $thumbs[1]) : "";

            $response = apiPlayWimVodItem($video->showtimeIdentifier);


//$response = apiGetDetailsVideo($video->contentidentifier);
//            $response = apiGetDetailsVideo($video->contentidentifier);
            $arrayjson = json_decode($response);
//             $url_thumbs = '<img src="http://52.19.105.240:8080/wimtv-server/asset/thumbnail/'.$arrayjson->resource->thumbnailId.'"  title="'.$arrayjson->resource->title.'" class="wimtv-thumbnail" />';
            $playlistConfPlaylistItem = array();
            $playlistConfPlaylistItem['file'] = $arrayjson->file;
            $playlistConfPlaylistItem['streamer'] = $arrayjson->streamer;
            $playlistConfPlaylistItem['type'] = "rtmp";
            $playlistConfPlaylistItem['primary'] = "flash";
            $playlistConfPlaylistItem['rtmp'] = "{tunnelling: false, fallback: false}";
            $playlistConfPlaylistItem['image'] = __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $arrayjson->resource->thumbnailId;
            $playlistConfPlaylistItem['title'] = str_replace("+", " ", utf8_decode(addslashes($video->title)));
            $playlistConfPlaylistItem['flashplayer'] = $dirJwPlayer;
            $playlistConf["playlist"].="{";
            foreach ($playlistConfPlaylistItem as $key => $value) {
                if ($value != "") {
                    if ($key != "rtmp" && $key != "skin" && $key != "logo") {
                        $value = "'" . $value . "'";
                    }
                    $playlistConf["playlist"].=$key . ": " . $value . ",";
                }
            }
            $playlistConf["playlist"] .= "},";


//            $playlistConfPlaylistItem['file'] = $arrayjson->streamingUrl->file;
//            $playlistConfPlaylistItem['streamer'] = $arrayjson->streamingUrl->streamer;
//            $playlistConfPlaylistItem['type'] = "rtmp";
//            $playlistConfPlaylistItem['primary'] = "flash";
//            $playlistConfPlaylistItem['rtmp'] = "{tunnelling: false, fallback: false}";
//            $playlistConfPlaylistItem['image'] = $thumb_url;
//            $playlistConfPlaylistItem['title'] = str_replace("+", " ", utf8_decode(addslashes($video->title)));
//            $playlistConfPlaylistItem['flashplayer'] = $dirJwPlayer;
////            var_dump($playlistConfPlaylistItem);die;
//            $playlistConf["playlist"].="{";
//            foreach ($playlistConfPlaylistItem as $key => $value) {
//                if ($value != "") {
//                    if ($key != "rtmp" && $key != "skin" && $key != "logo") {
//                        $value = "'" . $value . "'";
//                    }
//                    $playlistConf["playlist"].=$key . ": " . $value . ",";
//                }
//            }
//            $playlistConf["playlist"] .= "},";
        }

        $playlistConf["playlist"] .= "]";
    }

    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
//    $playlistConf['listbar'] = "{position: 'right',size: 180}";
    $playlistConf['skin'] = "";
    $playlistConf['logo'] = "";
    $playlistConf['repeat'] = "always";
    $playlistConf['fallback'] = "false";
//    $playlistConf['playlist.position'] = "right";


    $playlistConf['width'] = isset($width) ? $width : get_option('wp_widthPreview');
    $playlistConf['height'] = isset($height) ? $height : get_option('wp_heightPreview');

    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $playlistConf['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $playlistConf['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }


    $playlistConf['primary'] = "flash";
    $divContainerID = "container-" . $playlist_id . "-" . rand();
    $playListScript = "
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('$divContainerID').setup({";


    $playlistConf['modes'] = "[{type:'flash',src:'" . $dirJwPlayer . "'}]";

    $playListScript .= getConfFromDataArray($playlistConf);
    $playListScript .= "});</script>";

    return "<div id='$divContainerID' ></div>" . $playListScript;
}

function configurePlayer_PlaylistJS_HLS($playlist_id, $width, $height) {
    $playlistConf = array();
//    $user_agent = $_SERVER['HTTP_USER_AGENT'];
//    if (isset($_GET["isAdmin"])) {
//        $is_admin = true;
//    } else {
//        $is_admin = false;
//    }

    $playlistDBData = dbExtractSpecificPlayist($playlist_id);
    $playlistDBData = $playlistDBData[0];

    $listVideo = $playlistDBData->listVideo;
    $title = $playlistDBData->name;
    //Read Data videos
    $videoList = explode(",", $listVideo);

    $playlist_videos = dbGetUserVideosIn(get_option("wp_userWimtv"), $videoList);
    $sorted_videos = array();

    for ($i = 0; $i < count($videoList); $i++) {
        foreach ($playlist_videos as $record_new) {
            if ($videoList[$i] == $record_new->boxId) {
                array_push($sorted_videos, $record_new);
            }
        }
    }
    $playlistConf["playlist"] = "";
    if (count($sorted_videos) > 0) {
        $playlistConf["playlist"].="[";

        foreach ($sorted_videos as $video) {
            if (!isset($video->urlThumbs)) {
                $thumbs[1] = "";
            } else {
                $thumbs = explode('"', $video->urlThumbs);
            }
            // $thumb_url = str_replace("\\", "", $thumbs[1]);
            $thumb_url = isset($thumbs[1]) ? str_replace("\\", "", $thumbs[1]) : "";

            $response = apiPlayWimVodItem($video->showtimeIdentifier);


//$response = apiGetDetailsVideo($video->contentidentifier);
//            $response = apiGetDetailsVideo($video->contentidentifier);
            $arrayjson = json_decode($response);
//             $url_thumbs = '<img src="http://52.19.105.240:8080/wimtv-server/asset/thumbnail/'.$arrayjson->resource->thumbnailId.'"  title="'.$arrayjson->resource->title.'" class="wimtv-thumbnail" />';
            $playlistConfPlaylistItem = array();
            $playlistConfPlaylistItem['file'] = $arrayjson->streamer;
            $playlistConfPlaylistItem['primary'] = "html5";
            $playlistConfPlaylistItem['fallback'] = "false";
            $playlistConfPlaylistItem['image'] = __("API_URL", "wimtvpro") . 'asset/thumbnail/' . $arrayjson->resource->thumbnailId;
            $playlistConfPlaylistItem['title'] = str_replace("+", " ", utf8_decode(addslashes($video->title)));

//            $response = apiGetDetailsVideo($video->contentidentifier);
//            $arrayjson = json_decode($response);
//            
//            
//            
//   vecchio         $playlistConfPlaylistItem = array();
//
////            $playlistConfPlaylistItem['file'] = build_HLS_url($arrayjson->streamingUrl->streamer, $arrayjson->streamingUrl->file);
//            $playlistConfPlaylistItem['file'] = $arrayjson->streamingUrl->streamer;
//            $playlistConfPlaylistItem['primary'] = "html5";
//            $playlistConfPlaylistItem['fallback'] = "false";
//            $playlistConfPlaylistItem['image'] = $thumb_url;
//            $playlistConfPlaylistItem['title'] = str_replace("+", " ", utf8_decode(addslashes($video->title)));


            $playlistConf["playlist"].="{";
            foreach ($playlistConfPlaylistItem as $key => $value) {
                if ($value != "") {
                    if ($key != "rtmp" && $key != "skin" && $key != "logo") {
                        $value = "'" . $value . "'";
                    }
                    $playlistConf["playlist"].=$key . ": " . $value . ",";
                }
            }
            $playlistConf["playlist"] .= "},";
        }

        $playlistConf["playlist"] .= "]";
    }

    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
    $playlistConf['skin'] = "";
    $playlistConf['logo'] = "";
    $playlistConf['repeat'] = "always";
    $playlistConf['fallback'] = "false";

    $playlistConf['width'] = isset($width) ? $width : get_option('wp_widthPreview');
    $playlistConf['height'] = isset($height) ? $height : get_option('wp_heightPreview');

    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $playlistConf['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $playlistConf['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }


    $divContainerID = "container-" . $playlist_id . "-" . rand();
    $playListScript = "
            <script type='text/javascript' src='/wp-content/plugins/wimtvpro/script/jwplayer/jwplayer.js'></script>
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('$divContainerID').setup({";

    $playListScript .= getConfFromDataArray($playlistConf);

    $playListScript .= "});</script>";

//    $playListScript = "
//            <script type='text/javascript' src='http://192.168.45.4:5573/wp-content/plugins/wimtvpro/script/jwplayer/jwplayer.js'></script>
//            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>        
//            <script type='text/javascript'>jwplayer('$divContainerID').setup({
//                playlist: [
//                    {
//                        file: 'http://www.wim.tv:1935/vod/10977298cth6br2gf08dhmnirt059unotb.mp4/playlist.m3u8?token=37e63b05-a392-4c75-a258-d668c70128bf',
//                        primary: 'html5',
//                        fallback: false,
//                        title: 'content_field_4',
//                    },
//                    {
//                        file: 'http://www.wim.tv:1935/vod/10977298cth6br2gf08dhmnirt059unotb.mp4/playlist.m3u8?token=37e63b05-a392-4c75-a258-d668c70128bf',
//                        primary: 'html5',
//                        fallback: false,
//                        title: 'content_field_4',
//                    }
//
//                ],
//                repeat: 'always',
//                fallback: false,
//                width: 500,
//                height: 280,
//                }
//            );</script> ";
    return "<div id='$divContainerID' ></div>" . $playListScript;
}

//function build_HLS_url($streamer, $filename) {
//    $streamer_parts = parse_url($streamer);
//    $streamer_parts['scheme'] = "http";
//    $streamer_parts['path'] .= "/" . $filename . "/playlist.m3u8";
//    $result = $streamer_parts['scheme'] . "://" . $streamer_parts['host'] . ":" . $streamer_parts['port'] . $streamer_parts['path'] . "?" . $streamer_parts['query'];
//
//    // 'scheme' => string 'http' (length = 4)
//    // 'host' => string 'www.wim.tv' (length = 10)
//    // 'port' => int 1935
//    // 'path' => string '/vod' (length = 4)
//    // 'query' => string 'token=1330f8e5-3c37-4fcb-b43f-fc09457d84a0&iano=1'
////    var_dump($streamer);
//    // var_dump($filename);
////    var_dump($streamer_parts);
////    var_dump($result);
////    die;
//    return $result;
//}