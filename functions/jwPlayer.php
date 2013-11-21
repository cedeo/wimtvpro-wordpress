<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_viever_jwplayer($userAgent, $contentId,  $dirJwPlayer) {
    $isiPad = (bool) strpos($userAgent,'iPad');

    $isiPhone = (bool) strpos($userAgent,'iPhone');
    $isAndroid = (bool) strpos($userAgent,'Android');


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
        $url_parts = explode("/", $url);
        $url = $url_parts[count($url_parts) -1];
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $url . "','streamer':'" . $streamer . "',";
    }
    return $configFile;
}

?>