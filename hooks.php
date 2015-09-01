<?php

include_once("api/wimtv_api.php");
include_once("api/analytics_api.php");

include_once("menu/pages/settings/configuration.php");

header('Content-type: application/json');


define('BASE_URL', get_bloginfo('url'));

function wimtvpro_submenu($view_page) {
    $submenu = "<ul class='subsubsub'>";
    $submenu .= "<li><a href='admin.php?page=WimTvPro' class='config'>" . __("Configuration", "wimtvpro") . "</a> |";
    if ($view_page) {
        $submenu .= "<li><a href='admin.php?page=WimTvPro&pack=1' class='packet'>" . __("Pricing", "wimtvpro") . "</a> |";
        $submenu .= "<li><a href='admin.php?page=WimTvPro&update=1' class='payment'>" . __("Monetisation", "wimtvpro") . "</a> |";
        $submenu .= "<li><a href='admin.php?page=WimTvPro&update=2' class='live'>" . __('Live', "wimtvpro") . "</a> |";
        $submenu .= "<li><a href='admin.php?page=WimTvPro&update=3' class='user'>" . __("Personal Info", "wimtvpro") . "</a> |";
        
        // NS: Disabling "Features Page"
        $submenu .= "<li><a href='admin.php?page=WimTvPro&update=4' class='other'>" . __("Features", "wimtvpro") . "</a> ";
    }
    $submenu .= "</ul>";
    return $submenu;
}

/**
 * Questa funzione genera il contenuto della sezione che permette l'inserimento di contenuti di wim.tv all'interno dei post.
 * In base al contenuto scelto inserisce lo shortcode corrispondente all'interno del post, nel punto dov'è posizionato il cursore.
 */
function media_wimtvpro_process() {
    media_upload_header();

    $script = "<script type='text/javascript'>
  jQuery('.buttonInsert').click(function() {
      var width = jQuery(this).parent().children('.w').val();
      var height = jQuery(this).parent().children('.h').val();
      var id = jQuery(this).attr('id');
	  var win = window.dialogArguments || opener || parent || top;
      var shortcode  =  \"[streamingWimtv id='\" + id + \"' width='\" + width + \"' height='\" + height + \"' ]\";
	  win.send_to_editor(shortcode);

  });
  jQuery('.buttonInsertPlayList').click(function() {
      var id = jQuery(this).attr('id');
	  var win = window.dialogArguments || opener || parent || top;
      var shortcode  =  \"[playlistWimtv id='\" + id + \"']\";
	  win.send_to_editor(shortcode);

  });</script>";

    $videos = "<table class='itemsInsert'>" . wimtvpro_getVideos(TRUE, FALSE, TRUE) . "</table><div class='empty'></div>";

    $array_playlist = dbExtractPlayList(get_option('wp_userwimtv'));
    $numberPlaylist = count($array_playlist);
    if ($numberPlaylist > 0) {
        $videos .= "<h3 class='media-title'>PlayList</h3><ul class='itemsInsert'>";
        foreach ($array_playlist as $record_new) {
            $listVideo = $record_new->listVideo;
            $title = $record_new->name;
            $arrayVideo = explode(",", $listVideo);
            if ($listVideo == "")
                $countVideo = 0;
            else
                $countVideo = count($arrayVideo);

            $uploads_info = wp_upload_dir();
            $directory = $uploads_info["baseurl"] . "/skinWim";
            $array_videos_new_drupal = array();
            for ($i = 0; $i < count($videoList); $i++) {
                foreach ($array_videos as $record_new) {
                    if ($videoList[$i] == $record_new->contentidentifier) {
                        array_push($array_videos_new_drupal, $record_new);
                    }
                }
            }

            $playlist = "";
            foreach ($array_videos_new_drupal as $videoT) {
                $videoArr[0] = $videoT;
                $dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";

                $configFile = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'], $videoT->contentidentifier, $dirJwPlayer);
                if (!isset($videoT->urlThumbs))
                    $thumbs[1] = "";
                else
                    $thumbs = explode('"', $videoT->urlThumbs);

                $playlist .= "{" . $configFile . " 'image':'" . $thumbs[1] . "','title':'" . str_replace("+", " ", urlencode($videoT->title)) . "'},";
            }
            $videos .= $playlist;
            $videos .= "<li>" . $title . "(" . $countVideo . ")";
            $videos .= '<input type="hidden" value="' . $_GET['post_id'] . '" name="post_id">';
            $send = get_submit_button(__('Insert into Post', "wimtvpro"), 'buttonInsertPlayList', $record_new->id, false);


            $videos .= $send . '</li>';
        }
    }

    $videos .= "</ul><div class='empty'></div>";
    echo $videos . $script;
}

function wimtvpro_media_menu_handle() {
    return wp_iframe('media_wimtvpro_process');
}

add_action('media_upload_wimtvpro', 'wimtvpro_media_menu_handle');
?>
