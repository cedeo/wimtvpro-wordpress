<?php
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
$url_include = $parse_uri[0] . 'wp-load.php';

if (isset($_GET["isAdmin"])){
    $is_admin = true;
    require_once($url_include);
} else {
    $is_admin = false;
}

function includePlaylist($playlist_id) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (isset($_GET["isAdmin"])){
        $is_admin = true;
    } else {
        $is_admin = false;
    }

    $playlist = dbExtractSpecificPlayist($playlist_id);
    $playlist = $playlist[0];

    $listVideo = $playlist->listVideo;
    $title = $playlist->name;
    //Read Data videos

    $videoList = explode (",",$listVideo);

    $playlist_videos = dbGetUserVideosIn(get_option("wp_userWimtv"), $videoList);
    $sorted_videos = array();

    for ($i=0;$i<count($videoList);$i++){
        foreach ($playlist_videos as $record_new) {
            if ($videoList[$i] == $record_new->contentidentifier){
                array_push($sorted_videos, $record_new);
            }
        }
    }

    $playlist = "";
    $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";

    foreach ($sorted_videos as $video){
        $videoArr[0] = $video;
        $configFile  = wimtvpro_viever_jwplayer($user_agent, $video->contentidentifier, $dirJwPlayer);
        if (!isset($video->urlThumbs)) {
            $thumbs[1] = "";
        }
        else {
            $thumbs = explode ('"',$video->urlThumbs);
        }
        $thumb_url = str_replace("\\", "", $thumbs[1]);
        $playlist .= "{" . $configFile . " 'image':'" . $thumb_url . "','title':'" . str_replace ("+"," ",urlencode($video->title)) . "'},";
    }

    $uploads_info = wp_upload_dir();

    //Check if browser is mobile
    $isApple = (bool) strpos($user_agent, 'Safari') && !(bool) strpos($user_agent, 'Chrome');
    $isiPad = (bool) strpos($user_agent,'iPad');
    $isiPhone = (bool) strpos($user_agent,'iPhone');
    $isAndroid = (bool) strpos($user_agent,'Android');
    $html5 = false;
    if ($isiPad  || $isiPhone || $isAndroid || $isApple) {
        $html5 = true;
    }

    if (!$html5)
        $mode_type = "'flash',src:'" . $dirJwPlayer . "'";
    else
        $mode_type = "'html5'";

    $skin = plugin_dir_url(dirname(__FILE__))  . "/script/skinDefault/wimtv/wimtv.xml";

    $uploads_info = wp_upload_dir();
    $nomeFilexml  = wimtvpro_searchFile($uploads_info["basedir"] .  "/skinWim/" . get_option('wp_nameSkin'),"xml");
    if (get_option('wp_nameSkin')!="") {
        $directory =  $uploads_info["baseurl"] .  "/skinWim";
        $skin = $directory  . "/" . get_option('wp_nameSkin') . "/" . $nomeFilexml;
    }

    ob_start();
    ?>

    <?php if ($is_admin) { ?>
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