<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
$url_include = $parse_uri[0] . 'wp-load.php';

//require_once('/var/www/htdocs/wimtvPlugin/wordpress4/wp-load.php');
if (isset($_GET["isAdmin"])){
    $is_admin = true;
    require_once($url_include);
} else {
    $is_admin = false;
}

function includePlaylist($playlist_id) {
//    return"<script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script> <script type='text/javascript'>jwplayer('container').setup({file: '98305vsrk2902t73fuoiku7m17tq3ee.mp4',streamer: 'rtmp://peer.wim.tv:1935/vod?token=b39e7056-5689-4690-9585-71c252d50515',type: 'rtmp',primary: 'flash',rtmp: {tunnelling: false, fallback: false},width: '500',height: '280',image: 'http://peer.wim.tv:80/wimtv-webapp/thumbnails/urn-wim-tv-contentitem-4e7c2cd6-df4b-4661-9570-e9186c11ae17.jpg',skin: {name : 'ns', url : 'http://192.168.55.101:5573/wp-content/uploads/skinWim/ns/ns.css'},logo: {file : 'http://192.168.55.101:5573/wp-content/uploads/skinWim/ns/ns.png', hide : true},});</script>
//";
//    configurePlayer_PlaylistJS($playlist_id);
//    return;
//    
//    
//    $user_agent = $_SERVER['HTTP_USER_AGENT'];
//
//    if (isset($_GET["isAdmin"])){
//        $is_admin = true;
//    } else {
//        $is_admin = false;
//    }
//
//    $playlist = dbExtractSpecificPlayist($playlist_id);
//    $playlist = $playlist[0];
//
//    $listVideo = $playlist->listVideo;
//    $title = $playlist->name;
//    //Read Data videos
//    $videoList = explode (",",$listVideo);
//
//    $playlist_videos = dbGetUserVideosIn(get_option("wp_userWimtv"), $videoList);
//    $sorted_videos = array();
//
//    for ($i=0;$i<count($videoList);$i++){
//        foreach ($playlist_videos as $record_new) {
//            if ($videoList[$i] == $record_new->contentidentifier){
//                array_push($sorted_videos, $record_new);
//            }
//        }
//    }
//
//    $playlist = "";
//    $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
//
//    foreach ($sorted_videos as $video){
//        $videoArr[0] = $video;
//        $configFile  = wimtvpro_viever_jwplayer($user_agent, $video->contentidentifier, $dirJwPlayer);
//        if (!isset($video->urlThumbs)) {
//            $thumbs[1] = "";
//        }
//        else {
//            $thumbs = explode ('"',$video->urlThumbs);
//        }
//        $thumb_url = str_replace("\\", "", $thumbs[1]);
//        $playlist .= "{" . $configFile . " 'image':'" . $thumb_url . "','title':'" . str_replace ("+"," ",  utf8_decode($video->title)) . "'},";
//    }
//
//    $uploads_info = wp_upload_dir();
//
//    //Check if browser is mobile
//    $isApple = (bool) strpos($user_agent, 'Safari') && !(bool) strpos($user_agent, 'Chrome');
//    $isiPad = (bool) strpos($user_agent,'iPad');
//    $isiPhone = (bool) strpos($user_agent,'iPhone');
//    $isAndroid = (bool) strpos($user_agent,'Android');
//    $html5 = false;
//    if ($isiPad  || $isiPhone || $isAndroid || $isApple) {
//        $html5 = true;
//    }
//
//    if (!$html5)
//        $mode_type = "'flash',src:'" . $dirJwPlayer . "'";
//    else
//        $mode_type = "'html5'";
//
//    $skin = plugin_dir_url(dirname(__FILE__))  . "/script/skinDefault/wimtv/wimtv.xml";
//
//    $uploads_info = wp_upload_dir();
//    $nomeFilexml  = wimtvpro_searchFile($uploads_info["basedir"] .  "/skinWim/" . get_option('wp_nameSkin'),"xml");
//    if (get_option('wp_nameSkin')!="") {
//        $directory =  $uploads_info["baseurl"] .  "/skinWim";
//        $skin = $directory  . "/" . get_option('wp_nameSkin') . "/" . $nomeFilexml;
//    }
//
//    ob_start();
    ?>

    <?php if ($is_admin) { ?>
    <div style='text-align:center;'><h3><?php echo $title ?></h3>
    <?php } else { ?>
    <div style='text-align:center;width:100%;'>
    <?php } ?>
        <div id='container-<?php echo $playlist_id ?>' style='margin:0;padding:0 10px;'></div>
        <?php echo configurePlayer_PlaylistJS($playlist_id);?>
<!--        <script type='text/javascript'>
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
        </script>-->
    <?php if ($is_admin) { ?>
        <div style='float:left; width:50%;'>
            Embedded:
            <textarea style='resize: none; width:90%;height:70px;font-size:10px' readonly='readonly' onclick='this.focus(); this.select();'>
                <?php echo htmlentities($code) ?>
            </textarea>
        </div>
        <div style='float:left; width:50%;'>
            Shortcode:
            <textarea style='resize: none; width:90%;height:70px;font-size:20px' readonly='readonly' onclick='this.focus(); this.select();'>
                [playlistWimtv id='<?php echo $playlist_id ?>']
            </textarea>
        </div>
    <?php }?>
    </div>
<?php
    return ob_get_clean();
}

if ($is_admin) {
    $id = $_GET['id'];
    echo includePlaylist($id);
}

?>