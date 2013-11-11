<?php
  global $user,$wpdb;
  include("../../../wp-blog-header.php");

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


    	$playlist = dbExtractSpecificPlayist($idPlayList);

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

            dbUpdatePlaylist($idPlayList, $listVideo);
	    	
	        die ();
		}
		
    break;
  
    case "createPlaylist":
      $uploads_info = wp_upload_dir();
      $directory = $uploads_info["basedir"] .  "/playlistWim";
		   if (!is_dir($directory)) {
			  $directory = mkdir($uploads_info["basedir"] . "/playlistWim");
			}

        dbInsertPlayist(get_option('wp_userwimtv'), $name);
	           	
      die();
    
    break;
    
    case "modTitlePlaylist":

      dbUpdatePlaylist($idPlayList, $name);

      die();
    
    break;

	case "removePlaylist":
	  
	  $uploads_info = wp_upload_dir();

      dbDeletePlayist($idPlayList);

      die();
    
    break;

    
    default:
      echo "Non entro";
      die();
  }
    
?>