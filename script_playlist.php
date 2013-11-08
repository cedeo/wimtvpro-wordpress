<?php
  global $user,$wpdb;
  include("../../../wp-blog-header.php");
  $table_name = $wpdb->prefix . 'wimtvpro_playlist';


  if (isset($_GET['namefunction']))
    $function= $_GET["namefunction"];

  if (isset($_GET['namePlayList']))
    $name= $_GET["namePlayList"];
  
  if (isset($_GET['idPlayList']))
    $idPlayList = $_GET["idPlayList"];
    
    if (isset($_GET['id']))
    $id = $_GET["id"];


  switch ($function) {
  
    case "AddVideoToPlaylist":
    	$listVideo = "";
    	$playlist = $wpdb->get_results("SELECT listVideo,name FROM {$table_name} WHERE id='" . $idPlayList . "'");
		foreach ($playlist as $record) {
			$listVideo = $record->listVideo;
			$name = $record->name;
		}
		
		//Check if this file exist

		if ( strpos($listVideo,trim($id))>-1) {
			echo "This video exist into " . $name . " playlist.";
	        die ();
		
		} else {
		
	    	// UPDATE into DB (campo listVideo)
	    	if ($listVideo=="")
	    		$listVideo = $id;
	    	else
	    		$listVideo = $listVideo . "," . $id;
	    	$sql = "UPDATE " . $table_name  . " SET listVideo='" . $listVideo . "' WHERE id='" . $idPlayList . "'";
	        $wpdb->query($sql);
	
	    	
	        die ();
		}
		
    break;
  
    case "createPlaylist":
      $uploads_info = wp_upload_dir();
      $directory = $uploads_info["basedir"] .  "/playlistWim";
		   if (!is_dir($directory)) {
			  $directory = mkdir($uploads_info["basedir"] . "/playlistWim");
			}
      $wpdb->insert( $table_name, 
	  array (
	  				'uid' => get_option("wp_userwimtv"),
	            	'listVideo' => '',
	            	'name' =>  $name,
	            	)
	           	);
	           	
      die();
    
    break;
    
    case "modTitlePlaylist":

	  $sql = "UPDATE " . $table_name  . " SET name='" . $name . "' WHERE id='" . $idPlayList . "'";
      $wpdb->query($sql);
      die();
    
    break;

	case "removePlaylist":
	  
	  $uploads_info = wp_upload_dir();
	  $sql = "DELETE FROM " . $table_name  . " WHERE id='" . $idPlayList . "'";
      $wpdb->query($sql);
      die();
    
    break;

    
    default:
      echo "Non entro";
      die();
  }
    
?>