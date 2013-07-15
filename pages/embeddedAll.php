<?php
  global $user;
  include("../../../../wp-blog-header.php");
  $table_name = $wpdb->prefix . 'wimtvpro_video';
  $contentItem = $_GET['c'];
  $directory = $uploads_info["baseurl"] .  "/skinWim";
  if (strlen($contentItem)>0) {

    $arrayPlay = $wpdb->get_results("SELECT * FROM {$table_name} WHERE contentidentifier='" . $contentItem . "'");

	echo "<div style='text-align:center;'>";
	echo "<div id='container'></div>";
	
	$dimensions = "width: '" . get_option("wp_widthPreview") . "', height: '" . get_option("wp_heightPreview") . "',";
	
	$urlPlay = explode("$$", $arrayPlay[0]->urlPlay);
	
	if (!isset($arrayPlay[0]->urlThumbs)) $thumbs[1] = "";
	else $thumbs = explode ('"',$arrayPlay[0]->urlThumbs);
	$thumbs = str_replace('\\','',$thumbs);
	$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
	
	$configFile  = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'],$contentItem,$arrayPlay,$dirJwPlayer);
	
	echo "<script type='text/javascript'>jwplayer('container').setup({";
    if (get_option('wp_nameSkin')!="") {
     $uploads_info = wp_upload_dir();
        $directory =  $uploads_info["baseurl"] .  "/skinWim";

      $skin = "'skin':'" . $directory  . "/" . get_option('wp_nameSkin') . ".zip',";
    }
    
    
    
 	echo $skin . $dimensions . $configFile . " image: '" . $thumbs[1] . "',
 	});</script>";
 
    $output .= "<h3>" . $arrayPlay[0]->title . " (Preview)</h3>";
    $output .= "<p>Duration: <b>" . $arrayPlay[0]->duration . "</b>";
    if (count($arrayPlay[0]->categories)>0){
      $output .= "<br/>Categories<br/>";
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
    echo $output . "</div>";
 }   

?>