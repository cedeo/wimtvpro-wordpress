<?php

// EMBEDDEDALL.PHP
// NS: THIS FILE CONCERN WIMBOX e "WIMVOD PAYPERVIEW" PLAY

global $user;
include("../../../../wp-load.php");

//var_dump(get_option('wp_nameSkin'));die;

$contentItem = $_GET['c'];
$directory = isset($uploads_info) ? $uploads_info["baseurl"] . "/skinWim" : "";
$streamItem = isset($_GET['s']) ? $_GET['s'] : "";
if (strlen($contentItem) > 0) {

    $arrayPlay = dbGetVideo($contentItem);
    $widthDiv = get_option("wp_widthPreview") + 5; // + 280;
    $heightDiv = get_option("wp_heightPreview") + 150;
    $output = "<div class='responsiveVideo' style='width: " . $widthDiv . "px; height: " . $heightDiv . "px;'>";
    $output .= "<div id='container' style='margin-left:auto; margin-right:auto;'> </div>";

    if ($arrayPlay[0]->urlPlay != "") {
        $output .= configurePlayerJS($contentItem);

        $output .= "<h3>" . $arrayPlay[0]->title . " (Preview)</h3>";
        $output .= "[<b>" . $arrayPlay[0]->duration . "</b>]";
        if (count($arrayPlay[0]->categories) > 0) {
            $output .= "<p>" . __("Categories", "wimtvpro") . "<br/>";
            foreach ($arrayPlay[0]->categories as $key => $value) {
                $valuescCatST = "<i>" . $value->categoryName . ":</i> ";
                $output .= $valuescCatST;
                foreach ($value->subCategories as $key => $value) {
                    $output .= $value->categoryName . ", ";
                }
                $output = substr($output, 0, -2);
                $output .= "<br/>";
            }


            $output .= "</p>";
        }
        if (trim($streamItem) != "") {
            //Video is PAYPERVIEW
            $output .= "<p><b>Video PAY PER VIEW</b></p>";
        }

        echo $output . "</div>";
    } else {

        echo __('This video has not yet been processed, wait a few minutes and try to synchronize', "wimtvpro");
    }
}

//function configurePlayer($contentItem) {
//    $player = array();
//
//    $response = apiGetDetailsVideo($contentItem);
//    $arrayjson = json_decode($response);
//
//    $player['file'] = $arrayjson->streamingUrl->file;
//    $player['streamer'] = $arrayjson->streamingUrl->streamer;
//    $player['type'] = "rtmp";
//    $player['primary'] = "flash";
//    $player['rtmp'] = "{tunnelling: false, fallback: false}";
//
//
//    $player['width'] = get_option("wp_widthPreview");
//    $player['height'] = get_option("wp_heightPreview");
//    $player['image'] = $arrayjson->thumbnailUrl;
//
//    $player['skin'] = "";
//    $player['logo'] = "";
//
//    // A SKIN HAS BEEN ADDED: OVERRIDE DEFAULT SKIN PATH
//    $skinData = wimtvpro_get_skin_data();
//    if ($skinData['styleUrl'] != "") {
//        $player['skin'] = "{name : '" . $skinData["skinName"] . "', url : '" . $skinData['styleUrl'] . "'}";
//    }
//
//    if ($skinData['logoUrl'] != "") {
//        $player['logo'] = "{file : '" . $skinData['logoUrl'] . "', hide : true}";
//    }
//
//
//
//    $playerScript = "
//            <script>jwplayer.key='2eZ9I53RjqbPVAQkIqbUFMgV2WBIyWGMCY7ScjJWMUg=';</script>
//            <script type='text/javascript'>jwplayer('container').setup({";
//
//    foreach ($player as $key => $value) {
//        if ($value != "") {
//            if ($key != "rtmp" && $key != "skin" && $key != "logo") {
//                $value = "'" . $value . "'";
//            }
//            $playerScript.=$key . ": " . $value . ",";
//        }
//    }
//
//    $playerScript .= "});</script>";
//    return $playerScript;
//}

?>
