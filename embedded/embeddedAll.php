<?php
  global $user;
  include("../../../../wp-load.php");
  $contentItem = $_GET['c'];
  $directory = isset($uploads_info) ? $uploads_info["baseurl"] .  "/skinWim" : "";
  $streamItem = isset($_GET['s']) ? $_GET['s'] : "";

  if (strlen($contentItem)>0) {

    $arrayPlay = dbGetVideo($contentItem);
    $heightDiv = get_option("wp_heightPreview") +150;
	$widthDiv = get_option("wp_widthPreview") +280;
	echo "<div class='responsiveVideo'>";
	echo "<div id='container'> </div>";
	
	if ($arrayPlay[0]->urlPlay!=""){
	
		$dimensions = "width: '" . get_option("wp_widthPreview") . "', height: '" . get_option("wp_heightPreview") . "',";
		
		$urlPlay = explode("$$", $arrayPlay[0]->urlPlay);
		
		if (!isset($arrayPlay[0]->urlThumbs)) $thumbs[1] = "";
		else $thumbs = explode ('"',$arrayPlay[0]->urlThumbs);
		$thumbs = str_replace('\\','',$thumbs);
		$dirJwPlayer = plugin_dir_url(dirname(__FILE__)) . "script/jwplayer/player.swf";
		$configFile  = wimtvpro_viever_jwplayer($_SERVER['HTTP_USER_AGENT'],$contentItem,$dirJwPlayer);
		
		//Ricerca NomeFilexml
		$uploads_info = wp_upload_dir();
		$nomeFilexml  = wimtvpro_searchFile($uploads_info["basedir"] .  "/skinWim/" . get_option('wp_nameSkin'),"xml");
		echo "<script type='text/javascript'>jwplayer('container').setup({";
		$skin = "'skin':'" .  plugin_dir_url(dirname(__FILE__))  . "/script/skinDefault/wimtv/wimtv.xml',";
		if (get_option('wp_nameSkin')!="") {
			$directory =  $uploads_info["baseurl"] .  "/skinWim"; 
			$skin = "'skin':'" . $directory  . "/" . get_option('wp_nameSkin') . "/" . $nomeFilexml . "',";
		}
		
		
		
		echo $skin . $dimensions . $configFile . " image: '" . $thumbs[1] . "',
		});</script>";
	 
		$output .= "<h3>" . $arrayPlay[0]->title . " (Preview)</h3>";
		$output .= "[<b>" . $arrayPlay[0]->duration . "</b>]";
		if (count($arrayPlay[0]->categories)>0){
		  $output .= "<p>" . __("Categories","wimtvpro") . "<br/>";
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
		if (trim($streamItem)!="") {
			//Video is PAYPERVIEW
			$output .= "<p><b>Video PAY PER VIEW</b></p>";
		}

		echo $output . "</div>";
	
	} else {
		
		echo __('This video has not yet been processed, wait a few minutes and try to synchronize',"wimtvpro");
	}
	
 }   

?>
