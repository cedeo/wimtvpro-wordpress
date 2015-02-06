<?php
/**
 * Written by walter at 06/11/13
 */
function wimtvpro_viever_jwplayer($userAgent, $contentId,  $dirJwPlayer) {
    $isApple = (bool) strpos($userAgent, 'Safari') && !(bool) strpos($userAgent, 'Chrome');
    $isiPad = (bool) strpos($userAgent,'iPad');
    $isiPhone = (bool) strpos($userAgent,'iPhone');
    $isAndroid = (bool) strpos($userAgent,'Android');

	$response = apiGetDetailsVideo($contentId);
	$arrayjson   = json_decode($response);

    $streamer = $arrayjson->streamingUrl->streamer;
	$url = $arrayjson->url;

    if ($isiPad  || $isiPhone || $isApple) {
        $configFile = "'file': '" .  $streamer . "',";
    } else if ($isAndroid) {
        $configFile = "file: '" . $url . "',";
    } else {
        $url = lastURLComponent($url);
        $configFile = "'flashplayer':'" . $dirJwPlayer . "','file': '" . $url . "','streamer':'" . $streamer . "',";
    }
    return $configFile;
}

?>