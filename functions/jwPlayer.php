<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_viever_jwplayer($userAgent, $contentId, $video, $dirJwPlayer) {

    $isiPad = (bool) strpos($userAgent,'iPad');
    $urlPlay = explode("$$",$video[0]->urlPlay);
    $isiPhone = (bool) strpos($userAgent,'iPhone');
    $isAndroid = (bool) strpos($userAgent,'Android');

    if ($isiPad  || $isiPhone || $isAndroid) {

        $contentId = $video[0]->contentidentifier;
        $response = apiGetDetailsVideo($contentId);
        $arrayjson   = json_decode($response);

    }

    if ($isiPad  || $isiPhone) {
        $urlPlayIPadIphone = "";
        $urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
        $configFile = "'file': '" . $urlPlayIPadIphone . "',";
    } else if ($isAndroid) {
        $urlPlayAndroid =$arrayjson->streamingUrl->streamer;
        $filePlayAndroid =$arrayjson->streamingUrl->file;
        $configFile = "file: '" . $arrayjson->url . "',";
    } else {
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $urlPlay[1] . "','streamer':'" . $urlPlay[0] . "',";
    }
    return $configFile;
}

?>