<?php
  global $user;
  $parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
  $url_include = $parse_uri[0] . 'wp-load.php';

  if(@file_get_contents($url_include)){
	require_once($url_include);
  }

  $url_video = get_option("wp_basePathWimtv") . get_option("wp_urlVideosDetailWimtv");
  $credential = get_option("wp_userwimtv") . ":" . get_option("wp_passwimtv");

  $output = "";
  $urlEmbedded = get_option("wp_urlEmbeddedPlayerWimtv");
  $replaceContent = get_option("wp_replaceContentWimtv");
  $code = $_GET['c'];

  if (strlen($code)>0) {

    $contentItem = $_GET['c'];
    $streamItem = $_GET['s'];
    $jSonST =wimtvpro_detail_showtime(true, $streamItem);
	
    $arrayjSonST = json_decode($jSonST);
    $arrayST["showtimeIdentifier"] = $arrayjSonST->{"showtimeIdentifier"};
    $arrayST["title"] = $arrayjSonST->{"title"};
    $arrayST["duration"] = $arrayjSonST->{"duration"};
    $arrayST["categories"] = $arrayjSonST->{"categories"};
    $arrayST["description"] = $arrayjSonST->{"description"};
    $arrayST["thumbnailUrl"] = $arrayjSonST->{"thumbnailUrl"};
    $arrayST["contentId"] = $arrayjSonST->{"contentId"};
    $arrayST["url"] = $arrayjSonST->{"url"};
    $ch = curl_init();
    if (get_option('wp_nameSkin')!="") {
        $uploads_info = wp_upload_dir();
        $directory =  $uploads_info["baseurl"] .  "/skinWim/" . get_option('wp_nameSkin') . "/";
	    $nomeFilexml  = wimtvpro_searchFile($uploads_info["basedir"] .  "/skinWim/" . get_option('wp_nameSkin'),"xml");
	
      $skin =  "&skin=" . $directory  . "/" . $nomeFilexml;
    }
    else
      $skin = "";

    $height = get_option("wp_heightPreview") +150;
	$width = get_option("wp_widthPreview") +280;
	$widthP = get_option("wp_widthPreview") +250;

    $parametersGet = "get=1&width=" . get_option("wp_widthPreview") . "&height=" . get_option("wp_heightPreview") . $skin;
	$response = apiGetPlayerShowtime($arrayST["contentId"],$parametersGet);
	$output .= $response;

    
    $output .= "<h3>" . $arrayST["title"] . "</h3>";

    $output .= "<p>[" . $arrayST["duration"] . "]" . $arrayST["description"] . "</p>";
    if (count($arrayST["categories"])>0){
      $output .= "<br/>" . __("Categories","wimtvpro") . "<br/>";
      foreach ($arrayST["categories"] as $key => $value) {
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
    

    wp_reset_query();
    //echo "<p class='icon_downloadVideo' id='" . $arrayST["contentId"] . "'>Download</p>";   
    echo $output;
 }   
?>

