<?php
  global $user;
  include("../../../../wp-blog-header.php");

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
        $directory =  $uploads_info["baseurl"] .  "/skinWim";

      $skin = "&skin=" . $directory  . "/" . get_option('wp_nameSkin') . ".zip";
    }
    else
      $skin = "";

    
    $height = get_option("wp_heightPreview") +150;
	$width = get_option("wp_widthPreview") +280;
	$widthP = get_option("wp_widthPreview") +250;
    

    $url = get_option("wp_basePathWimtv") . get_option("wp_urlVideosWimtv") . "/" . $arrayST["contentId"] . '/embeddedPlayers';
    $url .= "?get=1&width=" . get_option("wp_widthPreview") . "&height=" . get_option("wp_heightPreview") . $skin;
    //echo $url;
    curl_setopt($ch, CURLOPT_URL,  $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language: en-us,en;'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $credential);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch);

 	echo "<div style='text-align:center;height:" . $height . "px;width:" . $width . "px;'>";
	$output .= $response;

    
    $output .= "<h3>" . $arrayST["title"] . "</h3>";

    $output .= "<p>" . $arrayST["description"] . "</p>";
    $output .= "<p>Duration: <b>" . $arrayST["duration"] . "</b>";
    if (count($arrayST["categories"])>0){
      $output .= "<br/>Categories<br/>";
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
    

    
    //echo "<p class='icon_downloadVideo' id='" . $arrayST["contentId"] . "'>Download</p>";   
    echo $output . "</div>";
 }   
?>

<script>

	jQuery('.icon_downloadVideo').click(function() {
	
		var uri = '<?php echo get_option("wp_basePathWimtv") . "videos/";?>' + jQuery(this).attr("id") + '/download';
		jQuery('body').append('<iframe src="' + uri + '" style="display:none;" />');
	
	});


</script>