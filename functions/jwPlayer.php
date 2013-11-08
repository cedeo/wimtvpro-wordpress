<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_viever_jwplayer($userAgent, $contentId, $video, $dirJwPlayer) {
    $isiPad = (bool) strpos($userAgent,'iPad');
    $urlPlay = explode("$$",$video[0]->urlPlay);
    $isiPhone = (bool) strpos($userAgent,'iPhone');
    $isAndroid = (bool) strpos($userAgent,'Android');

    $contentId = $video[0]->contentidentifier;
	$response = apiGetDetailsVideo($contentId);
	$arrayjson   = json_decode($response);

    $streamer = $arrayjson->streamingUrl->streamer;
	$file = $arrayjson->streamingUrl->file;
	$url = $arrayjson->url;

    if ($isiPad  || $isiPhone) {
        $urlPlayIPadIphone = "";
        $urlPlayIPadIphone = $arrayjson->streamingUrl->streamer;
        $configFile = "'file': '" .  $streamer . "',";
    } else if ($isAndroid) {
        $configFile = "file: '" . $url . "',";
    } else {
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $url . "','streamer':'" . $$streamer . "',";
    }
    return $configFile;
}

?>