<?php
// NS: OBSOLETE: TO BE REMOVED
function wimtvpro_programming_embedded($progId) {
    $basePath = cms_getWimtvApiUrl();
    $height = get_option("wp_heightPreview") + 100;
    $width = get_option("wp_widthPreview");

    $skinData = wimtvpro_get_skin_data();
    $skinStyle = "";
    $skinLogo = "";
    if ($skinData['styleUrl'] != "") {
        $skinStyle = $skinData["styleUrl"];
    }

    if ($skinData['logoUrl'] != "") {
        $skinLogo = $skinData['logoUrl'];
    }


    $parameters = "";
    $parameters.="width=" . $width;
    $parameters.="&height=" . $height;
    $parameters.="&insecureMode=on";
    $parameters.="&skin=" . $skinStyle;
    $parameters.="&logo=" . $skinLogo;
    $iframe = apiProgrammingPlayer($progId, $parameters);
    


//    $iframeUrl = $basePath . 'programming/' . $progId . '/embedded?width=' . $width . '&height=' . $height . '&insecureMode=on&autostart=true&skin=' . $skinStyle . '&logo=' . $skinLogo;
//    $iframe = '
//        <div class="wrapperiframe" style="width:' . $width . 'px">
//        <div class="h_iframe">
//            <iframe src="' . $iframeUrl . '" frameborder="0" allowfullscreen style="overflow:hidden;" style="height:"' . $height . 'px;width:2px"></iframe>
//        </div>
//        </div>';
    return $iframe;
}

?>