<?php
// EMBEDDED.PHP
global $user;
$parse_uri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$url_include = $parse_uri[0] . 'wp-load.php';

if (@file_get_contents($url_include)) {
    require_once($url_include);
}

$url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
$credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");

$urlEmbedded = get_option("wp_urlEmbeddedPlayerWimtv");
$replaceContent = get_option("wp_replaceContentWimtv");
$code = $_GET['c'];

if (strlen($code) > 0) {

    $contentItem = $_GET['c'];
    $streamItem = $_GET['s'];
    $showtime = json_decode(wimtvpro_detail_showtime(true, $streamItem));
    if (get_option('wp_nameSkin') != "") {
        $uploads_info = wp_upload_dir();
//        $directory = $uploads_info["baseurl"] . "/skinWim/" . get_option('wp_nameSkin') . "/";
//        $nomeFilexml = wimtvpro_searchFile($uploads_info["basedir"] . "/skinWim/" . get_option('wp_nameSkin'), "xml");
//        $skin = "&skin=" . $directory . "/" . $nomeFilexml;
        $directory = $uploads_info["baseurl"] . "/skinWim";
        $nomeFilexml = wimtvpro_searchFile($uploads_info["basedir"] . "/skinWim/" . get_option('wp_nameSkin') . "/wimtv/", "xml");
        $skin = "&skin=" . $directory . "/" . get_option('wp_nameSkin') . "/wimtv/" . $nomeFilexml;
    } else {
        $skin = "";
    }
    
    $height = get_option("wp_heightPreview");
    $width = get_option("wp_widthPreview");

    $parametersGet = "get=1&width=" . $width . "&height=" . $height . $skin;
    $response = apiGetPlayerShowtime($showtime->{"contentId"}, $parametersGet);
    ?>

    <div style='text-align:center;'>
        <?php echo $response ?>
        <h3><?php echo $showtime->{"title"} ?></h3>
        <p>[<?php echo $showtime->{"duration"} ?>] <?php echo $showtime->{"description"} ?></p>
        <?php if (count($showtime->{"categories"})) { ?>
            <br/><?php echo _("Categories", "wimtvpro") ?><br/>
            <?php foreach ($showtime->{"categories"} as $index => $category) { ?>
                <i><?php echo $category->categoryName ?>:</i>
                <?php
                foreach ($category->subCategories as $key => $subCategory) {
                    echo $subCategory->categoryName . ", ";
                }
                ?>
                <br/>
            <?php } ?>
        </p>
        </div>
        <?php
    }
}
?>

