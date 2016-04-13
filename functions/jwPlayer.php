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
            if ($key != "rtmp" && $key != "skin" && $key != "logo" && $key != "modes" && $key != "playlist" && $key !="listbar") {
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

        $skinCssFile = $uploads_info["basedir"] . "/skinWim" . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".css";
        if (file_exists($skinCssFile)) {
            $skinUrl = $uploads_info["baseurl"] . "/skinWim" . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".css";
            $skinData["styleUrl"] = htmlentities($skinUrl);
        }
        $logoUrl = $uploads_info["baseurl"] . "/skinWim" . "/" . $skinData["skinName"] . "/" . $skinData["skinName"] . ".png";
        if (file_is_displayable_image($logoUrl)) {
            $skinData["logoUrl"] = $logoUrl;
//            $player['logo'] = "{file : '" . $logoUrl . "', hide : true}";
        }
    }
    return $skinData;
}

function configurePlayerJS($contentItem) {
    $player = array();

    $response = apiGetDetailsVideo($contentItem);
    $arrayjson = json_decode($response);

    $player['file'] = $arrayjson->streamingUrl->file;
    $player['streamer'] = $arrayjson->streamingUrl->streamer;
    $player['type'] = "rtmp";
    $player['primary'] = "flash";
    $player['rtmp'] = "{tunnelling: false, fallback: false}";


    $player['width'] = get_option("wp_widthPreview");
    $player['height'] = get_option("wp_heightPreview");
    $player['image'] = $arrayjson->thumbnailUrl;

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

function configurePlayer_PlaylistJS($playlist_id) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (isset($_GET["isAdmin"])) {
        $is_admin = true;
    } else {
        $is_admin = false;
    }

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
            if ($videoList[$i] == $record_new->contentidentifier) {
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
            $thumb_url = str_replace("\\", "", $thumbs[1]);

            $response = apiGetDetailsVideo($video->contentidentifier);
            $arrayjson = json_decode($response);
            $playlistConfPlaylistItem = array();
            $playlistConfPlaylistItem['file'] = $arrayjson->streamingUrl->file;
            $playlistConfPlaylistItem['streamer'] = $arrayjson->streamingUrl->streamer;
            $playlistConfPlaylistItem['type'] = "rtmp";
            $playlistConfPlaylistItem['primary'] = "flash";
            $playlistConfPlaylistItem['rtmp'] = "{tunnelling: false, fallback: false}";
            $playlistConfPlaylistItem['image'] = $thumb_url;
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

    $skinData = wimtvpro_get_skin_data();
    if ($skinData['styleUrl'] != "") {
        $playlistConf['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
    }

    if ($skinData['logoUrl'] != "") {
        $playlistConf['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
    }


    $playlistConf['primary'] = "flash";
    $playListScript = "
            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
            <script type='text/javascript'>jwplayer('container-$playlist_id').setup({";


    //Check if browser is mobile
    $isApple = (bool) strpos($user_agent, 'Safari') && !(bool) strpos($user_agent, 'Chrome');
    $isiPad = (bool) strpos($user_agent, 'iPad');
    $isiPhone = (bool) strpos($user_agent, 'iPhone');
    $isAndroid = (bool) strpos($user_agent, 'Android');
    $html5 = false;
    if ($isiPad || $isiPhone || $isAndroid || $isApple) {
        $html5 = true;
    }

    if (!$html5) {
        $playlistConf['modes'] = "[{type:'flash',src:'" . $dirJwPlayer . "'}]";
//        "'flash',src:'" . $dirJwPlayer . "'";
    } else {
        $playlistConf['modes'] = "[{type:'html5'}]";
//        $playlistConf['modes'] = "'html5'";
    }

    $playListScript .= getConfFromDataArray($playlistConf);
    $playListScript .= "});</script>";

//    ob_start();
    return $playListScript;
/*    NS: QUESTA SEZIONE NON DOVREBBE ESSERE PIU' IN USO
     if ($is_admin) { ?>
        <div style='text-align:center;'><h3><?php echo $title ?></h3>
        <?php } else { ?>
            <div style='text-align:center;width:100%;'>
            <?php } ?>
            <div id='container-<?php echo $playlist_id ?>' style='margin:0;padding:0 10px;'></div>
            <script type='text/javascript'>
                jwplayer('container-<?php echo $playlist_id ?>').setup({
                    modes: [{type: <?php echo $mode_type ?>}],
                    repeat: 'always',
                    skin: '<?php echo $skin ?>',
                    width: '100%',
                    fallback: false,
                    playlist: [<?php echo $playlist ?>],
                    'playlist.position': 'right',
                    'playlist.size': '30%'
                });
            </script>
            <?php if ($is_admin) { ?>
                <div style='float:left; width:50%;'>
                    Embedded:
                    <textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus();
                        this.select();'>
                                  <?php echo htmlentities($code) ?>
                    </textarea>
                </div>
                <div style='float:left; width:50%;'>
                    Shortcode:
                    <textarea style='resize: none; width:90%;height:70px;font-size:20px' readonly='readonly' onclick='this.focus();
                        this.select();'>
                        [playlistWimtv id='<?php echo $playlist_id ?>']
                    </textarea>
                </div>
            <?php } ?>
        </div>
        <?php
        return ob_get_clean();

//    foreach ($player as $key => $value) {
//        if ($value != "") {
//            if ($key != "rtmp" && $key != "skin" && $key != "logo") {
//                $value = "'" . $value . "'";
//            }
//            $playerScript.=$key . ": " . $value . ",";
//        }
//    }


        return $playerScript;
  */
    }
    ?>